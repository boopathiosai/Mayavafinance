# SMS Concept – Analysis

Status: analysis only. No application code was changed or written.
Basis: the repository at `/home/user/Mayavafinance` (branch `claude/zealous-euler-6l8lzz`). Every claim below was read from the code. Items the code does not settle are listed in section 9 as questions.

---

## 0. Important notes up front

1. **No `CLAUDE.md` exists** anywhere in the repo. There is also no `docs/` folder, no `.sql` schema or migration file, no `composer.json`, no test suite, and no real README. The `README` file at the root is effectively empty. The only documentation is file header comments, which are largely boilerplate.
2. **The "SMS concept" is not defined anywhere in the repo.** There is no spec, ticket or TODO that describes it. I could only analyse the SMS code that exists. What "SMS concept" should add is a question for the business (section 9). Section 5 lists gaps visible in the current implementation, but these are candidates, not confirmed requirements.
3. A live-looking SMS gateway API key is **hard-coded** in `classes/sms.class.php`, and database credentials are committed in `includes/db.php`. Treat both as already exposed (section 8).

---

## 1. Overall project scope and structure

### 1.1 What the system is
**Mayava Finance** is a gold/silver pledge-loan (pawn-broking) management web application. Evidence:
- Core flows: pledge creation (`product_pledge.php`), interest payment (`pay_interest.php`), renewal (`renewal.php`), partial payment (`partial_amount_payment.php`), excess payment (`pay_excess_amount.php`), redemption (`redemption.php`) and auction (`auction_management.php`, `auction_notice.php`, `auction_final_notice.php`, `pre_auction_notice.php`).
- Supporting areas: customers, branches and sub-branches, schemes, products and categories, users and privileges, leaves, expenses (added 18/08/2025), cash-flow and bank transactions, and many reports (daily, stock, ledger, income, closing, category-wise, branch summary).
- Customer notification letters (`notification_letter.php`, `layouts/default/elements/reminder_notification_letter*.html`).
- An internal user-to-user message system (`message.php`, `inbox.php`, `sent_items.php`, `view_message.php`, `process_message.php`, `classes/message.class.php`). This is **not** SMS.
- Tamil-language content throughout: Tamil fonts (`css/Fonts/ETLAKSHM*`, `classes/font/madurai*`), `js/tamil.js`, `test_tamil.php`, and Tamil SMS text.

### 1.2 Technology and architecture
| Aspect | Finding (source) |
|---|---|
| Language | Plain procedural PHP with static-method "class" files. No framework. |
| Entry pattern | One PHP file per module at the repo root (for example `redemption.php`). It defines `ROOT_PATH`, includes `includes/config.php`, checks `$app->is_logged_in()`, then runs a `switch($type)` over `add/edit/save/update/delete/search/paging/...`. |
| Autoloading | `includes/autoload.php` loads `classes/<lowercased classname>.class.php`. Hence `sms::sendData` resolves to `classes/sms.class.php`. |
| Data layer | A homegrown `$app` object (`classes/application.class.php`, 367 lines). Calls look like `$app->access_table = X; $app->find(array(cols), array("conditions"=>...)); $app->add(); $app->save()`. DB wrapper in `classes/db.class.php` uses `mysqli` and falls back to `mysql_connect`. It sets charset `utf8mb4`. |
| Table names | Constants in `includes/tables.php`, all prefixed `mf_`. |
| Views | HTML templates in `layouts/default/**` (forms, elements, mails, common header, footer and left panel). |
| Frontend JS | jQuery 1.4–1.8 (old), jQuery UI, thickbox, plus custom `js/common.js`, `js/function_common_modules.js` and `js/product_pledge.js`. Forms save through AJAX and the server echoes a JSON-like string (`page/status/disp/content/isAlert/args`). |
| Output libs | `classes/pdf.class.php` and `classes/excel.class.php`. `classes/phpmailer.class.php` and `includes/mail_functions.php` handle email. |
| Config | `includes/config.php`, `constants.php`, `defaults.php`, `settings/settings.php`, `organization_details.php`. |
| Infra hints | `.gitignore` contains only `amazon-cloudwatch-agent.deb` (AWS host). `.bash_history` shows `crontab -l`, so cron may be used on the server, but no cron scripts are in the repo. |

