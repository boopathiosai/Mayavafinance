
function new_pro_txtbox_for_autocomplete(cat_id){

    product_remove_red();
    id = cat_id.replace("cat_id_","");

    $("#pname_"+id).val("");
    if($("#"+cat_id).val()!="")
        $("#pname_"+id).attr("disabled",false);
    else
        $("#pname_"+id).attr("disabled",true);
    $("#name_"+id).val("");

    $('#product_more_link_'+id).attr("title","Add More Details:[With: , Without: ]");
    $('#product_more_link_'+id).html("");

    callAjaxRequest("list_details.php", "page=product_auto_list&ind="+id+"&category_id="+$("#cat_id_"+id).val(), "null", "POST", "script", false,false);


}

function find_product_id(pro_id)
{	    
	//id = pro_id.replace("name_","");	
	//alert(pro_id); return false;
    $.ajax({
           type: "POST",
           url: "product_pledge.php?type=find_product_id",
           data:{
                "product_id": pro_id
           },
           success: function(msg){
				if(msg == '0'){
					$("#name_"+id).val("");
				}
          }
         });

    return false;
}

function product_remove_red(){
    remove_red('category_id');
    remove_red('product_name');
    remove_red('gross_weight');
    remove_red('net_weight');
    remove_red('product_items');
}

function product_auto(name,ids){
    //alert(name);
	
	//return false;
    product_remove_red();
    
    search = $("#"+name).val();
    id = name.replace("pname_","");

    if($('#name_'+id).val()!="" || ($('#name_'+id).val()=="" && $('#pname_'+id).val()!="")){

        $('#name_'+id).val("");
        $('#product_with_'+id).val("");
        $('#product_without_'+id).val("");

        $('#product_with_'+id).attr("readonly",false);
        $('#product_without_'+id).attr("readonly",false);
    }
    //jalert($('#pname_'+id).val());
    
    if($('#pname_'+id).val()==""){
        
        $('#name_'+id).val("");
        
        
        $('#product_with_'+id).val("");
        $('#product_without_'+id).val("");

        $('#product_with_'+id).attr("readonly",false);
        $('#product_without_'+id).attr("readonly",false);
    }
    
    if($('#pname_'+id).val()!=""){

    }
    
    
}



