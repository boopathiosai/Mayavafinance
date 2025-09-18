
var count = 0;
$(document).ready(function() {

        $('[name=customer_name]').autocomplete(customer_names,{     
                                                                    formatItem: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatMatch: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatResult: function(row){
                                                                        return row.name;
                                                                    }

        });

        $("[name=customer_name]").result(function(event, data, formatted) {     
        $("[name=pval[customer_id]]").val(data["id"]);
        $("#cust_name").attr("disabled", "disabled");
        $("#cust_edit").html('<a href="#" onclick="edit_cust();"><img src="images/edit.jpg" style="height:15px; width:15px"></a>');
        customer_details(data["id"]);
        });
/*
        $('[name=product_name[]]').autocomplete(product_names,{
                                                                    formatItem: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatMatch: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatResult: function(row){
                                                                        return row.name;
                                                                    }
        });

        $("[name=product_name]").result(function(event, data, formatted) {
        $("#name_0").val(data["id"]);
        });
*/
        $("input,select,textarea").not( $(":button,:hidden") ).keypress(function (evt) {
            if (evt.keyCode == 13) {
                iname = $(this).val();
                if (iname !== 'Submit'){    
                    var fields = $(this).parents('form:eq(0),body').find('button,input:text,input:radio,input:checkbox,textarea,select');                   
                    
                    var index = fields.index( this );
                    if ( index > -1 && ( index + 1 ) < fields.length) {
                        fields.eq( index + 1 ).focus();
                    }
                    else
                    {               
                      product_pledge_process('content_div','<?php echo $uval[id]; ?>','<?php echo $page_no; ?>','<?php echo $search_args; ?>');
                    }
                    return false;
                }
            }
        });

});

function product_auto(){
        
        var category_name_len = jQuery('[name="category_id[]"]').length;
        category_name_len =category_name_len-1;
        var category_name_value = $("#cat_id_"+category_name_len).val();

        var product_names_flt = new Object();
        //alert( product_names[1].category_id);
        for(var k=0;k<product_names.length;k++){
            //alert(product_names[k].category_id);
            if(parseInt(category_name_value) == parseInt(product_names[k].category_id)){
                
                
                product_names_flt = product_names[k];
                //alert(product_names_flt[k].name);
            }
        }
        alert(product_names_flt.length);
        
        $('[name=product_name[]]').autocomplete(product_names_flt,{
                                                                    formatItem: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatMatch: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatResult: function(row){
                                                                        return row.name;
                                                                    }
        });

        $("[name=product_name]").result(function(event, data, formatted) {
        $("#name_0").val(data["id"]);
        });

}
function append_row()
{  
    //validate_product_pledge();
if(!validate_product_pledge()){
return false;
}
var dropdown;   
//  var dropdown = $("#cat1").html();
    //alert(dropdown);  
    //return false;
    //var select_option;
    //var select_options = '<?php echo $category_options; ?>';  


    resultdata=$.ajax({
          type: "GET",
              async: false,
           url: "product_pledge.php?type=category_dropdown",           
           data:{
                id : "",
                pageno : "",
                mod : "Ajax"
                
           }

    }).responseText;
    
    dropdown=resultdata;    
    count++;
    var category_select = "<select name='category_id[]' id='cat_id_"+count+"'><option value=''>-- Select Category --</option>"+dropdown+"</select>";
    var product_elements = "<tr width='100%'><td width='10%' align='center' >"+category_select+"</td><td width='30%' align='center'><input class='textfield-l' onkeypress='product_auto();' name='product_name[]' id='name_"+count+"' size='20' type='text'></td><td width='20%' align='center'><input class='textfield-s' name='gross_weight[]' id='gross_weight_"+count+"' size='10' type='text' onkeypress='return decimalformat(event,this,8,2);' onchange='gross_total();' ></td><td width='10%' align='center'><input class='textfield-s' name='net_weight[]' id='net_weight_"+count+"' size='10' type='text' onkeypress='return decimalformat(event,this,8,2);' onchange='net_total();' ></td><td width='20%' align='center'><input class='textfield-s' name='market_rate[]' id='market_rate_"+count+"' size='10' type='text' onkeypress='return decimalformat(event,this,10,2);' ></td><td width='10%' align='center'><a href='#' class='remove_row' ><img src='images/pledge-edit.jpg' title='delete'/></a></td></tr>";    
    $("#product_content_table").last().append(product_elements);    
    var item_count = parseInt($("#item_count").val())+1;
    $("#item_count").attr("value",item_count);

    $('#name_'+count).autocomplete(product_names,{
                                                                    formatItem: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatMatch: function(row, i, max){
                                                                        return row.name;
                                                                    },
                                                                    formatResult: function(row){
                                                                        return row.name;
                                                                    }
        });

    $('#name_'+count).result(function(event, data, formatted) {
    //  $('#name_'+count).val(data["name"]);
        });
        
}

$('.remove_row').live('click',function(){  
  if(confirm("Are you sure you want to delete this item?")==true){  
  $(this).closest('tr').remove();
  gross_total();
  net_total();
    var count=$("#item_count").val();
    var item_count = count-1;
    count=count-1;
    $("#item_count").attr("value",item_count);
  }
  return false;
});

