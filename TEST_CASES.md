# Mayava Finance – Overall Test Cases

Scope: whole application (PHP, procedural, root-level module pages + `classes/` + `layouts/default/`).
Type: **manual functional test cases** – the repo has no PHPUnit/test harness (see `SMS_CONCEPT_ANALYSIS.md` §6). No code was changed to write this document.

## How to read this document
- **Source** column / notes cite the file the expectation comes from. Error/success texts are the actual constants in `includes/constants.php`.
- Where the code does not show a concrete expected value (for example interest amounts, report totals), the case says **"verify against business rule"** instead of inventing a number. Those need a finance owner to supply expected values (listed in §22).
- Priority: **P1** = money/data integrity or access control, **P2** = core function, **P3** = UI/convenience.
- Run everything on **staging with test customers/mobile numbers**. Never run SMS cases against production customers.

## Conventions / test data
| Item | Value |
|---|---|
| Users | U-SA (Super Admin), U-AD (Admin), U-BR (branch user, limited privileges), U-BLK (blocked, status≠1), U-NOPRIV (no module access) |
| Branches | B1 (with `system_ip` = tester machine IP), B2 (other IP) |
| Customers | C-OK (valid 10-digit mobile starting 6–9), C-BADMOB (short/blank mobile, or starting 0–5), C-DUP (duplicate data) |
| Schemes | S5 (scheme id 5, gold), S-OTH (any other scheme) |
| Loan | pledged loan L1 (open), L2 (redeemed), L3 (renewed) |

---

## 1. Authentication & session (`index.php`, `logout.php`, `Application::login/authenticate`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| AUTH-01 | P1 | Login with valid email + password from an allowed IP (the user's branch `system_ip`) | Redirect to `dashboard.php`; `last_login` updated; session holds user, branch, group, modules |
| AUTH-02 | P1 | Login with wrong password | Message "Invalid Password!"; no session |
| AUTH-03 | P1 | Login with unknown email | "Invalid Username!" |
| AUTH-04 | P1 | Login with blocked user (status ≠ 1), correct password | "Account Blocked!" |
| AUTH-05 | P1 | Login from an IP different from the branch `system_ip` (and not on the hard-coded allow-list) | "Please Use valid machine to login!" |
| AUTH-06 | P1 | Login as user id 1 from any IP | IP check skipped for user id 1 (code: `$user[0]['id']!='1'`) – confirm this is intended |
| AUTH-07 | P2 | Submit with empty username and/or password | "Required Fields Missing!" |
| AUTH-08 | P2 | Already logged in, open `index.php` | Redirect to `dashboard.php` |
| AUTH-09 | P1 | Logout, then press Back / open any module URL directly | Redirected to `index.php`; no data shown |
| AUTH-10 | P1 | Open each module URL without logging in (loop through root `*.php`) | Every page redirects to login (note: pages such as `phpinfo.php`, `test*.php`, `sample*.php`, `info.php`, `upload.php`, `unzip.php` – record whether they require login) |
| AUTH-11 | P2 | Password containing quotes / `' OR 1=1 --` | Login fails safely, no SQL error shown |
| AUTH-12 | P3 | Repeated failed logins (10+) | Record behaviour (no lockout found in code) |

## 2. Forgot password (`forgot_password.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| FP-01 | P2 | Active user email | Mail sent (template `layouts/default/mails/forgot_password.html`) |
| FP-02 | P2 | Unknown / inactive email | "Please check the email address!" |
| FP-03 | P2 | Empty email | "Invalid Email Address!" |
| FP-04 | P1 | Inspect mail content | Check whether a plain password is emailed (security review item) |

## 3. Access control / privileges (`privillege.php`, `is_accessible`, `can_add/edit/delete/view`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| PRV-01 | P1 | U-NOPRIV opens a module URL directly | Access denied / redirect (per `is_accessible()`) |
| PRV-02 | P1 | Group with only "view" on a module | Add/edit/delete buttons hidden and the corresponding `type=` requests rejected server-side (call `?type=delete` directly) |
| PRV-03 | P1 | Group with add but no delete | Delete not possible via UI nor direct URL |
| PRV-04 | P2 | Assign/unassign module privileges to a group, re-login | Menu and left panel reflect change |
| PRV-05 | P1 | Branch user searches/list pages | Only own branch data (`session_user_branch_id`) shown; super admin sees all |
| PRV-06 | P1 | Branch user edits URL `id=` of another branch's record | Not allowed / not shown |
| PRV-07 | P2 | `can_change_status` for Admin/Super Admin vs normal | Only Admin/Super Admin can change status |
| PRV-08 | P3 | Privilege page lists all modules/categories | Matches `mf_modules` |