function append_row(module)
{  
   var dropdown; 
   var dropdownprename; 
   var dropdownname;
   
   resultdata=$.ajax({
          type: "GET",
              async: false,
           url: module+".php?type=category_dropdown",           
           data:{
                id : "",
                pageno : "",
                mod : "Ajax"

           }

    }).responseText;

    dropdown=resultdata;    
    var category_name = jQuery('[name="category_id[]"]');
	
	
	predata=$.ajax({
          type: "GET",
              async: false,
           url: module+".php?type=prename_dropdown",           
           data:{
                id : "",
                pageno : "",
                mod : "Ajax"

           }

    }).responseText;

    pre_dropdown=predata;   
	var pre_name = jQuery('[name="pre_name_id[]"]');
	
	
	namedata=$.ajax({
          type: "GET",
              async: false,
           url: module+".php?type=name_dropdown",           
           data:{
                id : "",
                pageno : "",
                mod : "Ajax"

           }

    }).responseText;

    name_dropdown=namedata;   
	var name = jQuery('[name="name_id[]"]');


    count = pledgeProductCount;
	
    var category_select = "  <select name='category_id[]' onchange='new_pro_txtbox_for_autocomplete(this.id);' id='cat_id_"+count+"' style='width:100px;'><option value=''>-- Select Category --</option>"+dropdown+"</select>";
	
	var prename_select = "  <select name='pre_name_id[]' id='pre_name_id_"+count+"' style='width:100px;'><option value=''>-- Select Category --</option>"+pre_dropdown+"</select>";
	
	var name_select = "  <select name='product_id[]' id='product_id_"+count+"' style='width:100px;'><option value=''>-- Select Category --</option>"+name_dropdown+"</select>";
	/*
    var product_elements = "<tr width='100%'><td width='10%' align='center' >"+category_select+"</td><td width='30%' align='center'><span id='pro_txt_"+count+"' class='prod_txt_bld'><input class='textfield-l productlist' name='product_name[]' disabled id='pname_"+count+"' disable onkeypress='javascript:convertThis(event); product_auto(this.id);' size='20' type='text' class='converters' charset='utf-8' onkeydown='toggleKBMode(event)'><input name='product_id[]' class='prod_id_hdn' id='name_"+count+"' size='20' type='hidden'  value='' ></span></td><td width='10%' id='product_more_with_"+count+"' align='center'><input class='textfield-s' name='product_with[]' id='product_with_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,1)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td id='product_more_without_"+count+"'><input class='textfield-s' name='product_without[]' id='product_without_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,0)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td width='10%' align='center'><input name='product_items[]' class='currency textfield-s productlist' id='product_items_"+count+"' size='10' type='text' onchange='item_total();'  onfocus='product_remove_red()' ></td><td width='10%' align='center'><a href='#' class='remove_row' id='"+count+"' ><img src='images/pledge-edit.jpg' title='delete'/></a></td></tr>"; */

		/* var product_elements = "<tr width='100%'><td width='8%' align='center' >"+category_select+"</td><td width='8%' align='center'>"+prename_select+"</td><td width='20%' align='center'>"+name_select+"</td><td width='5%' id='product_more_with_"+count+"' align='center'><input class='textfield-s' name='product_with[]' id='product_with_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,1)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td id='product_more_without_"+count+"'><input class='textfield-s' name='product_without[]' id='product_without_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,0)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td width='10%' align='center'><input name='product_items[]' class='currency textfield-s productlist' id='product_items_"+count+"' size='10' type='text' onchange='item_total();'  onfocus='product_remove_red()' ></td><td width='10%' align='center'><a href='#' class='remove_row' id='"+count+"' ><img src='images/pledge-edit.jpg' title='delete'/></a></td></tr>"; */


var product_elements = "<tr width='100%'><td width='8%' align='center' >"+category_select+"</td><td width='8%' align='center'>"+prename_select+"</td><td width='30%' align='center'><span id='pro_txt_"+count+"' class='prod_txt_bld'><input class='textfield-l productlist' name='product_name[]' disabled id='pname_"+count+"' disable onkeypress='javascript:convertThis(event); product_auto(this.id);' size='20' type='text' class='converters' charset='utf-8' onkeydown='toggleKBMode(event)'><input name='product_id[]' class='prod_id_hdn' id='name_"+count+"' size='20' type='hidden'  value='' ></span></td><td width='5%' id='product_more_with_"+count+"' align='center'><input class='textfield-s' name='product_with[]' id='product_with_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,1)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td id='product_more_without_"+count+"'><input class='textfield-s' name='product_without[]' id='product_without_"+count+"' type='text' value =''  onchange='set_new_product("+count+",this.value,0)' class='converters' charset='utf-8' onkeypress='javascript:convertThis(event)' onkeydown='toggleKBMode(event)'></td><td width='10%' align='center'><input name='product_items[]' class='currency textfield-s productlist' id='product_items_"+count+"' size='10' type='text' onchange='item_total();'  onfocus='product_remove_red()' ></td><td width='10%' align='center'><a href='#' class='remove_row' id='"+count+"' ><img src='images/pledge-edit.jpg' title='delete'/></a></td></tr>";		

    $("#product_content_table").last().append(product_elements);   

    pledgeProductCount++;

    var product_count = parseInt($("#product_count").val())+1;
    $("#product_count").attr("value",product_count);
    
    setNumberKeyEvent();
    
    resetEnterNext();        

}



$('.remove_row').live('click',function(event){  

    if(confirm("Are you sure you want to delete this item?")==true){    

    var product_count = $("#product_count").val();
    product_count=product_count-1;

    $("#product_count").attr("value",product_count);

    id = this.id.replace("in_items_","");

    row_item = jQuery('#product_items_'+id).val(); 
    row_item = parseInt(row_item);  

    count=0;

    $(this).closest('tr').remove();
    item_total();
    }
    return false;
});