function scheme_details()
{

    var scheme_id = $("#sch_id").val();
    $.ajax({
           type: "POST",
           url: "product_pledge.php?type=fetch_scheme_details",
           data:{
                "scheme_id" :scheme_id
                
           },
           success: function(msg){
            result_data = msg.split("~~");
            $("#sch_desc").html(result_data[0]);
            if(result_data[1] == 1){ var stype = "Floating"}
            else{ var stype = "Flat"}
            $("#sch_type").html(stype);

            }

         });

    return false;
}
function cust_validate(){
for(i=0;i <= customer_names.length;i++){
    
    //alert(customer_names[1].name);
    //return false;
    var cust_name= $("[name=customer_name]").val();
    var cust_id =$("[name=pval[customer_id]]").val();
    //if(cust_name == customer_names[i].name){
     if(cust_id == ""){
          // customer_details(customer_names[i].id);
           //remove_red('customer_id');
           alert("Please select the customer name from the list");
           $("[name=customer_name]").val("");
           $("[name=pval[customer_id]]").val("");
           return false;
    }
    /*
    else{
           $("[name=customer_name]").val("");
           $("[name=pval[customer_id]]").val("");
           $("#cust_details").html("");
           $("#cust_img").attr("src","customer/images/no_photo.jpg");
           alert("Please select the customer name");
           return false;
    
    }
    */
}
}
function customer_details(id)
{
    var customer_id = id;
    // if enter key is no pressed after entering the name, then validation that customer name
    /*
    if(customer_id == undefined){
           if($("[name=customer_name]").val() != ""){
           alert("Please select the customer name from the list 1");
           }
           $("[name=customer_name]").val("");
           $("[name=pval[customer_id]]").val("");
           $("#cust_details").html("");
           $("#cust_img").attr("src","customer/images/no_photo.jpg");
        alert("Please select the customer name from the list");
        return false;
    }
    */
    $.ajax({
           type: "POST",
           url: "product_pledge.php?type=fetch_customer_details",
           data:{
                "customer_id" :customer_id
                
           },
           success: function(msg){
           //alert(msg);
           if(msg != 0){
           result_data = msg.split("~~");
            $("#cust_details").html(result_data[2]+"<br>"+result_data[3]+"<br>"+result_data[4]+"<br>"+result_data[5]+"<br>");
            $("#cust_img").attr("src",result_data[0]);
           }
           else{
           $("[name=customer_name]").val("");
           $("[name=pval[customer_id]]").val("");
           $("#cust_details").html("");
           $("#cust_img").attr("src","customer/images/no_photo.jpg");
           alert("Entered customer name doesn't exist!");
           $('#cust_name').focus();

           }
            }

         });

    return false;
}

function cal_total(){
var loan_amt =$('#loan_amt').val();
var service_crg = $('#service_crg').val();
if(loan_amt != ""){
if(service_crg == ""){ service_crg=0;}
var sum =parseInt(loan_amt) + parseInt(service_crg);
}
$("#total_amt_given").html(sum);
$("#total_amt_given_hidden").attr("value",sum);
}

function gross_total(){

        var gross_weight = jQuery('[name="gross_weight[]"]');
        var gross_weight_total;
        gross_weight_total=0;
        var index = gross_weight.length-1;
        var i;
        for(i=0;i<=index;i++){
        gross_weight_total =gross_weight_total + parseInt(gross_weight[i].value);
        }
        $("#gross_wt").attr("value",gross_weight_total);
}
function net_total(){
        var net_weight = jQuery('[name="net_weight[]"]');
        var net_weight_total;
        net_weight_total=0;
        var index = net_weight.length-1;
        var i;
        for(i=0;i<=index;i++){
        net_weight_total =net_weight_total + parseInt(net_weight[i].value);
        }
        $("#net_wt").attr("value",net_weight_total);
}

function edit_cust(){

        $("#cust_name").removeAttr('disabled');
        $("#cust_edit").html('');
           $("[name=customer_name]").val("");
           $("[name=pval[customer_id]]").val("");
           $("#cust_details").html("");
           $("#cust_img").attr("src","customer/images/no_photo.jpg");
          // alert("Entered customer name doesn't exist!");
           $('#cust_name').focus();

}
function validate_product_pledge(){
    //$("name=product_name[]").val();
    var category_name = jQuery('[name="category_id[]"]');
    var product_name = jQuery('[name="product_name[]"]');
    var gross_weight = jQuery('[name="gross_weight[]"]');
    var net_weight = jQuery('[name="net_weight[]"]');
    var net_index = net_weight.length-1;
    var gross_index = gross_weight.length-1;
    var prod_index = product_name.length-1;
    var cat_index = category_name.length-1;
     if( (net_weight[net_index].value == "") || (gross_weight[gross_index].value == "") || (product_name[prod_index].value == "") || (category_name[cat_index].value == ""))
    {
        alert("Please enter the pledge details");
        return false;
    }

return true;
}