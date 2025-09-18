<?php
$module_name = "expenses";
?>
<!-- Choices.js CSS -->

<style>
/* --- Styling --- */
#expensesTable{width:100%;border-collapse:collapse;margin-top:15px;font-family:Arial,sans-serif;box-shadow:0 0 5px rgba(0,0,0,0.05)}
#expensesTable th,#expensesTable td{border:1px solid #ddd;padding:10px;text-align:left;vertical-align:top;}
.description{width:100%}.amount{width:10%}
#expensesTable thead th{background:#A33D98;color:#fff;font-weight:bold}
#expensesTable tbody tr{background:#fff;transition:background-color .3s}
#expensesTable tbody tr:hover{background:#f9f9f9}
#expensesTable textarea[name="description[]"]{width:100%;min-height:80px;padding:8px;border-radius:4px;border:1px solid #ccc;resize:vertical;box-sizing:border-box}
#expensesTable input[name="amount[]"]{width:100%;min-height:35px;text-align:left;padding:6px;border-radius:4px;border:1px solid #ccc;box-sizing:border-box}
#expensesTable select[name="cash_type[]"]{width:90%;min-height:35px;padding:6px;border-radius:4px;border:1px solid #ccc;box-sizing:border-box}
#addMoreBtn{background:#3498db;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;font-weight:bold;margin-top:15px;float:right}
#addMoreBtn:hover{background:#2980b9;transform:translateY(-2px)}

/* --- Choices.js Custom Styling --- */
.choices {
  width: 100% !important;
  max-width: 100% !important;
  font-size: 14px;
  display: block;
}