function scheme_rate_display(stype,loan_amt,srate){

    if(!isNaN(parseFloat(loan_amt))) loan_amt = parseFloat(loan_amt); 
    else loan_amt=0;
    
    if(!isNaN(parseFloat(srate))) srate = parseFloat(srate); 
    else srate=0;
    
    
    if(loan_amt == 0 ){

        if(stype.toLowerCase()=="flat"){
            $("#ser_charge").html("<span class='disp_calc'>  Rs. "+srate+" per 1000s Loan Amount Rs. "+parseFloat(loan_amt).toFixed(2)+" </span>");
        }else{
            $("#ser_charge").html("<span class='disp_calc'>  "+srate+" % of Loan Amount Rs. "+parseFloat(loan_amt).toFixed(2)+" </span>");
        }
        ser_amt = 0;
        sum = 0;
        
    }else{
        
        if(stype.toLowerCase()=="flat"){
            serice_amt = ( loan_amt / 1000 ) * srate;
            ser_amt = roundServiceAmount(serice_amt);   
            $("#ser_charge").html("Rs. "+ser_amt.toFixed(2)+" - <span class='disp_calc'> [ Rs. "+parseFloat(srate).toFixed(2)+" per 1000s Loan Amount Rs. "+loan_amt.toFixed(2)+" ]</span>");
        }else{
            serice_amt = (loan_amt * srate) /100;
            ser_amt = roundServiceAmount(serice_amt);           
            $("#ser_charge").html("Rs. "+ser_amt.toFixed(2)+" - <span class='disp_calc'> [ "+srate+" % of Loan Amount Rs. "+loan_amt.toFixed(2)+" ]</span>");
        }
        
        sum = loan_amt - ser_amt;
    }
    
        sum_rnd = Math.round(sum);
        
        $("#service_crg").attr("value",ser_amt.toFixed(2));
        $("#service_rate").attr("value",srate);
        
        
    disp_total = "<table class='disp_calc' width=95% align=center>";
    disp_total += "<tr><td> </td><td class='disp_calc'>Loan Amount  :</td> <td align=right class='disp_calc'>"+loan_amt.toFixed(2)+"</td><td> </td><td> - </td></tr>";
    disp_total += "<tr><td> </td><td class='disp_calc'>Service Charge (Rounded):</td> <td align=right class='disp_calc'>"+ser_amt.toFixed(2)+"</td><td> </td><td> </td></tr>";
    disp_total += "<tr><td> </td><td class='disp_calc'>To Give :</td> <td align=right class='disp_calc'>"+sum.toFixed(2)+"</td><td> </td><td> </td></tr>";
    disp_total += "<tr><td> </td><td>To Give (Rounded) :</td> <td colspan=3 align=right><b><font size=+1 color='#a33d98'>Rs. "+sum_rnd.toFixed(2)+"</font></b></td></tr>";
    disp_total += "</table>";
        
    $("#tot_amt_given").html(disp_total);
    $("#total_amt").attr("value",sum_rnd.toFixed(2));

}


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

            //alert(msg);
            if(result_data[0] != ""){
                $("#sch_desc").html(result_data[0]);
            }else{
                $("#sch_desc").html("-");
            }
            if(result_data[1] == 1){ var stype = "Floating"}
            else{ var stype = "Flat"}
            $("#sch_type").html(stype);
            $("#scheme_type").val(stype);



            var ser_decimal_val= result_data[2];

            loan_amt = $("#loan_amt").val();
            
            scheme_rate_display(stype,loan_amt,ser_decimal_val);


            var sch_roi=parseFloat(result_data[3]);
            
            $("#sch_roi").html(sch_roi.toFixed(2));

            $("#loan_rate").attr("value", sch_roi.toFixed(2));

            if(scheme_id!=''){
                $("#loan_amt").attr("disabled",false);
            }
            else{
                $("#loan_amt").attr("disabled",true);
            }

            cal_total();
        
        }
     });

    return false;
}

function sub_branch_code()
{
	
    var sub_id = $("#sub_id").val();
    $.ajax({
           type: "POST",
           url: "product_pledge.php?type=fetch_sub_branch_details",
           data:{
                "sub_id": sub_id
                
           },
           success: function(msg){
		   //alert(msg);
           //result_data = msg.split("~~");
		  //$('#val').val(sub_id);
		  //if(sub_id!=''){
		
		  if(sub_id != ""){
			$("#sub1_loan_id").val(msg);
		    $("#sub_loan_id").html(msg);
		  }
		  //}

		   //jQuery("#content_div").html(msg);

            
            }

         });

    return false;
}