## 4. Users (`users.php`, `classes/user.class.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| USR-01 | P2 | Add user with all required fields (name, branch, user type, password, email) | "User saved successfully" |
| USR-02 | P2 | Leave each required field blank, one at a time | "Required Fields Missing!" |
| USR-03 | P2 | Invalid email format | "Invalid Email" |
| USR-04 | P2 | Existing email | "Users Details Already exists!" |
| USR-05 | P2 | Password of 5 chars | "Password length should be greater than 5!"; 6 chars accepted |
| USR-06 | P2 | Edit user, change group/branch | "User updated successfully"; effect visible on next login |
| USR-07 | P1 | Block a user (status) | User cannot login (AUTH-04) |
| USR-08 | P2 | Delete user | Removed; related records unaffected |
| USR-09 | P1 | Check password storage in DB | Record how it is stored (`encrypt_password`) – security review |
| USR-10 | P3 | `mayava_users_list.php` | Lists users correctly |

## 5. Branches & sub-branches (`branches.php`, `sub_branches.php`, `sub_branch_loanid_generate.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| BR-01 | P2 | Add branch with required fields (organization, name, address, location, description, system_ip, out_time) | "Branch saved successfully" |
| BR-02 | P2 | Missing each required field | "Required Fields Missing!" |
| BR-03 | P2 | Duplicate branch name or branch code | "Details Already exists!" |
| BR-04 | P2 | Edit branch | "Branch updated successfully" |
| BR-05 | P1 | Delete branch | Message "Branch Deleted. All the branch regarding data transfer to the head office branch." – verify data really moves and no orphans |
| BR-06 | P1 | Change branch `system_ip`, login from old/new IP | Login rule (AUTH-05) follows new IP |
| SB-01 | P2 | Add / edit sub-branch | "Sub Branch saved/updated successfully" |
| SB-02 | P2 | Delete sub-branch with and without pledges | "Sub Branch Deleted" vs "Sub Branch not Deleted" as applicable |
| SB-03 | P1 | Sub-branch loan-id generation settings | New pledge IDs follow configured code/sequence (see PLG-04) |

## 6. Customers (`customers.php`, `customers_details.php`, `export_customer.php`, `customer_id_report.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| CUS-01 | P2 | Add customer with all required fields (name, branch, relationship type/category, relation name, street, location, district, postal code, contact no) | "Customer saved successfully"; customer code generated |
| CUS-02 | P2 | Leave each required field blank | "Required Fields Missing!" |
| CUS-03 | P2 | Contact number of 9 digits | Rejected (min length 10); 10 digits accepted |
| CUS-04 | P2 | Duplicate contact number / email / ID-proof code | Duplicate message (labels: Customer Code, Customer Email, Contact Number) |
| CUS-05 | P2 | Non-numeric / special characters in contact | Record behaviour (code only checks length) |
| CUS-06 | P2 | Upload photo + signature (webcam `photo_capture`/`photo_booth`, file upload) | Images stored under `customer/images/<id>/`; shown on edit and receipt |
| CUS-07 | P2 | Edit customer | "Customer updated successfully" |
| CUS-08 | P2 | Search by name, code, mobile, location | Matching rows only; pagination works |
| CUS-09 | P2 | Export customers (`export_customer.php`) | File opens; columns/data match screen; Tamil text intact |
| CUS-10 | P1 | Delete customer with open pledges | Blocked or handled – record behaviour |
| CUS-11 | P2 | Tamil names/addresses | Stored and displayed correctly (utf8mb4) |
| CUS-12 | P3 | Customer with blank-photo | Default `blank_photo.jpg` shown |

