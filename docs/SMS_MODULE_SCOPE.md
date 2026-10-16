# SMS Module – Scope

Status: **DRAFT for client approval.** Phase 1 (UI, mock only) is implemented; Phase 2+ (backend) is **not started**.
Evidence base: `SMS_CONCEPT_ANALYSIS.md` (source-derived), `TEST_CASES.md`, and the code in this repository. The live database schema is **not verified** (no schema file exists in the repo).
Related: [UI spec](../sms/08_SMS_UI_SPECIFICATION.md) · [DB proposal](../sms/09_SMS_DATABASE_SCHEMA_PROPOSAL.md) · [Client dependencies](../sms/10_SMS_CLIENT_DEPENDENCIES.md) · [Checklist](../sms/11_SMS_IMPLEMENTATION_CHECKLIST.md)

> Note: the brief referred to earlier files `sms/01`–`sms/07` (including `sms/03_SMS_DATABASE_ANALYSIS.md`). They do not exist in this repository, so they were not used. The database evidence below comes from `SMS_CONCEPT_ANALYSIS.md` §2.4 and source inspection only.

## 1. Objective
Give staff a **Send Reminder** action and an **SMS Reminder History** view on the Stock Summary screen, so that reminder SMS activity can be initiated and reviewed. This release delivers the **UI only, with mock data**; real sending and storage follow after client approval.

## 2. Business requirements (from the brief)
1. Stock Summary shows **Send Reminder** and **History** buttons.
2. Send Reminder acts directly – **no customer-selection modal**.
3. History is enabled once a send action has been initiated.
4. History modal filters: Loan ID, Customer Name, Mobile Number, Status; Search and Reset; Excel and PDF export controls.
5. History rows show message status, remarks and timestamps; paginated; closable.
6. **No** From Date, To Date or Success Rate anywhere in the feature.
7. Uses existing project styling and responsive behaviour.

Not specified (so not assumed): which loans receive reminders, reminder trigger rules, message wording. See the dependencies document.

## 3. Scope
### 3.1 Immediate – UI work (Phase 1, done, mock only)
- New partial `layouts/default/elements/sms_reminder_ui.html` (markup, CSS, JS; no backend).
- Minimal insertion of two buttons and one `include` into `layouts/default/summary_reports_stock_report.html`.
- Mock Send Reminder feedback, History modal with mock rows, filters, pagination, mock export controls.
- Documentation (this file and `sms/08`–`sms/11`).

### 3.2 Future – backend work (Phase 2+, NOT started, requires client approval)
- Database tables / migration (proposal only in `sms/09`).
- Server endpoints for send, history query and export (Excel/PDF).
- Provider integration, template handling, scheduling/queue, delivery status.
- Security hardening, logging, retention, deployment.

## 4. Exclusions (this release)
- Any real SMS call or provider contact; any change to `classes/sms.class.php`, `product_pledge.php`, `redemption.php` or other business logic.
- Any SQL, table creation, migration, or database change.
- Credential, configuration or dependency changes.
- Real Excel/PDF files, real data in History, delivery-status tracking.
- Reminder rules, resend-on-edit behaviour, consent handling.

## 5. Assumptions
1. "Stock Summary" = the **Summary Reports → Stock Report** tab (`summary_reports.php` + `summary_reports_stock_report.html`), the only page in the repo that combines stock figures with PDF/XLS/Print buttons. **To be confirmed by the client.**
2. History shows only what was initiated in the current page session until a backend exists (mock).
3. The existing front-end stack (jQuery + jQuery UI dialog, `greybtn` styles) is the convention to follow.
4. Statuses used in the mock (PENDING, SUBMITTED, FAILED, SKIPPED) are placeholders, to be replaced by the approved lifecycle.

## 6. Dependencies
See `sms/10_SMS_CLIENT_DEPENDENCIES.md`: vendor/test account, approved wording and DLT templates, processing mode and scheduler, credential storage, resend-on-edit, schema approval, consent/retention.

## 7. UI acceptance criteria (Phase 1)
- AC-UI-1 Buttons appear on the Stock Summary report title bar; History is visibly disabled until Send Reminder is clicked.
- AC-UI-2 Send Reminder opens no customer-selection popup, shows mock processing feedback, and states that no SMS was sent.
- AC-UI-3 History opens a modal with filters (Loan ID, Customer Name, Mobile, Status), Search, Reset, Excel, PDF, Close.
- AC-UI-4 Table shows S.No, Loan ID, Customer Name, Mobile, Status, Remarks, Created, Updated; paginated.
- AC-UI-5 No From Date, To Date or Success Rate control or figure exists.
- AC-UI-6 Modal closes via Close button, X, Esc and overlay click.
- AC-UI-7 Works at phone width without page-level horizontal scroll; uses existing styles.
- AC-UI-8 Mock data is labelled MOCK; no wording claims real delivery; no network request is made by the feature.
- AC-UI-9 No backend, DB, config or other source file is changed.

## 8. Database design requirements (for Phase 2; detail in `sms/09`)
Per-message record with status lifecycle; optional attempt history; optional template registry; idempotency key to avoid duplicate sends; indexes for the four history filters; privacy-aware storage of mobile numbers and message text; retention policy; reviewed, reversible migration; schema verified against the live database first.

## 9. Security requirements (Phase 2)
- Provider credentials outside source control; rotate the key currently hard-coded in `classes/sms.class.php`.
- TLS verification enabled for provider calls.
- Server-side permission checks using existing privilege functions (`is_accessible`, `can_*`); branch isolation as in other modules.
- Validation and escaping of all filter inputs; parameterised queries.
- No customer data in logs beyond what is approved; masked mobile numbers where suitable.
- Export access limited to permitted users.

## 10. Testing strategy
- Phase 1: manual UI test cases in `sms/08` (§ Verification) plus a headless-browser check performed for this delivery (desktop and 380px widths, no external requests, no script errors).
- Phase 2: unit tests for message building/number validation (needs a test harness approval), staging tests with a sandbox number, regression of pledge and redemption saves, security checks. See `TEST_CASES.md` §19 and `sms/11`.

## 11. Deployment stages
1. **Stage A – UI (mock):** deploy the two layout files; no DB/config change; verify on staging.
2. **Stage B – Approvals:** client closes the items in `sms/10`.
3. **Stage C – Database:** reviewed migration on staging, then production, after backup.
4. **Stage D – Backend and provider:** staging with sandbox account, then limited production pilot.
5. **Stage E – Full rollout and monitoring.**
Rollback for Stage A: revert the two files (the layout edit is 6 added lines).
