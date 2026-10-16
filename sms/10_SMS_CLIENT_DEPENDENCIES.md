# 10 – SMS Client Dependencies

Purpose: every decision/input needed from the client before backend work. Current status of all items: **OPEN** (nothing has been confirmed in this repo or conversation). Owners are proposed roles – confirm names with the client.
Facts already in the code are noted so they need not be re-asked, but still need client confirmation of intent.

| # | Item | Current status | Proposed owner | Depends on | Blocks |
|---|---|---|---|---|---|
| D1 | **SMS vendor and test/sandbox account** – code currently uses `sms.textspeed.in` (gateway changed ≥3 times historically). Is it permanent? Provide sandbox/test credentials and API doc (status codes, delivery reports, rate limits, balance). | OPEN | Client business owner / vendor contact | — | All provider integration, staging tests, delivery-status design |
| D2 | **Approved message wording and language** (Tamil/English) for the reminder; the existing pledge/redemption texts are Tamil literals. | OPEN | Client business owner | D1 (vendor limits) | Template design, message build, DLT registration |
| D3 | **DLT template IDs and sender details** – current sender `KFLOAN`, three template IDs are hard-coded for pledge/redemption; a **reminder** template is not known to exist. | OPEN | Client compliance / DLT account holder | D2 | Provider integration, any real send |
| D4 | **Dynamic message fields and character limits** (loan id, amount, due date, branch, etc.; Unicode segment limits). | OPEN | Client business owner + vendor | D2, D3 | Template registry, message build, UI preview |
| D5 | **Processing mode** (synchronous, queued, scheduled) and **server scheduler availability** (cron on production server; `crontab -l` appears in a committed `.bash_history`, unverified). | OPEN | Client IT / hosting admin | — | Backend architecture, send-reminder behaviour, retries |
| D6 | **Credential storage** – where provider key/sender live (currently hard-coded in source and in git history); rotation of exposed key. | OPEN | Client IT / security owner | D1 | Provider integration, security sign-off |
| D7 | **Pledge-edit resend behaviour** – should editing a pledge re-send SMS? (Existing code appears to send on every save; unverified.) | OPEN | Client business owner | — | Idempotency design, message rules, regression tests |
| D8 | **Database schema and migration approval** – export of live schema; approval of `sms/09` tables (or changes); migration window and rollback. | OPEN | Client DBA / IT + business sign-off | live schema export | Database stage, backend, History with real data |
| D9 | **Consent, retention and customer privacy** – consent/opt-out needed? retention period; mask mobile numbers; who may export. | OPEN | Client legal / compliance | — | Schema (consent, retention), export design, History data visibility |
| D10 | **Reminder rules** – which loans get reminders, when, how often, who triggers (not specified anywhere). | OPEN | Client business owner | D2 | Send Reminder backend, status lifecycle |
| D11 | **Status lifecycle and delivery reports** – approved statuses; is DELIVERED available from the vendor. | OPEN | Client + vendor | D1 | History status values, callback endpoint |
| D12 | **"Stock Summary" location** – confirm the Summary Reports → Stock Report tab is the intended screen. | OPEN | Client product owner | — | Final UI placement (Phase 1 is mock) |
| D13 | **Export format** – columns/layout of Excel and PDF History exports; branding. | OPEN | Client business owner | D8 | Export implementation |
| D14 | **Environments and test numbers** – staging that cannot reach real customers; approved test mobile numbers. | OPEN | Client IT | D1 | Staging tests, pilot |
| D15 | **Test harness approval** – permission to add automated tests (none exist). | OPEN | Client / dev lead | — | Unit tests |

## Notes
- Items D1, D2, D3, D5, D6 are hard blockers for any real send.
- Items D8, D9 are hard blockers for any database work.
- D12 does not block Phase 1 (mock UI is done) but may require moving the buttons.
