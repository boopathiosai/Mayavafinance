# 08 – SMS UI Specification (Mock Only)

Status: implemented as mock. Scope document: [`docs/SMS_MODULE_SCOPE.md`](../docs/SMS_MODULE_SCOPE.md).

## 1. Files
| File | Change |
|---|---|
| `layouts/default/elements/sms_reminder_ui.html` | **New.** Self-contained CSS + JS (+ a PHP comment header). No PHP logic, no AJAX/fetch/XHR. |
| `layouts/default/summary_reports_stock_report.html` | **+6 lines:** History and Send Reminder buttons, a feedback `<span>`, and one `include` of the partial, inside the existing title-bar cell (next to PDF / XLS / Print). |

Nothing else was touched (no backend class, controller, DB, config, JS or CSS file).

## 2. Mock-only guarantee
- The feature contains **no** call to `sms::sendData`, any provider URL, `$.ajax`, `$.get/post/load`, `fetch` or `XMLHttpRequest`. Verified by code search and by a headless-browser run that recorded **zero** external network requests.
- All rows are fabricated (`MOCK-####` loan ids, "Sample Customer X", `0000000001`-style numbers).
- UI text states that no SMS was sent. Status labels are neutral (PENDING / SUBMITTED / FAILED / SKIPPED) and shown beside a **MOCK DATA** banner; the UI never says "Delivered".
- State is held in JS memory only and is lost on full page reload.

## 3. Stock Summary integration
Placement: Summary Reports → **Stock Report** tab (assumption A1 in the scope doc – to be confirmed), in the report title bar cell with the existing PDF / XLS / Print buttons. Buttons use existing `greybtn` / `greybtn-off` classes. The partial is re-included each time the tab is reloaded by AJAX; the script is idempotent (state on `window.MfSmsMock`, handlers re-bound with a namespaced event).

## 4. Send Reminder
- Click acts immediately. **No customer-selection modal.**
- Marks the action as *initiated*, enables History, shows inline feedback: "MOCK: reminder request in progress (simulated). No SMS is being sent."
- After ~0.8 s adds one mock row (status PENDING) to History and changes the message to "…recorded in History. No SMS was sent and no provider was contacted."
- Re-click while processing is ignored (busy guard).

## 5. History button
- Initial state: `greybtn-off`, `aria-disabled="true"`, title "Available after a reminder has been initiated"; click does nothing.
- Enabled **as soon as Send Reminder is initiated** (not on completion); stays enabled for the page session.

## 6. SMS Reminder History modal
**Design reference:** client mock-up image (purple title bar "SMS Reminder History", green Excel / red PDF buttons top-right, filter grid, purple table headers, pager footer). jQuery UI dialog (existing project mechanism), modal, `dialogClass: sms-hist-dlg`, width `min(1280px, window - 20px)`.
| Area | Spec |
|---|---|
| Banner | Small "MOCK DATA – generated sample records…" strip (kept so mock rows are never mistaken for real delivery) |
| Export | **Excel** (green) and **PDF** (red) buttons, top-right. Mock: show "MOCK: Excel/PDF export is a UI placeholder. No file was generated and nothing was sent." |
| Filters | Row 1: **Loan ID :**, **Customer Name :**, global search box "Search Loan ID, Customer Name or Mobile". Row 2: **Status :** (All / Success / Failed / Pending), **Mobile No :**, **Search** (purple) and **Reset** (grey). All filters combine with AND; Enter key searches. |
| Columns | S.No · Loan ID · Customer Name · Mobile Number · **Template ID** · Status (badge: Success green / Failed red / Pending amber) · Remarks (`-` when none) · **Sent Date** (`dd-mm-yyyy hh:mm AM/PM`) |
| Body | Scrollable (max-height 380px) with sticky purple-tinted header; "No records found" when empty |
| Pagination | 30 rows/page; footer left "Showing 1 - 30 of N records", centre pager « ‹ 1 2 3 4 … last › », right "Page X / Y"; filter change resets to page 1 |
| Close | Close button (bottom-right), title-bar ×, Esc, overlay click. Dialog DOM destroyed on close; reopening resets filters. |
| Escaping | All cell text HTML-escaped |
| Mock data | 450 generated rows (`MOCK-POS29xxx`, "Sample Customer A…", `0000xxxxxx` numbers, template id `1000000000000000001`) plus one row per mock Send Reminder click. Remarks samples copied from the design: Invalid Mobile Number, API Error (101), DND Number. |

Design note: the mock-up labels statuses "Success/Failed". In this release they are **sample labels on generated data only**; the real lifecycle (and whether "Success" may mean *delivered* or only *accepted by provider*) is client decision D11. The earlier "Updated" column was dropped to follow the design (single "Sent Date"); Template ID was added per the design (value is a fake placeholder).

Header buttons: **Send Reminder** (paper-plane icon) and **History** (clock icon) are purple per the mock-up; History is greyed until Send Reminder is initiated.

## 7. Removed elements
**From Date, To Date and Success Rate are not present** anywhere (controls, labels, totals, exports). Do not reintroduce in Phase 2 without a new approval.

## 8. Responsive behaviour and styling
- Reuses `greybtn`, `greybtn-off`, theme dialog styling from `css/jquery-ui.custom.css`, and the table look of `.list-tbl` headings.
- Filters use flex-wrap; at ≤640px each filter takes a full row; table scrolls horizontally inside the modal (`overflow-x:auto`), the page itself does not.
- Verified in headless Chromium at 1200px and 380px widths.

## 9. Verification performed (this delivery)
Headless Chromium, repo's jQuery 1.7.1 + jQuery UI 1.8.16 (the page's CDN versions were unreachable from the sandbox):
History disabled initially ✔ · click on disabled History opens nothing ✔ · Send Reminder enables History ✔ · 5 rows on page 1, 9 total after one mock send ✔ · status filter FAILED → 2 ✔ · Reset + page 2 ✔ · Excel placeholder message ✔ · no-match → "No records found" ✔ · Esc closes ✔ · no page-level horizontal scroll at 380px ✔ · no script errors ✔ · 0 external requests ✔.
**Not verified:** inside the full application (requires PHP + MySQL + login + real data); with the production CDN jQuery 3.6/UI 1.12 combination; Safari/Firefox.

## 10. Known limitations / risks
- The code uses `.on/.off` (jQuery ≥ 1.7). The header also loads jQuery 1.6.2 first and then CDN jQuery 3.6; if the CDN is blocked in production, the dialog would already fail elsewhere in the app.
- Pages containing the partial twice (not currently the case) would duplicate element ids.
- "Stock Summary" location is an assumption (see scope A1).

## 11. Phase 2 hand-over notes (not implemented)
Replace the mock array with server data, keep the same column set; export must be generated server-side; status values must come from the approved lifecycle (see `sms/09`); History enablement rule may need to depend on persisted data rather than page state.