function cust_validate(){

    for(i=0;i <= customer_names.length;i++){

        var cust_name= $("[name=customer_name]").val();
        var cust_id =$("[name='pval[customer_id]']").val();
        if(cust_id == ""){
               alert("Please select the customer name from the list");
               $("[name=customer_name]").val("");
               $("[name='pval[customer_id]']").val("");
               return false;
        }
    }
}



var imgExist = true;

function imgExistStatus(status){
    imgExist = status;
}

function customer_details(id)
{
var customer_id = id;
$.ajax({
       type: "POST",
       dataType : 'script',
       url: "list_details.php?page=fetch_customer_details",
       data:{
            "customer_id" :customer_id

       },
       success: function(msg){

           //eval(msg);


           if(count>0){

                $("[name=customer_name]").attr("value",content.name);
                $("[name=customer_id]").attr("value",content.customer_id);
                $("[name='pval[customer_id]']").attr("value",content.id);
                $("#cust_details").html(content.street+"<br>"+content.location+"<br>"+content.district+"<br>"+content.state+"<br>"+content.postal_code+"<br>"+content.contact_no+"<br>");

                if(view_status)
                    $("#cust_history").html("<br><a href='javascript:' class='button' onclick='view_history(\""+content.id+"\")'>History</a>");
                else
                    $("#cust_history").html("<br><a href='javascript:' class='button-off' onclick=\"jalert(this.title)\" title='No Privilege to view history for this customer'>History</a>"); 

                $("#ref_name").attr("value",content.reference_name);
                $("#ref_add").attr("value",content.reference_address.replace(/<!--NL-->/i,"\n"));
                
                $("#cust_img").attr("src",content.photo);
                $("#cust_sign").attr("src",content.sign);


           }else{
               $('#cust_name').attr("disabled",false);
               $('#cust_name').attr("readonly",false);
               $("[name=customer_name]").val("");
               $("[name='pval[customer_id]']").val("");
               $("[name=customer_id]").attr("value","");
               $("#cust_details").html("");
               $("#cust_history").html("");
                $("#cust_img").attr("src","customer/images/no_photo.jpg");
                $("#cust_sign").attr("src","customer/images/no_sign.jpg");
               jalert("Entered customer name doesn't exist!");
               
                $("#ref_name").attr("value","");
                $("#ref_add").attr("value","");
               
               //$('#cust_name').focus();
               $("[name=customer_id]").focus();
           }
       
       
        }

     });

return false;
}

function edit_cust(id)
    {
         $("#cust_name").removeAttr('disabled');
    $("#cust_edit").html('');
       $("[name=customer_name]").val("");
       $("[name='pval[customer_id]']").val("");
       $("#cust_details").html("");
       $("#cust_img").attr("src","customer/images/no_photo.jpg");
       $("#cust_sign").attr("src","customer/images/no_sign.jpg");
      // alert("Entered customer name doesn't exist!");
      $("#cust_edit").html('<a href="#" onclick="reset_cust(\''+id+'\');" title="Reset to old customer name"><img src="images/delete.jpg" style="height:15px;width:15px"></a>');
      $('#cust_name').focus();

}

function reset_cust(id){
    var id_reset = $("#for_reset").val();
    if( id_reset !=""){
    id = id_reset;
    }

    customer_details(id);
    //$("#cust_name").attr("readonly",true);
    $("#cust_edit").html('<a href="#" onclick="edit_cust(\''+id+'\');" title="Edit customer name"><img src="images/edit.jpg" style="height:15px;width:15px"></a>');
    

}