## 7. Master data
### 7.1 Categories / products / pre-name / product-with (`categories.php`, `products.php`, `product_pre_name.php`, `product_with.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| MST-01 | P2 | Add/edit/delete category, product, pre-name, product-with | Success messages ("Categories/Product saved successfully", etc.) |
| MST-02 | P2 | Duplicate names | Duplicate rejected |
| MST-03 | P2 | Dependent dropdowns in pledge form (category → pre-name → product-with → name) | Lists filter correctly |
| MST-04 | P2 | Delete a product already used in a pledge | Blocked/handled |

### 7.2 Schemes (`schemes.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| SCH-01 | P2 | Add scheme with name + loan rate | "Scheme saved successfully" |
| SCH-02 | P2 | Missing name or rate | "Required Fields Missing!" |
| SCH-03 | P2 | Duplicate scheme name | "Already exists!" |
| SCH-04 | P1 | Edit rate of an in-use scheme | Existing loans' interest unaffected or changed per business rule – verify |
| SCH-05 | P2 | Scheme details (slabs / penalty rates) saved | Used by interest calculation (see PI-*) |

### 7.3 Service charges (`service_charges.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| SVC-01 | P2 | Add slab (starting, ending, charge) | Saved |
| SVC-02 | P2 | Duplicate/overlapping slab | Duplicate rejected; record behaviour for overlap |
| SVC-03 | P1 | Pledge with loan amount at slab boundaries (start, end, end+1) | Correct charge fetched (`fetch_service_charge_amount`) |
| SVC-04 | P2 | Amount outside all slabs | Record behaviour; pledge save requires non-zero charge ("Service Charge amount cannot be Zero") |

### 7.4 Departments, leaves, settings (`departments.php`, `leaves.php`, `settings.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| DEP-01 | P3 | Add/edit/delete department | "Department saved/updated successfully" |
| LV-01 | P3 | Add/edit/delete leave entry | Saved; listed |
| SET-01 | P2 | Add setting (name + value); missing either | Saved / "Required Fields Missing!" |
| SET-02 | P2 | Edit setting | "Settings updated successfully"; effect visible where used |

## 8. Product pledge – new loan (`product_pledge.php`, `ProductPledge`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| PLG-01 | P1 | Create pledge for C-OK with sub-branch, scheme, loan amount, ≥1 item, gross/net weight, item count | "Product pledged saved successfully"; loan id generated; details rows saved; cash-flow entry created |
| PLG-02 | P1 | Leave each required field blank (sub-branch, customer, scheme, loan amount, gross weight, net weight, item count) | Validation message per field ("Product pledge data(s) invalid !") |
| PLG-03 | P1 | Net weight > gross weight | Record behaviour – verify business rule |
| PLG-04 | P1 | Loan id uniqueness: create two pledges in the same sub-branch | Sequential distinct ids per `getNewSubLoanCode`; duplicate id → rejected |
| PLG-05 | P1 | Empty loan id (tamper hidden `pval[loan_id]`) | "Loan ID data(s) invalid !" |
| PLG-06 | P1 | Zero / missing service charge | "Service Charge amount cannot be Zero" |
| PLG-07 | P1 | Customer without valid mobile (C-BADMOB) | Validation: "Customer Mobile data invalid !" (10 digits, first digit 6–9) |
| PLG-08 | P1 | Loan amount 0, negative, decimal, very large | Rejected or handled; no negative loans |
| PLG-09 | P2 | Add multiple items; remove an item before saving | Item count, weights match saved rows |
| PLG-10 | P2 | Edit pledge (update) | "Pledged product updated successfully"; modified_by/date set |
| PLG-11 | P1 | Edit pledge twice quickly / double-click Save | Only one pledge created (no duplicates) |
| PLG-12 | P1 | Delete pledge with no transactions | "Pledged product deleted successfully"; related cash-flow reversed (`deleteCashFlow`) |
| PLG-13 | P1 | Delete pledge that has pay-interest / auction / redemption / renewal (old or new) / excess / partial records | Blocked with the matching `VALIDATE_PRODUCT_PLEDGE_*` messages |
| PLG-14 | P2 | Preview, paging, search (name, customer id, location, FHW, branch, loan id, mobile) | Correct results; paging counts |
| PLG-15 | P2 | Print pledge receipt / slip (`customer_receipt.php`, `customerslip.php`) | Data, Tamil text, photo, signature render; printable |
| PLG-16 | P1 | Back-date / future-date loan date | Record rule |
| PLG-17 | P1 | Scheme change on form | Interest/scheme details refresh (`fetch_scheme_details`) |
| PLG-18 | P1 | Cash-flow: pledge reduces branch cash by amount given | Verify entry in cash-flow report |
| PLG-19 | P2 | Pledge SMS (see §19) | One SMS per intended save |