.choices__inner {
  min-height: 40px;
  border: 1px solid #ccc;
  border-radius: 6px;
  padding: 6px 8px !important;
  background: #fff;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.choices__list--dropdown, 
.choices__list[aria-expanded] {
  width: 100% !important;
  box-sizing: border-box;
}

.choices__input {
  padding: 6px !important;
  margin: 4px 0 !important;
  border: 1px solid #ddd !important;
  border-radius: 4px !important;
  font-size: 14px;
  width: 98% !important;
  box-sizing: border-box;
}

.choices__list--multiple .choices__item {
  background-color: #a33d98;
  border-radius: 4px;
  color: #fff;
  font-size: 13px;
  margin: 2px;
  padding: 4px 8px;
}

.choices__list--multiple .choices__item .choices__button {
  border-left: 1px solid rgba(255,255,255,0.4);
  margin-left: 6px;
  color: #fff;
}
</style>

<div class="breadcrumbs">
  <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
      <td>&nbsp;Masters  &raquo; Expenses &raquo; Master</td>
      <td>
        <span class="btn-rht">
          <a href="javascript:;" class="greybtn" onclick="return preSave();">SAVE</a>
          <a href="javascript:;" class="greybtn" onclick="return resetForm();">RESET</a>
          <a href="expenses.php" class="greybtn">BACK</a>
        </span>
      </td>
    </tr>
  </table>
</div>

<div id="contentarea">
  <form id="expensesForm" method="post" style="margin:0;">
    <table class="frm-cntr" width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr><td class="title">Expenses &raquo; Master</td></tr>

      <tr>
        <td width="50%">
          <table class="frm-tbl" width="100%">
            <tr>
              <td>Branch* </td>
              <td>
                <select id="branch_id" class="textfield-m" required onchange="filterSubBranches()">
                  <option value="">-- Select Branch --</option>
                </select>
              </td>
            </tr>

            <tr>
              <td>Sub branch* </td>
              <td>
                <select id="sub_branch_id" class="textfield-m" multiple required>Select</select>
              </td>
            </tr>

            <tr>
              <td>Expenses Category* </td>
              <td>
                <select id="category_id" class="textfield-m" required onchange="filterSubCategories()">
                  <option value="">-- Select Category --</option>
                </select>
              </td>
            </tr>

            <tr>
              <td>Expenses Sub Category* </td>
              <td>
                <select id="sub_category_id" class="textfield-m" required>
                  <option value="">-- Select Sub Category --</option>
                </select>
              </td>
            </tr>

            <tr>
              <td>Opening Balance*</td>
              <td>
                <input type="number" step="0.01" min="0" id="opening_balance"
                  class="textfield-m" required placeholder="Enter Opening Balance">
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td>
          <table id="expensesTable">
            <thead>
              <tr>
                <th>Description</th>
                <th>Amount</th>
                <th>Cash Type</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="tableBody"></tbody>
          </table>
          <button type="button" id="addMoreBtn" onclick="addRow()">Add More</button>
        </td>
      </tr>
    </table>
  </form>
</div>

<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
// Dummy data for dropdowns
const branches = {
  "KAMARAJAR ROAD": ["வர்னிகா பைனான்சியர்ஸ்","கவிதா பைனான்சியர்ஸ்", "வர்னிகா பைனான்ஸியர்ஸ்","கற்பகம் பைனான்ஸியர்ஸ்","வெங்கடேஸ்வரா பைனான்ஸியர்ஸ்", "வெங்கடேஸ்வரா பைனான்சியர்ஸ்", "கற்பகம் பைனான்சியர்ஸ்"],
  "KUMAR NAGAR": ["வெங்கடேஸ்வரா பைனான்சியர்ஸ்", "வெங்கடேஸ்வரா பைனான்ஸியர்ஸ்"]
};

const categories = {
  "NSR.Road": ["வெங்கடேஸ்வரா பைனான்சியர்ஸ்", "கற்பகம் பைனான்சியர்ஸ்"],
  "PN ROAD": ["கற்பகம் பைனான்சியர்ஸ்", "கவிதா பைனான்சியர்ஸ்"],
  "PALLADAM ROAD": ["கவிதா பைனான்சியர்ஸ்", "சரவணா பைனான்சியர்ஸ்"]
};

// Pair groups for auto select
const subBranchPairs = [
  ["வர்னிகா பைனான்சியர்ஸ்", "வர்னிகா பைனான்ஸியர்ஸ்"],
  ["வெங்கடேஸ்வரா பைனான்சியர்ஸ்", "வெங்கடேஸ்வரா பைனான்ஸியர்ஸ்"],
  ["கற்பகம் பைனான்சியர்ஸ்", "கற்பகம் பைனான்ஸியர்ஸ்"]
];

// Populate Branch
const branchSelect = document.getElementById("branch_id");
Object.keys(branches).forEach(b => {
  let opt = document.createElement("option");
  opt.value = b; opt.textContent = b;
  branchSelect.appendChild(opt);
});

// Populate Categories
const catSelect = document.getElementById("category_id");
Object.keys(categories).forEach(c => {
  let opt = document.createElement("option");
  opt.value = c; opt.textContent = c;
  catSelect.appendChild(opt);
});

// Init Choices.js
const subBranchElement = document.getElementById("sub_branch_id");
const subBranchChoices = new Choices(subBranchElement, {
  removeItemButton: true,
  searchPlaceholderValue: "Search sub branch...",
  noResultsText: "No results found",
  placeholderValue: 'Select Sub Branch*', // 👈 ithu user-ku visible placeholder
  searchPlaceholderValue: 'Type to search...',// 👈 search box placeholder
});

// Filter Sub Branch
function filterSubBranches(){
  const val = branchSelect.value;
  subBranchChoices.clearChoices();
  if(branches[val]){
    subBranchChoices.setChoices(
      branches[val].map(sb => ({value: sb, label: sb})),
      'value',
      'label',
      true
    );
  }
}

// Filter Sub Category
function filterSubCategories(){
  const val = catSelect.value;
  const subSel = document.getElementById("sub_category_id");
  subSel.innerHTML = '<option value="">-- Select Sub Category --</option>';
  if(categories[val]){
    categories[val].forEach(sc=>{
      let opt = document.createElement("option");
      opt.value = sc; opt.textContent = sc;
      subSel.appendChild(opt);
    });
  }
}


// Auto select & delete pair logic with proper events
subBranchElement.addEventListener("addItem", function(event){
  let value = event.detail.value;

  subBranchPairs.forEach(pair=>{
    if(value === pair[0] && !subBranchChoices.getValue(true).includes(pair[1])){
      subBranchChoices.setChoiceByValue(pair[1]);
    }
    if(value === pair[1] && !subBranchChoices.getValue(true).includes(pair[0])){
      subBranchChoices.setChoiceByValue(pair[0]);
    }
  });
});

subBranchElement.addEventListener("removeItem", function(event){
  let value = event.detail.value;

  subBranchPairs.forEach(pair=>{
    if(value === pair[0]){
      subBranchChoices.removeActiveItemsByValue(pair[1]);
    }
    if(value === pair[1]){
      subBranchChoices.removeActiveItemsByValue(pair[0]);
    }
  });
});


// Row functions
function addRow(desc="", amt="", cash=""){
  let row = document.createElement('tr');
  row.innerHTML =
    `<td><textarea name="description[]" placeholder="Enter description">${desc}</textarea></td>
     <td><input type="number" name="amount[]" value="${amt}" placeholder="Enter amount" step="0.01" min="0"></td>
     <td><select name="cash_type[]">
          <option value="">-- Select --</option>
          <option value="IN" ${cash=="IN"?"selected":""}>Cash In</option>
          <option value="OUT" ${cash=="OUT"?"selected":""}>Cash Out</option>
        </select></td>
     <td>
        <a href="javascript:;" onclick="deleteRow(this)" title="Delete">
          <img src="images/delete.jpg" width="24" height="24"/>
        </a>
     </td>`;
  document.getElementById("tableBody").appendChild(row);
}
function deleteRow(btn){ btn.closest("tr").remove(); }

// Init 5 rows
function initRows(){
  const tbody = document.getElementById("tableBody");
  tbody.innerHTML = "";
  for(let i=0;i<5;i++){ addRow(); }
}
initRows();

// Reset form function
function resetForm(){
  document.getElementById("expensesForm").reset();
  initRows();
  subBranchChoices.clearStore();
  return false;
}

// Save to localStorage
function preSave(){
  const data = {
    branch: branchSelect.value,
    sub_branch: subBranchChoices.getValue(true),
    category: catSelect.value,
    sub_category: document.getElementById("sub_category_id").value,
    opening_balance: document.getElementById("opening_balance").value,
    rows: []
  };

  document.querySelectorAll("#tableBody tr").forEach((tr)=>{
    const desc = tr.querySelector("textarea").value;
    const amt = tr.querySelector("input").value;
    const cash = tr.querySelector("select").value;
    if(desc || amt || cash){
      data.rows.push({description:desc, amount:amt, cash_type:cash});
    }
  });

  let existing = JSON.parse(localStorage.getItem("expensesData")||"[]");
  data.id = Date.now();
  existing.push(data);
  localStorage.setItem("expensesData", JSON.stringify(existing));

  alert("Saved Successfully!");
  window.location.href = "expenses.php";
  return false;
}
</script>