### 1.3 Repository hygiene (relevant to how SMS work must be done)
- 1,181 tracked files. A very large share are **dated backup copies kept side by side** (for example `application.class_04_SEP_2018.php`, `sms.class_03-09-2021.php`, `redemption.class_*.php`). There is no use of git history for versioning. Anyone editing must be careful to touch only the live file (the one without a date suffix).
- Secrets and server artefacts are committed: `.bash_history`, `.mysql_history`, `.viminfo`, `mayavafinance.key` and `.csr`, DB credentials in `includes/db.php`.
- Git history has only 3 commits.

---

## 2. Existing SMS-related functionality

### 2.1 The SMS class – `classes/sms.class.php` (live version, 44 lines)
- One static method: `sms::sendData($smsData, $sendNo, $templateId)`.
- HTTP POST (cURL, form-encoded) to `https://sms.textspeed.in/vb/apikey.php?apikey=<hard-coded key>&senderid=KFLOAN`.
- Body fields: `templateid`, `message`, `number`, `unicode=2`.
- `CURLOPT_SSL_VERIFYPEER` is **false**.
- The response is fetched but **discarded**. Error handling is commented out. There is no return value, no logging, no timeout, no retry, no phone-number validation or normalisation.
- Sender ID `KFLOAN` and the provider are hard-coded. The `templateid` parameter and `unicode=2` indicate DLT-registered templates (India), with Unicode (Tamil) messages.

### 2.2 Gateway history (from the backup copies)
| File | Gateway |
|---|---|
| `sms.class_01_JUN_2018.php` | earlier version (38 lines) |
| `sms.class.php_14-03-2022`, `sms.class_03-09-2021.php` | `sms.nettyfish.com` (`pushsms.aspx`), `sid=KFLOAN`; the recipient number was hard-wired to a single test number while debugging |
| `sms.class_staging.php` | `sendData()` is an **empty stub** (so staging sends nothing); an older `sendDataOld` used `sms.creativepoint.in` (Mvaayoo / creativepoint) |
| `sms.class.php` (live) | `sms.textspeed.in` |

So the gateway has changed at least 3 times, which supports isolating the provider behind one interface.

### 2.3 Where SMS is actually sent (only two places)
| Trigger | File and location | Message (Tamil) | DLT template ID | Recipient |
|---|---|---|---|---|
| **New or updated pledge saved** | `product_pledge.php` ~L568–710, call at L710 | Scheme id 5: `நகை அடகு விபரம் அ.எண்:{loan_id} அசல்:{amount} 1.5% தினசரி வட்டி-GOLDLOAN`. Any other scheme: `நகை அ.விபரம் அ.எண்:{loan_id} அசல்{amount} வெள்ளி 3 தினசரி வட்டி-GOLDLOAN` | `1707161737187975096` (scheme 5), `1707161737208291855` (others) | `mf_customers.contact_no` of the pledge's customer |
| **Redemption saved** | `redemption.php` ~L380–467, call at L467 | `நகை திருப்பிய விவரம் அ.எண்:{loan_id} அசல்:{amount} வட்டி:{interest}-GOLDLOAN` | `1707161737194835530` (hard-coded inline) | `mf_customers.contact_no` |

Observations:
- Message wording is built inline in each module, and two template IDs are hard-coded in the controllers. Scheme-specific wording ("1.5% daily interest" and "silver 3 daily interest") is hard-coded to `scheme_id == 5` versus everything else.
- The send runs **synchronously inside the save request**. A slow gateway delays the user's save, and a failure is invisible.
- The `product_pledge.php` block sits after the pledge-detail loop. It looks like it runs for every save of a pledge (the `add` branch is at L347 and the block is not inside an `if($type=='add')`), so editing a pledge may re-send the SMS. This needs confirming by a runtime test.
- A commented-out call at `product_pledge.php:14` (`//sms::sendData('','');`) is a leftover test.
- Both modules look up `mf_sms_support` rows (`nagai_adagu_vibaram`, `nagai_thirupiya_vibaram`, `Asal`, `a_ean`, `scheme`, `Loan_amount`, `Interest`) to fetch Tamil words, but in the current code **the fetched values are no longer used in the live message** (the text is now a literal; the older concatenation is commented out). These are dead queries.