## 9. Pay interest (`pay_interest.php`, `PayInterest`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| PI-01 | P1 | Select open loan; interest auto-calculated for today | Amount = **verify against business rule** (days × rate, normal vs penalty slab, `total_days_of_year`) |
| PI-02 | P1 | Save payment | "Interest payment processed successfully"; cash-flow credit; appears in list |
| PI-03 | P1 | Required fields blank (interest amount, loan id, days, rate, paid on) | "Pay interest data(s) invalid !" |
| PI-04 | P1 | Paid date before loan date | Date invalid alert (`PAID_DATE_INVALID_ALERT`) |
| PI-05 | P1 | Waiver days greater than allowed | `WAVIER_DAYS_INVALID_ALERT` |
| PI-06 | P1 | Waiver within limit with reason | Interest reduced by waiver; reason stored |
| PI-07 | P1 | Boundary dates: 1st day, month end, 29 Feb, year end | Day counts correct |
| PI-08 | P1 | Pay interest twice for same period | Second payment starts from last pay date; no double counting |
| PI-09 | P2 | Edit payment (update) | "Interest payment modified"; totals recalc |
| PI-10 | P1 | Delete payment | "Paid interest been deleted successfully"; cash-flow reversed; interest due restored |
| PI-11 | P2 | Fetch loan details for loan id not found / closed loan | Handled message, no PHP error |
| PI-12 | P1 | Amount shown on screen manipulated before save (tamper `interest_amount`) | Server recomputes or rejects (compare with `getInterestDetails`) |
| PI-13 | P2 | Receipt/print | Matches saved values |

## 10. Renewal (`renewal.php`, `renewal-detailed.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| RN-01 | P1 | Renew open loan: old loan, new loan number, loan amount, new scheme | "Renewal successfully"; old loan closed, new loan created; interest on old loan settled |
| RN-02 | P1 | Missing required fields | "Renew the Pleged product data(s) invalid !" |
| RN-03 | P1 | New loan amount greater/less than old principal | Cash difference recorded correctly in cash-flow |
| RN-04 | P1 | Renew an already redeemed/renewed loan | Rejected |
| RN-05 | P1 | New loan number already used | Rejected |
| RN-06 | P2 | Delete renewal | Old loan reopened; new loan removed; cash-flow reversed (verify) |
| RN-07 | P2 | Renewal report (detailed) | Totals match transactions |

## 11. Partial amount payment (`partial_amount_payment.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| PA-01 | P1 | Pay part of principal: customer, old loan, new loan, partial amount, scheme, new loan amount | "Partial amount paid successfully"; new loan = old principal − partial (verify) |
| PA-02 | P1 | Partial amount 0, negative, ≥ principal | Rejected |
| PA-03 | P1 | Required fields blank | Validation message |
| PA-04 | P1 | Interest due handled at partial payment | Verify rule |
| PA-05 | P2 | Delete partial payment | Reverses loans and cash-flow |

## 12. Pay excess amount (`pay_excess_amount.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| EX-01 | P1 | Increase loan (excess to customer): customer, loan amount, old/new loan, excess amount, scheme | "Excess amount paid to customer successfully"; new loan = old + excess (verify) |
| EX-02 | P1 | Zero/negative/non-numeric excess | Rejected |
| EX-03 | P1 | Required fields blank | Validation message |
| EX-04 | P1 | Cash-flow shows cash-out for excess | Correct |
| EX-05 | P2 | Delete excess payment | Reversal correct |