function cal_total(){
    
    if(!isNaN(parseFloat($('#loan_amt').val()))) 
        loan_amt = parseFloat(parseFloat($('#loan_amt').val())); 
    else 
        loan_amt = 0;
    
    if(!isNaN(parseFloat($('#loan_rate').val()))) 
        loan_rate = parseFloat(parseFloat($('#loan_rate').val())); 
    else 
        loan_rate = 0;
        
    if(!isNaN(parseFloat($('#net_wt').val()))) 
        net_wt = parseFloat(parseFloat($('#net_wt').val())); 
    else 
        net_wt = 0;
        
    if(!isNaN(parseFloat($('#gross_wt').val()))) 
        gross_wt = parseFloat(parseFloat($('#gross_wt').val())); 
    else 
        gross_wt = 0;
    
    
    
    /*loan_rate = parseFloat($("#loan_rate").val());
    gross_wt = parseFloat($("#gross_wt").val());
    net_wt = parseFloat($("#net_wt").val());*/
    
    max_avail_loan = loan_rate * net_wt;
    //alert(net_wt +"  "+max_avail_loan);
    if(loan_amt > max_avail_loan)
    {
        msg = "Your pledged product's net weight is <b>"+net_wt.toFixed(2)+"</b> grams <br>";
        msg += "Your selected scheme loan rate is Rs.<b>"+loan_rate.toFixed(2)+"</b> per gram <br>";
        msg += "So your maximum loan amount to claim is Rs.<b>"+max_avail_loan.toFixed(2)+"</b> <br>";
        msg += "Your claimed loan amount is Rs.<b>"+loan_amt.toFixed(2)+"</b> <br>";
        msg+= "<b><i>This amount exceed the maximam loan amount limits of Rs."+max_avail_loan.toFixed(2)+"<br>Please change scheme or add some more products to increase net weight</b></i>";
        jalert(msg,'error');
        
        $('#loan_amt').val(max_avail_loan.toFixed(2));
        
        loan_amt = max_avail_loan;
        //alert(loan_amt);
        
        product_remove_red();
        //list_details('scheme_detail','scheme_details','id='+jQuery("#sch_id").val()+'&loandate='+jQuery("#loan_dt").val()+'&loanamount='+jQuery("#loan_amt").val());
        //return false;
    }  
    
    int_amount =  parseInt(loan_amt/100);
    dec_val =  Math.round(((loan_amt/100) - int_amount) * 100);

    
    
    if(dec_val != 0 && dec_val != 50 ){

        if(dec_val <= 25){

            dec_val = 0;

        }else if(dec_val > 25 && dec_val <= 50){

            dec_val = 50;

        }else if(dec_val > 50 && dec_val <= 75){

            dec_val = 50;

        }else if(dec_val > 75){

            dec_val = 100

        }

        new_amt = (int_amount  * 100) + dec_val;
    
    
        msg = "<b><i>Loan amount should be rounded of 50s or 100s</i></b><br><br>";
        msg += "Your entered Loan amount is Rs.<b>"+loan_amt.toFixed(2)+"</b><br>";
        msg += "Rounded loan amount is Rs.<b>"+new_amt.toFixed(2)+"</b> <br>";

        jalert(msg,'alert');
        
        $('#loan_amt').val(new_amt.toFixed(2));
        
        loan_amt = new_amt;
        //alert(loan_amt);
        

    
    
    }
    
    var service_rate = $('#service_rate').val();
    var stype = $('#scheme_type').val().toLowerCase();
    //if(service_rate == ""){ service_rate=0;}else{ service_rate = parseFloat(service_rate); }
    //if(loan_amt == ""){ loan_amt=0;}else{ loan_amt = parseFloat(loan_amt); }
    //alert(stype+" @ "+loan_amt +" @ "+ser_decimal_val);
    scheme_rate_display(stype,loan_amt,service_rate);    



    list_details('scheme_detail','scheme_details','id='+jQuery("#sch_id").val()+'&loandate='+jQuery("#loan_dt").val()+'&loanamount='+jQuery("#loan_amt").val());
    

    cashin_display();
    cashout_display();
    loan_service_charge_display();


}


function CurrencyFormatted(amount)
{
    var i = parseFloat(amount);
    if(isNaN(i)) { i = 0.00; }
    var minus = '';
    if(i < 0) { minus = '-'; }
    i = Math.abs(i);
    i = parseInt((i + .005) * 100);
    i = i / 100;
    s = new String(i);
    if(s.indexOf('.') < 0) { s += '.00'; }
    if(s.indexOf('.') == (s.length - 2)) { s += '0'; }
    s = minus + s;
    return s;
}