### 2.4 Database
- Only one SMS table is referenced: `SMS_SUPPORT` = `mf_sms_support` (`includes/tables.php:53`). From usage it has columns `name` and `tamil_name`, a key to Tamil vocabulary. **No schema file exists**, so the full structure is unknown.
- There is **no table for SMS logs, templates, queue, delivery status or customer consent**.

### 2.5 Operations that do NOT send SMS today
`pay_interest.php`, `renewal.php`, `partial_amount_payment.php`, `pay_excess_amount.php`, `auction_management.php`, `amount_received.php` and similar modules contain no SMS calls (grep confirmed). The customer receipt template (`layouts/default/forms/customer_receipt*.html`, L212 and L502) prints a Tamil note saying that the principal and interest will be sent to the customer by SMS (குறுஞ்செய்தி(SMS)) when redeeming. This shows the printed receipt promises an SMS that is only partly implemented (see section 5).

### 2.6 Related but separate
- Internal messaging (`Message` class, `mf_message_details`, `mf_message_list`) and email (`phpmailer`, `mail_functions.php`) are separate channels. They are potential patterns to follow, not SMS.
- Notification letters (`notification_letter.php`) are printed letters with reminder wording, a possible candidate for SMS reminders (a question, not a requirement).

---

## 3. What needs to be developed or modified

Because the requirement is undefined, this section separates (a) **gaps in the present implementation** that any SMS concept will need fixed and (b) **functionality that depends on the business definition**.

### 3.1 Gaps in the present SMS implementation (observed)
| # | Gap | Evidence |
|---|---|---|
| G1 | Gateway API key and sender ID hard-coded in source | `sms.class.php` L5 |
| G2 | TLS certificate verification disabled | `CURLOPT_SSL_VERIFYPEER=false` |
| G3 | No result handling: gateway response and cURL errors ignored | response discarded; debug block commented out |
| G4 | No send log or audit, so there is no way to prove an SMS was sent or why one failed | no table or code |
| G5 | Synchronous send inside the user's save transaction, no timeout set | `product_pledge.php:710`, `redemption.php:467` |
| G6 | Message text and template IDs hard-coded in controllers; scheme-specific content hard-coded | see 2.3 |
| G7 | No validation or normalisation of `contact_no` (min-length 10 is enforced only at customer entry; no country-code handling) | `customer.class.php` L22 |
| G8 | Possible duplicate SMS on pledge edit | see 2.3 (verify) |
| G9 | Dead `mf_sms_support` lookups add several useless DB queries per save | see 2.3 |
| G10 | Provider logic cannot be switched per environment (staging uses a separate stub file copy) | `sms.class_staging.php` |
| G11 | Several events lack SMS although the receipt promises or business may expect it | see 2.5 |

### 3.2 Candidate new functionality (to be confirmed – see section 9)
Only the items below follow from evidence in the code. Others (OTP, marketing, bulk, two-way SMS) have no basis in the repo and are not assumed.
- Extend SMS to other loan events: interest payment, renewal, partial payment, excess payment, auction notices and overdue or reminder notices. Evidence: these modules exist and have no SMS; `notification_letter` exists as a paper analogue.
- Configurable message templates (text plus DLT template ID per event and scheme).
- SMS history and resend capability for staff (analogous to the existing inbox and sent-items screens).
- Admin settings for gateway and sender ID (`settings.php` and `mf_settings` already exist).

---

## 4. Required changes by layer (proposed, pending confirmation)