## 13. Redemption (`redemption.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| RD-01 | P1 | Redeem open loan, paid principal + interest | "Pledged product have been redeemed successfully"; loan closed; cash-flow credit; sub-customer photo/sign saved |
| RD-02 | P1 | Principal amount blank | "Required Fields Missing!" |
| RD-03 | P1 | Paid date before loan date | `PAID_DATE_INVALID_ALERT` |
| RD-04 | P1 | Waiver days invalid | `WAVIER_DAYS_INVALID_ALERT` |
| RD-05 | P1 | Interest on screen differs from recalculated (edit-status users) | `PAID_ON_RESET_ALERT` shown |
| RD-06 | P1 | Customer mobile invalid (C-BADMOB) | Validation error message about mobile |
| RD-07 | P1 | Redeem an already redeemed loan; double click Save | Second attempt rejected; one record only |
| RD-08 | P1 | Redeem with partial interest payments made earlier | Interest due = verify rule |
| RD-09 | P1 | Delete redemption | Loan reopens; cash-flow reversed |
| RD-10 | P2 | Receipt prints; "SMS will be sent" note present | Content correct |
| RD-11 | P2 | Redemption SMS (see §19) | Sent once with correct amounts |
| RD-12 | P1 | Redeem in sub-branch different from pledge sub-branch | Verify rule |
| RD-13 | P2 | Photo/sign capture on redemption | Files stored under `customer/images/<id>/sub/` |

## 14. Auction (`auction_management.php`, `auction_notice.php`, `auction_final_notice.php`, `pre_auction_notice.php`, `pre_auction_report.php`, `post_auction_report.php`)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| AU-01 | P1 | Pre-auction report lists loans past due as per rule | List matches business definition |
| AU-02 | P2 | Generate pre-auction / auction / final notice (letter, Tamil text) | Letter prints with correct customer, loan, dates (`languages/notice.php` text) |
| AU-03 | P1 | Record auction: amount difference, loan, items, gross/net weight, paid on, serial no | "Auction bided successfully"/"Auction biding details saved successfully" |
| AU-04 | P1 | Required fields blank | Validation message |
| AU-05 | P1 | Duplicate serial/code | Duplicate rejected |
| AU-06 | P1 | Auctioned loan: further interest/redemption not allowed | Rejected |
| AU-07 | P1 | Auction surplus/shortage amounts | Verify calculation |
| AU-08 | P2 | Delete auction | "Bided auction deleted successfully"; loan state restored |
| AU-09 | P2 | Post-auction report totals | Match auction records |

## 15. Cash & bank flows
| ID | Pri | Steps | Expected |
|---|---|---|---|
| CF-01 | P1 | Add cash-flow transaction (branch, type, purpose, date, amount); missing fields | "Cashflow saved successfully" / validation |
| CF-02 | P1 | Edit/delete cash-flow | "Cashflow updated/delete successfully"; balances recalc |
| CF-03 | P1 | Daily opening/closing balance across consecutive days | Closing(day n) = opening(day n+1) |
| CF-04 | P1 | Auto entries from pledge/interest/redemption/renewal/partial/excess | One entry per transaction with correct sign |
| AT-01 | P1 | Amount allocation to branch (`allocation_of_amount.php`, `allocation_of_branch.php`) | "Allocated successfully"; both branches' balances move |
| AT-02 | P1 | Amount transfer (mode, user, cheque details, date) then receive (`amount_transfer.php`, `amount_received.php`) | "Amount tranfered successfully" / "Amount received successfully"; totals tie |
| AT-03 | P1 | Transfer more than available balance | Rejected or flagged |
| AT-04 | P2 | Bank transaction at branch level (branch, employee, mode, date) | "Bank transaction saved successfully"; edit/delete messages |
| AT-05 | P2 | Money-shortage request (`raise_request.php`) | "Request money shortage saved successfully"; update/delete |
| AT-06 | P1 | Month/year boundary; back-dated entries | Reports correct |

## 16. Expenses (`expenses*.php`, `Expenses*` classes, added 18/08/2025)
| ID | Pri | Steps | Expected |
|---|---|---|---|
| EXP-01 | P2 | Add category; duplicate name | Saved / "Category Name Already exists!" |
| EXP-02 | P2 | Add sub-category under category; ajax dropdown (`ajax_get_subcategory.php`) | Loads only that category's items |
| EXP-03 | P1 | Add expense: branch, category, sub-category, opening balance, amount, cash type, date | Saved; branch cash reduced |
| EXP-04 | P1 | Missing each required field | Validation |
| EXP-05 | P1 | Amount 0 / negative / above available cash | Rejected or flagged |
| EXP-06 | P1 | Edit / delete expense | Cash balance recalculated |
| EXP-07 | P2 | Expense report (`expenses_report.php`, `ajax_expenses_report.php`) filters and totals | Match entries |
| EXP-08 | P1 | Expenses appear in daily report | Included as designed (commit "expenses report module and incorporate with daily report") |