function gross_total(){

    var gross_weight = jQuery('[name="gross_weight[]"]');
    var gross_weight_total;
    gross_weight_total=0;
    var index = gross_weight.length-1;
    var i;
    for(i=0;i<=index;i++){
        if(!isNaN(parseFloat(gross_weight[i].value))){
            gross_weight_total =gross_weight_total + parseFloat(gross_weight[i].value);
        }
    }
    var gross_weight_total_result = parseFloat(gross_weight_total).toFixed(2);
    $("#gross_wt").attr("value",parseFloat(gross_weight_total_result).toFixed(2));
}


function item_total(){

    var product_items = jQuery('[name="product_items[]"]');
    var item_count;
    item_count=0;
    var index = product_items.length-1;
    var i;
    for(i=0;i<=index;i++){
        if(!isNaN(parseFloat(product_items[i].value))){
            item_count =item_count + parseFloat(product_items[i].value);
        }
    }
    product_remove_red();
    //item_count = parseFloat(item_count).toFixed(2);
    $("#item_cnt").attr("value",parseInt(item_count));
}


function net_total(){
    
    var net_weight = jQuery('[name="net_weight[]"]');
    var weights = jQuery('[name="weights"]');
    
    var initial_weight = jQuery('#initial_weight').val();
    if(initial_weight=='') initial_weight = 0; else initial_weight = parseFloat(initial_weight);
    
    var weight_out = initial_weight;
    
    var net_weight_total;
    net_weight_total=0;
    var index = net_weight.length-1;
    var i;
    
    for(i=0;i<=index;i++){
        
        if(!isNaN(parseFloat(net_weight[i].value))){
            net_weight_total = net_weight_total + parseFloat(net_weight[i].value);
        }
        
        if(typeof weights[i] != "undefined"){
        if(!isNaN(parseFloat(weights[i].value))){
            weight_out = weight_out - parseFloat(weights[i].value);
        }
        }
    
    }

    //alert(net_weight_total + "   " + initial_weight);
    
    weight_in = net_weight_total - (initial_weight - weight_out);
    if(weight_in<0) weight_in = 0;
    var net_weight_total_result = parseFloat(net_weight_total).toFixed(2);
    
    $("#net_wt").attr("value",parseFloat(net_weight_total_result).toFixed(2));
    $("#weight_in").attr("value",parseFloat(weight_in).toFixed(2));
    $("#weight_out").attr("value",parseFloat(weight_out).toFixed(2));
    
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
    alert("Please enter the pledge details before adding a another row");
    return false;
}

return true;
}



function product_validate(){
    //alert(name);
    //var category_name_value = $("#cat_id_"+id).val();
    var category_name_len = jQuery('[name="category_id[]"]').length;
    category_name_len =category_name_len-1;
    var category_name_value = $("#cat_id_"+category_name_len).val();
    //alert(category_name_value);
    var  pro_name = $("#pname_"+category_name_len).val();
    //alert(pro_name);
    for(var k=0;k<product_names.length;k++){            
        if(parseInt(category_name_value) == parseInt(product_names[k].category_id)){                        
            //product_names_flt.push({name: product_names[k].name, id: product_names[k].id, category_id:product_names[k].category_id});


            if(pro_name == product_names[k].name){

                return true;
            }

        }

    } 
    $("#pname_"+category_name_len).val("");
    $("#pname_"+category_name_len).focus();

    alert("Product name doesn't exist please select the data from the list");

    return false;


}

function roundVal(val){
var dec = 2;
var result = Math.round(val*Math.pow(10,dec))/Math.pow(10,dec);
return result;
}

function auto_index_id(){
    var pid=0;
    var cid=0;
    var spid=0;
    var phid=0;
    
   $('[name=product_name[]]').each(function () {       
       $('[name=product_name[]]').attr("id","pname_"+pid);
       pid++;
   });
   
   $('[name=category_id[]]').each(function () { 
   
       $('[name=category_id[]]').attr("id","cat_id_"+cid);
       //$('[name=category_id[]]').unbind("change");
       //$('[name=category_id[]]').bind("change", function(event){new_pro_txtbox_for_autocomplete(cid);});
       
       cid++;
   });
   
   $('.prod_txt_bld').each(function () {       
       $('.prod_txt_bld').attr("id","pro_txt_"+spid);
       spid++;
   });
   

   $('.prod_id_hdn').each(function () {      
       $('.prod_id_hdn').attr("id","name_"+phid);
       phid++;
   });
}