### 4.1 Backend (PHP)
- **`classes/sms.class.php`**: keep the `sms::sendData(...)` signature for backward compatibility, but add: return value (success flag and gateway response), cURL timeout, cURL error capture, number normalisation and validation, credentials read from configuration instead of source, SSL verification enabled.
- **Logging**: write one record per attempt (see 4.3) from inside the class, so every caller gets it automatically.
- **Callers**: `product_pledge.php` and `redemption.php` should stop building text and looking up unused `sms_support` rows. They would call a single helper such as `sms::sendEvent($event, $loanData)` (name illustrative). Remove the dead queries only with explicit approval.
- **New SMS events**: add the call in `pay_interest.php`, `renewal.php`, `partial_amount_payment.php`, `pay_excess_amount.php` and auction modules after the successful save, following the pattern of the `case 'save'/'update'` blocks.
- **Constants**: add new table constants to `includes/tables.php` (existing convention, `mf_` prefix).
- Do not edit the dated backup files.

### 4.2 Frontend
- Currently there is **no SMS UI at all**. Only server-side sends exist.
- If a log or settings screen is approved: a new module page `sms_log.php` at the root with a template under `layouts/default/` plus entries in `menu.php`, the privilege tables (`privillege.php`, `mf_modules`, `mf_module_privillege`) and the left panel. It must follow the existing listing pattern (search, `paging`, JSON AJAX response) used by `message.php`.
- Possibly a "Resend SMS" action on the pledge and redemption screens.
- Receipt text (`customer_receipt*.html`) may need correcting if the wording about SMS is wrong for the final behaviour.

### 4.3 Database (no schema file exists; all DDL would be new)
Proposed tables (illustrative; names and columns need approval):
- `mf_sms_log`: id, event type, loan/pledge/redemption reference, customer id, branch id, mobile number, message text, template id, provider response, status, created datetime and user.
- `mf_sms_template` (only if templates are to be configurable): event, scheme (nullable), text with placeholders, DLT template ID, active flag.
- Existing `mf_sms_support` stays unless found obsolete. Its structure must first be read from the production database.
- A schema script must be supplied because no migrations exist. The production schema must be exported first to avoid guessing column types.

### 4.4 API / integration
- Outbound only: the SMS gateway (`sms.textspeed.in` currently). No inbound API is present in the repo. A delivery-report callback would require a new public endpoint and is only needed if delivery status tracking is required (question).
- No internal REST API exists. All "APIs" are page endpoints returning JSON-like strings.

---

## 5. Implementation plan (proposed phases)

1. **Discovery (blocking)**: obtain answers to section 9; export the production schema (`mf_sms_support`, `mf_customers`, `mf_pledge`, `mf_redemption`, `mf_settings`); obtain the gateway API documentation (status codes, delivery reports, balance and rate limits) and the list of DLT-approved templates and sender IDs.
2. **Hardening of the existing class** (lowest risk): configuration for key and sender, SSL verification, timeout, response and error handling, number normalisation, return value. Behaviour of the two current messages unchanged.
3. **SMS log table and logging** inside `sms.class.php`.
4. **Refactor the two existing callers** to a single helper; remove the dead lookups; resolve the duplicate-on-edit question (G8).
5. **New events** one module at a time, behind a settings switch so each can be turned on per environment.
6. **Optional UI**: log screen, resend action, template and settings screen, menu and privilege entries.
7. **Staging parity**: replace the file-copy approach (`sms.class_staging.php`) with an environment setting so staging cannot send to real customers.
8. **Release**: back up DB and code, deploy to staging, run the test plan, then production.

---

## 6. Testing requirements
There is no automated test infrastructure (no PHPUnit, no test directory; `test.php`, `test2.php` and `test_tamil.php` are ad hoc scripts). So testing must be defined and mostly manual unless the team approves adding a test harness.

- **Unit-level (if a harness is approved)**: number normalisation and validation; message construction per scheme and event; gateway-response parsing with mocked cURL.
- **Functional (manual, staging with a sandbox or test number)**:
  - Pledge create, edit and scheme variants (scheme 5 and others): one SMS per intended event, correct template ID and text.
  - Redemption: correct principal and interest values.
  - Each new event module: SMS sent only after a successful save, not on validation failure.
  - Customer with missing, short, or non-numeric contact number: save must still succeed and the failure must be logged.
  - Gateway down, timeout or error response: the user's transaction still succeeds and the failure is logged.
  - Tamil (Unicode) rendering and length and segment behaviour on a real handset.