## 17. Reports (all `*_report*.php`, `summary_reports.php`, `tally_report.php`, `stock_report.php`, `log_report.php`)
For each report: (a) loads without PHP/SQL error; (b) date/branch/customer filters work; (c) totals equal the sum of rows; (d) branch user sees only own branch; (e) Excel/PDF export opens and matches screen; (f) empty result handled; (g) large range (1 year) performs acceptably.
| ID | Pri | Report | Specific checks |
|---|---|---|---|
| RPT-01 | P1 | Daily report (`daily_report.php`, `daily_report_dev.php`) | Opening + inflows − outflows = closing; dev vs live agree |
| RPT-02 | P1 | Cash flow reports / cash-in / cash-out details | Match cash-flow entries |
| RPT-03 | P1 | Closing report (`closing_report*.php`) | Balances tie to daily report |
| RPT-04 | P1 | Detailed ledger / stock / detailed stock | Loan-wise principal/weight totals |
| RPT-05 | P1 | Income reports (interest, service charge, auction) | Match payments |
| RPT-06 | P2 | Branch-wise summary, category-wise, loan-amount-rate-wise | Group totals |
| RPT-07 | P2 | Customer pledged, customer-wise transaction, customer-id report | Filter by customer/mobile |
| RPT-08 | P2 | Interest-paid details | Matches pay-interest records |
| RPT-09 | P2 | Tally report | Export format accepted by Tally (confirm format) |
| RPT-10 | P2 | Log report | User actions recorded with user/time |
| RPT-11 | P2 | Pre/post-auction reports | See §14 |
| RPT-12 | P3 | Print stylesheets (`*_print.css`) | Layout OK; Tamil fonts embedded in PDF |

## 18. Messaging & notification letters
| ID | Pri | Steps | Expected |
|---|---|---|---|
| MSG-01 | P3 | Compose internal message (branch, subject, message, to); missing fields | Sent / "Required Fields Missing!" |
| MSG-02 | P3 | Recipient inbox shows unread; opening marks read; sent items lists | Correct status text |
| MSG-03 | P3 | Delete inbox / sent item | "…details are deleted!" |
| MSG-04 | P1 | Branch user only sees messages addressed to them | Isolation |
| NL-01 | P2 | Notification letter list filters (date range, customer, location, FHW, branch, loan) | Correct rows |
| NL-02 | P2 | Print reminder letter (Tamil) | Fields filled correctly |

## 19. SMS (current behaviour – `classes/sms.class.php`, `product_pledge.php`, `redemption.php`)
Use a gateway sandbox or a dedicated test mobile number.
| ID | Pri | Steps | Expected |
|---|---|---|---|
| SMS-01 | P1 | Save new pledge, scheme id 5 | One SMS to customer mobile with template `1707161737187975096` text "நகை அடகு விபரம்… 1.5% தினசரி வட்டி-GOLDLOAN", correct loan id & amount |
| SMS-02 | P1 | Save new pledge, any other scheme | One SMS with template `1707161737208291855` text |
| SMS-03 | P1 | Edit/update an existing pledge | Record whether a second SMS is sent (suspected duplicate – see analysis G8) |
| SMS-04 | P1 | Redeem loan | One SMS, template `1707161737194835530`, loan id, principal, interest |
| SMS-05 | P1 | Customer mobile invalid | Save blocked by validation (PLG-07/RD-06) and no SMS call |
| SMS-06 | P1 | Gateway unreachable (block outbound host) | Business transaction still saves; note no error/log is produced (known gap) |
| SMS-07 | P2 | Gateway slow (simulate delay) | Measure save-time impact (no timeout in code) |
| SMS-08 | P2 | Tamil text renders correctly on handset; message length/segments | Readable, not garbled |
| SMS-09 | P1 | Staging safety | Confirm staging cannot SMS real customers |
| SMS-10 | P2 | Interest payment, renewal, partial, excess, auction | Confirm **no** SMS today (documented gap) |
| SMS-11 | P1 | Source/config review | API key not exposed to users in responses/pages |

