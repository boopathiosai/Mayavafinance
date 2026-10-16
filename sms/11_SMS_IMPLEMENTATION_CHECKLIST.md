# 11 – SMS Implementation Checklist

Legend: ☑ done · ☐ not started · **BLOCKED** = waiting on client item in [`10_SMS_CLIENT_DEPENDENCIES.md`](10_SMS_CLIENT_DEPENDENCIES.md).
Phase 1 (UI, mock) is complete. Everything else is **not started**; backend work starts only after client approval.

## 1. UI (Phase 1 – mock)
Depends on: none. Acceptance: AC-UI-1…9 in [`docs/SMS_MODULE_SCOPE.md`](../docs/SMS_MODULE_SCOPE.md) §7.
- ☑ Send Reminder button, no customer modal, mock feedback
- ☑ History button disabled until send initiated
- ☑ History modal: filters, Search/Reset, Excel/PDF mock controls, table, pagination, close behaviours
- ☑ From/To Date and Success Rate absent
- ☑ Responsive check (1200px, 380px) in headless browser
- ☐ Manual check inside the full app (PHP + MySQL + login) – needs a running environment
- ☐ Client UI sign-off – **BLOCKED** by D12 (placement)

## 2. Database
Depends on: D8, D9, D11. Acceptance: reviewed additive migration passes on a staging copy; rollback tested; History filter queries use indexes; no existing table altered.
- ☐ Export and verify live schema – **BLOCKED** (D8)
- ☐ Finalise `mf_sms_message` (and optional attempt/template tables) – **BLOCKED** (D8, D9, D11)
- ☐ Migration + rollback scripts reviewed – **BLOCKED** (D8)
- ☐ Table constants in `includes/tables.php`

## 3. Backend
Depends on: Database stage, D5, D7, D10. Acceptance: History reads real rows with branch isolation; Send Reminder creates exactly one record per intended event (idempotent); existing pledge/redemption SMS behaviour unchanged unless approved; save never fails because SMS fails.
- ☐ Reminder selection rules – **BLOCKED** (D10)
- ☐ Send endpoint with validation, idempotency – **BLOCKED** (D7, D8)
- ☐ History query endpoint (4 filters, pagination) – **BLOCKED** (D8)
- ☐ Excel / PDF export (server side, existing `excel.class.php` / `pdf.class.php`) – **BLOCKED** (D13)
- ☐ Asynchronous/scheduled processing – **BLOCKED** (D5)

## 4. Provider integration
Depends on: D1, D2, D3, D4, D6, D11. Acceptance: sandbox send succeeds with approved template; response and errors recorded; timeout set; TLS verification on; staging cannot reach real customers.
- ☐ Vendor API contract and sandbox account – **BLOCKED** (D1)
- ☐ Approved templates and sender – **BLOCKED** (D2, D3, D4)
- ☐ Result/status handling and optional delivery callback – **BLOCKED** (D11)

## 5. Security
Depends on: D6, D9. Acceptance: no secrets in source; privilege checks server-side; inputs validated/escaped; exports permission-controlled; security review passed.
- ☐ Move credentials out of source and rotate – **BLOCKED** (D6)
- ☐ Permission checks and module/privilege entry (existing `mf_modules` model)
- ☐ Masking / retention rules applied – **BLOCKED** (D9)
- ☐ Review of committed secrets (`includes/db.php`, history files, key file) with client

## 6. Testing
Depends on: UI, Backend, D14. Acceptance: all P1 cases in `TEST_CASES.md` §8, §13, §19 pass; History/filters/pagination/export verified with real data; failure paths verified.
- ☑ Phase 1 headless UI verification (see `08` §9)
- ☐ Functional + regression on staging with sandbox number – **BLOCKED** (D1, D14)
- ☐ Duplicate-send and pledge-edit tests – **BLOCKED** (D7)
- ☐ Automated test harness – **BLOCKED** (D15)

## 7. Deployment
Depends on: all previous stages, client approval. Acceptance: staged rollout with rollback; monitoring of failures.
- ☐ Stage A: deploy UI (2 layout files) to staging, then production
- ☐ Stage C: DB migration (backup first) – **BLOCKED** (D8)
- ☐ Stage D: backend + provider pilot – **BLOCKED** (D1–D7)
- ☐ Stage E: rollout, monitoring, retention job – **BLOCKED** (D9)
