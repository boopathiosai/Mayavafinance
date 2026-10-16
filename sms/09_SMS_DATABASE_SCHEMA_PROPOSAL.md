# 09 – SMS Database Schema Proposal

**PROPOSAL ONLY. NOT EXECUTED. NOT APPROVED.** No table has been created, no SQL run, no migration prepared. The live schema is **not verified**: the repository contains no schema/migration/dump file.

Evidence reviewed: `SMS_CONCEPT_ANALYSIS.md` §2.4 / §4.3, `includes/tables.php`, and source usage. (`sms/03_SMS_DATABASE_ANALYSIS.md` does not exist in this repository, so it could not be reviewed.)

## 1. Confirmed existing (from source references only)
| Object | Evidence | What is known | Unknown |
|---|---|---|---|
| `mf_sms_support` (`SMS_SUPPORT`) | `includes/tables.php:53`; queried in `product_pledge.php`, `redemption.php` | Has columns `name`, `tamil_name` (queried by `name`) | Full column list, keys, row count, whether still needed (current live messages are literals) |
| `mf_customers.contact_no` | `customer.class.php`, SMS callers | Mobile source; min length 10 enforced at entry | Type, format, indexing |
| `mf_pledge` (`loan_id`, `customer_id`, `branch_id`, `sub_branch_id`, `scheme_id`) | `productpledge.class.php`, `product_pledge.php` | Loan identity for reminders | Exact types/indexes |
| `mf_redemption` | `redemption.php` | Redemption event | — |
| `mf_branch`, `mf_sub_branch`, `mf_users`, `mf_settings` | `tables.php` | Branch isolation, actor, config | — |
**No existing table stores SMS messages, attempts, templates, delivery status or consent.**

## 2. Proposed tables (names illustrative, `mf_` prefix per convention)
### 2.1 `mf_sms_message` – one row per reminder/message
| Column | Purpose |
|---|---|
| id (PK) | |
| loan_pk (FK → `mf_pledge.id`), customer_id (FK), branch_id, sub_branch_id | relationships / branch isolation |
| loan_id_text | loan number as displayed (supports History "Loan ID" filter) |
| customer_name_snapshot, mobile_number | snapshot at send time (History filters) – see privacy |
| event_type | e.g. REMINDER (values to be approved) |
| template_id (nullable FK) / dlt_template_id | wording + DLT id used |
| message_text | rendered text (or reference – privacy decision) |
| status | lifecycle value (§3) |
| remarks | human-readable reason/detail (History "Remarks") |
| provider_message_ref | provider reference, nullable |
| idempotency_key (UNIQUE) | prevents duplicates (§5) |
| created_at, updated_at, created_by | History timestamps and actor |
### 2.2 `mf_sms_attempt` (optional) – one row per provider attempt
id, message_id (FK), attempt_no, requested_at, provider_http_code, provider_response (trimmed), outcome, error_detail. Needed only if retries/audit of raw responses are required.
### 2.3 `mf_sms_template` (optional) – template registry
id, event_type, language, scheme_id (nullable), body_with_placeholders, dlt_template_id, sender_id, active, created_at/updated_at. Needed only if wording must be editable; otherwise keep literals in code. Blocked by client wording/DLT decisions.

## 3. Status lifecycle (placeholder – client to approve)
`PENDING → SUBMITTED → DELIVERED | FAILED`; `PENDING → SKIPPED` (rule says not due / invalid number); `FAILED → PENDING` (retry, if retries approved). `DELIVERED` is only valid if the provider supplies delivery reports (dependency). The Phase 1 mock uses PENDING/SUBMITTED/FAILED/SKIPPED and never shows DELIVERED.

## 4. Relationships
`mf_sms_message` N:1 `mf_pledge`, N:1 `mf_customers`, N:1 `mf_branch`; 1:N `mf_sms_attempt`; N:1 `mf_sms_template`. Prefer storing IDs plus minimal snapshots; confirm FK vs soft references (existing tables define constants only; foreign-key usage in the live DB is unknown).

## 5. Indexing and idempotency
- Indexes supporting History: (branch_id, created_at), loan_id_text, mobile_number, status, customer_name_snapshot (prefix/FULLTEXT only if justified by volume).
- Idempotency: UNIQUE `idempotency_key` built from event + loan + business date/version so a double-click, retry or pledge **edit** cannot create a second message (rule for pledge-edit resend is a client decision).
- Confirm engine/charset (code sets `utf8mb4`; Tamil text requires it).

## 6. Privacy and retention (decisions required)
- Mobile numbers and message text are personal data; consider masking in UI/exports and limiting export permission.
- Retention period (e.g. N months), purge job, and whether `message_text` is stored or reconstructed are **client decisions**.
- Consent/opt-out record: not present anywhere today; whether needed is a client/legal decision (a `mf_sms_consent` table is **not** proposed until confirmed).

## 7. Migration considerations
1. Export the live schema first and verify §1 assumptions.
2. Additive-only migration (new tables), reviewed script, tested on a staging copy, with a documented rollback (drop new tables).
3. No change to existing tables; no data backfill (history starts empty).
4. Add table constants to `includes/tables.php` only in the implementation phase.
5. Backup before production run; run outside business hours.

## 8. Illustrative SQL – **NOT EXECUTED – NOT APPROVED – column types unverified**
```sql
-- NOT EXECUTED. NOT APPROVED. Illustration of §2.1 only.
CREATE TABLE mf_sms_message (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  loan_pk INT NOT NULL,
  customer_id INT NOT NULL,
  branch_id INT NOT NULL,
  loan_id_text VARCHAR(50) NOT NULL,
  customer_name_snapshot VARCHAR(150) NOT NULL,
  mobile_number VARCHAR(15) NOT NULL,
  event_type VARCHAR(30) NOT NULL,
  status VARCHAR(20) NOT NULL,
  remarks VARCHAR(255) NULL,
  provider_message_ref VARCHAR(100) NULL,
  idempotency_key VARCHAR(100) NOT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  created_by INT NULL,
  UNIQUE KEY uq_idem (idempotency_key),
  KEY ix_branch_created (branch_id, created_at),
  KEY ix_loan (loan_id_text),
  KEY ix_mobile (mobile_number),
  KEY ix_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```