- **Regression**: pledge, redemption, receipts, reports and listings still work. Check the JSON-like AJAX responses are unchanged, since the front-end parses them.
- **Security**: credentials not present in committed code; TLS verification on; log screen protected by the privilege system; no customer data exposed to users from other branches (the app filters by `session_user_branch_id` elsewhere, so the same rule must apply).
- **Environment safety**: confirm staging cannot reach the production gateway or real customers.

---

## 7. Dependencies

- External: the SMS gateway account and API (currently `sms.textspeed.in`), DLT registration with approved templates and sender ID (`KFLOAN`), available SMS credit, outbound HTTPS and cURL from the server.
- Data: `mf_customers.contact_no` quality; the `mf_sms_support` table; branch and scheme master data.
- Platform: the PHP version in production is not recorded in the repo (the DB wrapper falls back to the removed `mysql_*` functions, which suggests very old code). It must be confirmed before using any newer language features.
- People: the business owner (message content and events), the gateway or DLT contact, whoever administers the production server (config, cron, DB).

---

## 8. Risks

| Risk | Detail |
|---|---|
| Leaked credentials | The gateway API key is in the repository source and in git history. It should be rotated once moved to configuration. DB credentials (`includes/db.php`), `.mysql_history`, `.bash_history` and a private key file (`mayavafinance.key`) are also committed. |
| MITM exposure | SSL verification is disabled for SMS calls. |
| Silent failures | Current code cannot tell staff whether a customer received the SMS, a business and dispute risk given the receipt promises an SMS. |
| Transaction latency | The synchronous call blocks the save if the gateway is slow (no timeout). |
| Duplicate or wrong messages | Possible re-send on pledge edit; scheme-specific text depends on the magic value `scheme_id == 5`. |
| Regulatory | Template mismatches with DLT lead to rejected messages. Customer consent or opt-out rules are not handled anywhere. |
| Wrong-environment sends | Staging relies on swapping class files; a mistaken deploy could SMS real customers. |
| Codebase fragility | Many backup copies, no tests, no schema file, old PHP and jQuery, so change impact is hard to measure. Editing the wrong dated copy has no effect. |
| Unknown schema | `mf_sms_support` and the other tables cannot be verified from the repo. |

---

## 9. Questions requiring clarification

**Scope**
1. What exactly is the "SMS concept"? Is it a new feature, a rework of the current notifications, or a different mechanism (for example reminders, OTP, bulk SMS or SMS by schedule)?
2. Which events must send SMS? Current: pledge and redemption only. Should interest payment, renewal, partial payment, excess payment, auction notices and overdue reminders be added?
3. Is the receipt statement ("principal plus interest will be sent by SMS when redeeming") the intended behaviour? Today the SMS has loan id, principal and interest paid, not the amount payable.
4. Should SMS go only to the customer, or also to staff and branch contacts?

**Content and compliance**
5. Who owns the DLT templates, and how are new ones registered? Do we need the full list of approved template IDs and sender IDs?
6. Must wording stay fixed (scheme 5 = "1.5% daily interest", others = "silver 3 daily interest"), or should it be configurable per scheme from the UI?
7. Is customer consent or an opt-out list required?

**Technical**
8. Is `sms.textspeed.in` the permanent provider? Is a provider switch expected again? Can we get its API document (response codes, delivery reports, balance check)?
9. Is delivery status tracking needed (requires a public callback URL)?
10. Should sends be asynchronous (queue and cron)? Is cron available on the production server?
11. Does a duplicate SMS on pledge edit occur today in production, and is that intended?
12. What PHP and MySQL versions run in production and staging? Can we get a schema export?
13. Is `mf_sms_support` still needed now that the text is literal?
14. How should the exposed API key and DB credentials be handled (rotation, config location)?

**Process**
15. Is a UI needed (log, resend, template admin), and which user groups get access (privilege model)?
16. Who approves the scope of cleaning up the dead code and backup files? This analysis changed none of them.
17. Which environment and test numbers may be used for verification?

---

*Prepared from direct inspection of the repository. Nothing was assumed as a requirement; open items are in section 9.*