## 20. Cross-cutting UI, data & security
| ID | Pri | Steps | Expected |
|---|---|---|---|
| X-01 | P1 | Enter `<script>` / HTML in text fields (names, address, reasons, messages) | Output escaped; no script runs |
| X-02 | P1 | SQL metacharacters (`'`, `"`, `;--`) in every search box and text field | No SQL error/injection; correct literal handling |
| X-03 | P1 | Direct request of `?type=delete|save|update` without privilege or CSRF token | Rejected (note whether CSRF protection exists) |
| X-04 | P1 | Access sensitive files by URL: `.bash_history`, `.mysql_history`, `.viminfo`, `mayavafinance.key`, `includes/db.php`, `phpinfo.php`, `*.class_*.php` backups | Must not be downloadable/readable on the web server (these are committed in the repo) |
| X-05 | P1 | File upload (`upload.php`, photos) with .php / oversized / wrong type | Rejected; files not executable |
| X-06 | P2 | Session timeout / idle | Behaviour recorded |
| X-07 | P2 | Two users edit the same record | Last-save behaviour recorded; no data corruption |
| X-08 | P2 | Pagination (first, last, beyond range), page size | Correct |
| X-09 | P2 | Date formats (dd/mm/yyyy display vs yyyy-mm-dd DB), date picker (`jscal2`) | Consistent across modules |
| X-10 | P2 | Currency/number formatting (`convertToCurrency`, `makeComma`, decimals, rounding) | Consistent; rounding rule documented |
| X-11 | P2 | Tamil input/display (keyboard widget `keypad.js`, `tamil.js`), PDF/Excel exports | Correct glyphs |
| X-12 | P3 | Browsers: current Chrome, Edge, Firefox, Safari; zoom 100/125% | Forms usable (jQuery 1.4–1.8 era code) |
| X-13 | P3 | Mobile / small screen | Record usability (layout is desktop-oriented) |
| X-14 | P2 | PHP error display | No warnings/notices shown to users; errors logged |
| X-15 | P2 | Duplicate-file hygiene: confirm no module loads a dated backup class by mistake | Live classes used |

## 21. Non-functional
| ID | Pri | Check | Expected |
|---|---|---|---|
| NF-01 | P2 | List pages and reports with realistic volume (all branches, 1+ year) | Loads within agreed time; memory limit (1024M in `db.php`) not exhausted |
| NF-02 | P2 | Concurrent users (e.g. 20) saving pledges | Unique loan ids, no deadlocks |
| NF-03 | P1 | Backup/restore of DB and `customer/images/` | Restore reproduces data |
| NF-04 | P2 | Time zone | Application timestamps use Asia/Calcutta where set; check mixed handling |
| NF-05 | P2 | Cron/scheduled jobs (server `crontab -l` was used) | Document and test any job |

## 22. Information needed to finalise expected results
These are not in the repo, so the expected values above say "verify":
1. Interest rules per scheme (normal vs penalty slabs, day-count basis, minimum days, rounding) with worked examples.
2. Rules for partial payment/excess loans (new principal, interest settlement).
3. Auction eligibility period and surplus/shortage accounting.
4. Cash-flow sign conventions and opening-balance rules.
5. Whether net weight may exceed gross weight, and date back-dating limits.
6. Intended behaviour of: IP rule for user id 1, branch delete, customer delete with open loans, SMS on pledge edit.
7. Seed data / DB dump for a test environment (no schema or fixtures exist in the repo).

## 23. Suggested execution order & exit criteria
1. Smoke: AUTH-01, PRV-01, CUS-01, PLG-01, PI-02, RD-01, RPT-01.
2. P1 cases by module in order: Masters → Customers → Pledge → Interest → Renewal/Partial/Excess → Redemption → Auction → Cash flow → Reports → Security.
3. P2 then P3.
4. Exit: all P1 pass; no open money/data-integrity defects; P2 failures have accepted workarounds; X-04 and SMS-09 confirmed.

Total: 210 numbered test cases across 23 sections; the report-level checks (a)–(g) in §17 apply to each report in addition.
