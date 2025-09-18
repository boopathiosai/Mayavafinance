 function show_interest_status(content,val)
{
    //alert($("#interest_message").html());
    if(val == 0){
        var color='#990000';
        var back_color ="#FFEAEA";
        var border_color ="2px solid #990000";
    }
    if(val == 1){
        var color='#446423';
        var back_color ="#EFF5EA";
        var border_color ="2px solid #446423 ";
    }

//  $(".header-floating-message-querybuilder").slideUp();
    //$("#interest_message").css({ border:border_color});
    $("#interest_message").css({ backgroundColor:back_color});
    $("#interest_message").css({ float:'left'});
    $("#interest_message").css("color",color);
    $("#interest_message").hide();
    $("#interest_message").html('<center>'+content+'</center>');
    $("#interest_message").slideDown(1000);
    $("#interest_message").fadeTo(1000, 5);
    $("#interest_message").slideUp(1000);
    //$(".header-floating-message-querybuilder").slideDown(6000);
}




function module_checkall(mod_class,chk)
{
        //remove_red('module_category');
        remove_red('module_pages');

 jQuery("."+mod_class).each(
 function(i){
    this.checked = chk;
 }
 );

}


function module_check_reset(chk_name, module_cat)
{
        //remove_red('module_category');
        remove_red('module_pages');

      flag=0;
      //frm = document.getElementById(myform);
      selall = document.getElementById('selall_'+module_cat);
      element = jQuery("."+module_cat+"_privilegechk").get();
      elem_count = element.length;
 for(i=0;i<elem_count;i++)
 {
   if(element[i].name == chk_name && element[i].type=="checkbox" && element[i].checked==false)
   {
       if(element[i].disabled==false){
        flag=1;
        break;
       }
   }
 }

 if(flag==0)
     selall.checked=true;

 else
  selall.checked=false;

 /*ischecked = true;
  jQuery("."+module_cat+"_privilegechk").each(
  function(i){
     if(this.checked)  ischecked = false;
  }
 );

 alert(ischecked);
 jQuery("#selall_"+module_cat).attr('checked',ischecked);*/


}






// All Product Transaction common Functions
function setPrinceple(chk){
    //alert(chk)
    if(chk){
        jQuery("#principle_amt").attr("disabled",false);
        pamt = parseFloat(jQuery("#principle_amt").val());
        if(pamt < 100){
            jQuery("#principle_amt").val(100);
            //jalert("Minimum principle amount is Rs.100");
        }
    }
    else{
        jQuery("#principle_amt").attr("disabled",true);
        jQuery("#principle_amt").val(0.00);
    }
    //setTotalAmount();
}


//Module Function call for Cash Flow Auto Complete
function cashflow_auto_list(content,args){
    //$("[name=pval[cash_purpose]]").flushCache();
	$("#cash_purpose1").flushCache();

    cashflow_names = content;

    $('#cash_purpose1').autocomplete(cashflow_names,{
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

    $("#cash_purpose1").result(function(event, data, formatted) {

    });

}


function cashflow_fetch_puposes(type){

    /*Changes for the cashin and cashout purpose*/

    if(type!='')
    {
      $("#cash_transaction_type").hide();
      $("#cash_purpose1").attr("disabled", false);
      callAjaxRequest("list_details.php", "page=cashflow_auto_list&cash_type="+type, "null", "POST", "script", false,false);
    }
    else
    {
        $("#cash_purpose1").attr("disabled", true);
        $("#cash_transaction_type").show();
        $("#cash_purpose1").val("");
    }
}

// Module Functioins Call By Common Ajax Function From the Function "module_page_action"
function customer_auto_list(content, args){
    $("[name=customer_name]").flushCache();
    customer_names = content;

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
        $("[name='pval[customer_id]']").val(data["id"]);
        $("#cust_name").attr("disabled", "disabled");
        $("#cust_edit").html('<a href="#" onclick="edit_cust('+data["id"]+');" title="Edit customer details"><img src="images/edit.jpg" style="height:15px; width:15px"></a>');
        customer_details(data["id"]);
    });

}




function set_new_product(id,val,flag){
    if(flag==0) type = "without";
    else if(flag==1) type = "with";

    //try{ eval("oldvalue = product_details_"+id+"."+type+";"); } catch(e){ oldvalue = '';}
    //eval("oldvalue = product_details_"+id+"."+type+";");

    //if(oldvalue != val)
    {
        //alert("Inside - New Setup enabled");
        $('#name_'+id).val("");
    }

}

function product_auto_list(content, args){

    $('#pname_'+args.id).flushCache();
    product_names = content;
    /*alert(args.id);*/


    $('#pname_'+args.id).autocomplete(product_names,{
            formatItem: function(row, i, max){
                return row.details;
            },
            formatMatch: function(row, i, max){
                return row.details;
            },
            formatResult: function(row){
                return row.details;
            }
        });

    $('#pname_'+args.id).result(function(event, data, formatted) {

            //$('#gross_weight_'+id).focus();
            /*alert("selected");*/
            $('#pname_'+args.id).val(data["name"]);
            $('#name_'+args.id).val(data["id"]);

            $('#product_with_'+args.id).attr("value",data["product_with"]);
            $('#product_without_'+args.id).attr("value",data["product_without"]);

            eval("product_details_"+args.id+" = { 'without' : '"+data["product_without"]+"', 'with': '"+data["product_with"]+"' };");

            /*$('#product_with_'+args.id).attr("readonly",true);
            $('#product_without_'+args.id).attr("readonly",true);*/

            $('#product_items_'+args.id).focus();

          //***** link Function enable / disable
          //$('#product_more_link_'+args.id).attr("title","More Details:[With:"+data["product_with"]+" , Without:"+data["product_without"]+" ]");
          //$('#product_more_link_'+args.id).html("View");


          is_resulted = true;
    });



    /*$('#pname_'+args.id).change(function(event, data, formatted) {
      is_resulted = false;
      if(!$('#product_with_'+args.id).attr("readonly"))
        $('#product_with_'+args.id).focus();
      else
        $('#gross_weight_'+id).focus();
    });*/
}




//Created by Karthi on 12/ for client side valisdation

function amount_transfer_client()
{
    //alert("karthik");
    var amt_trans=document.getElementById("amount_transf1").value;
    var amt_req=parseFloat($("#amount_req").html());

    if(amt_trans > amt_req)
        {
              jalert("Invalid amount");
              return false;
        }
     else
        return true;
}

function date_val()
{
    var transfer_date=document.getElementById("tran_date").value;
    var received_date=document.getElementById("received_date1").value;
    var transfer_date=transfer_date.split("-");
    var received_date=received_date.split("-");
    var stransfer_date = new Date(transfer_date[2],transfer_date[1],transfer_date[0]);
    var sreceived_date = new Date(received_date[2],received_date[1],received_date[0]);
    if(stransfer_date > sreceived_date)
    {
        jalert("Invalid received date");
        return false;

    }
    else
        return true;
}


function cheque_transfer_client()
{
   var cheque=/^[0-9]{5,20}$/;
   var amt=document.getElementById("cheque_details1").value;
   //alert(amt);
  /* if(!cheque.test(jQuery("cheque_details1").val()))
    {
        alert("Enter valid cheque number");
        return false;
    }
  else*/
       return true;

}

function editfunc()
{
    jalert("Amount allocated,so you can't edit!");
}
function deletefunc()
{
    jalert("Amount allocated,so you can't delete!");
}


function resetNewAmount()
{

}
function presetNewAmount(){

//alert("inside")

}

function ini_loan_amount_display(module){

    if(!isNaN(parseFloat(jQuery("#customer_amt").val())))
        customer_amt = parseFloat(jQuery("#customer_amt").val());
    else
        customer_amt = 0;

    if(!isNaN(parseFloat(jQuery("#principle_remain").val())))
        old_amt = parseFloat(jQuery("#principle_remain").val());
    else
        old_amt = 0;

    if(!isNaN(parseFloat(jQuery("#interest_amt").val())))
        interest_amt = parseFloat(jQuery("#interest_amt").val());
    else
        interest_amt = 0;

    if(!isNaN(parseFloat(jQuery("#add_amt").val())))
        add_amt = parseFloat(jQuery("#add_amt").val());
    else
        add_amt = 0;

    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    if(module == "renewal"){

        disp_ini_new_amt = "<table class='disp_calc' width=95% align=center>";
        disp_ini_new_amt += "<tr><td>[ </td><td class='disp_calc'>Old Loan Amount/ Principle Remain  :</td> <td align=right class='disp_calc'>"+old_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        if(jQuery("#add_to_renew").attr("checked")){
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Interest Amount  :</td> <td align=right class='disp_calc'>"+interest_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Additional Charge:</td> <td align=right class='disp_calc'>"+add_amt.toFixed(2)+"</td><td>] </td><td>  </td></tr>";
        }
        disp_ini_new_amt += "<tr><td> </td><td>Initial New Loan Amount :</td> <td colspan=3 align=right>Rs. "+ini_loan_amt.toFixed(2)+"</td></tr>";
        disp_ini_new_amt += "</table>";

    }else if(module == "partial_amount_payment"){

        disp_ini_new_amt = "<table class='disp_calc' width=95% align=center>";
        disp_ini_new_amt += "<tr><td>[ </td><td class='disp_calc'>Old Loan Amount/ Principle Remain  :</td> <td align=right class='disp_calc'>"+old_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Interest Amount  :</td> <td align=right class='disp_calc'>"+interest_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Additional Charge:</td> <td align=right class='disp_calc'>"+add_amt.toFixed(2)+"</td><td>] </td><td> - </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Customer Amount :</td> <td align=right class='disp_calc'>"+customer_amt+"</td><td>  </td><td>  </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td>Initial New Loan Amount :</td> <td colspan=3 align=right>Rs. "+ini_loan_amt.toFixed(2)+"</td></tr>";
        disp_ini_new_amt += "</table>";


    }else if(module == "pay_excess_amount"){

        disp_ini_new_amt = "<table class='disp_calc' width=95% align=center>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Old Loan Amount/ Principle Remain  :</td> <td align=right class='disp_calc'>"+old_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Interest Amount  :</td> <td align=right class='disp_calc'>"+interest_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Additional Charge:</td> <td align=right class='disp_calc'>"+add_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Customer Requested Amount :</td> <td align=right class='disp_calc'>"+customer_amt+"</td><td>  </td><td>  </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td>Initial New Loan Amount :</td> <td colspan=3 align=right>Rs. "+ini_loan_amt.toFixed(2)+"</td></tr>";
        disp_ini_new_amt += "</table>";

    }else{

        disp_ini_new_amt = "<table class='disp_calc' width=95% align=center>";
        disp_ini_new_amt += "<tr><td>[ </td><td class='disp_calc'>Old Loan Amount/ Principle Remain  :</td> <td align=right class='disp_calc'>"+old_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Interest Amount  :</td> <td align=right class='disp_calc'>"+interest_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td class='disp_calc'>Additional Charge:</td> <td align=right class='disp_calc'>"+add_amt.toFixed(2)+"</td><td>] </td><td>  </td></tr>";
        disp_ini_new_amt += "<tr><td> </td><td>Initial New Loan Amount :</td> <td colspan=3 align=right>Rs. "+ini_loan_amt.toFixed(2)+"</td></tr>";
        disp_ini_new_amt += "</table>";

    }
    jQuery("#disp_ini_loan_amt").html(disp_ini_new_amt);

}

function service_charge_display(module){

    type = jQuery("#ld_scheme_type").val().toLowerCase();

    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_charge = parseFloat(jQuery("#service_crg").val());
        //ser_charge = roundServiceAmount(service_charge);
    else
        ser_charge = 0;

    if(!isNaN(parseFloat(jQuery("#service_rate").val())))
        service_rate = parseFloat(jQuery("#service_rate").val());
    else
        service_rate = 0;

    if(type=="flat"){
        disp_service = "<table class='disp_calc' width=95% align=center>";
        disp_service += "<tr><td> </td><td class='disp_calc'>( Initial New Loan Amount / 1000 ) :</td> <td align=right class='disp_calc'>"+parseFloat(ini_new_amt/1000).toFixed(2)+"</td><td> </td><td> * </td></tr>";
        disp_service += "<tr><td> </td><td class='disp_calc'>Service Charge per Rs.1000 :</td> <td align=right class='disp_calc'>"+service_rate+"  </td><td>  </td><td>  </td></tr>";
        disp_service += "<tr><td> </td><td>Service Charge :</td> <td colspan=3 align=right>Rs. "+(parseFloat(ini_new_amt/1000) * service_rate).toFixed(2)+"</td></tr>";
        disp_service += "</table>";
    }else{
        disp_service = "<table class='disp_calc' width=95% align=center>";
        disp_service += "<tr><td> </td><td class='disp_calc'>( Initial New Loan Amount / 100 ) :</td> <td align=right class='disp_calc'>"+parseFloat(ini_new_amt/100).toFixed(2)+"</td><td> </td><td> * </td></tr>";
        disp_service += "<tr><td> </td><td class='disp_calc'>Service rate :</td> <td align=right class='disp_calc'>"+service_rate+" % </td><td>  </td><td>  </td></tr>";
        disp_service += "<tr><td> </td><td>Service Charge :</td> <td colspan=3 align=right>Rs. "+(parseFloat(ini_new_amt/100).toFixed(2) * service_rate).toFixed(2)+"</td></tr>";
        disp_service += "</table>";
    }


    jQuery("#disp_service_charge").html(disp_service);
}


function reset_service_charge_display(module){

    type = jQuery("#ld_scheme_type").val().toLowerCase();

    if(!isNaN(parseFloat(jQuery("#actual_loan_amt").val())))
        actual_loan_amt = parseFloat(jQuery("#actual_loan_amt").val());
    else
        actual_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_charge = parseFloat(jQuery("#service_crg").val());
        //ser_charge = roundServiceAmount(service_charge);
    else
        ser_charge = 0;

    if(!isNaN(parseFloat(jQuery("#ld_service_rate").val())))
        service_rate = parseFloat(jQuery("#ld_service_rate").val());
    else
        service_rate = 0;


    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    final_revised = ini_loan_amt + ser_charge; //Rathna
    if(type == 'floating') {
        final_ser_charge = parseFloat((final_revised/100) * service_rate); }
    else{
        final_ser_charge = parseFloat((final_revised/1000) * service_rate);
    }

    final_ser_charge_rnd = roundServiceAmount(final_ser_charge);
    disp_service = "<table class='disp_calc' width=95% align=center>";
    //if(type == 'floating')
        //disp_service += "<tr><td> </td><td class='disp_calc'>( Revised Loan Amount / 1000 ) :</td> <td align=right class='disp_calc'>"+(final_revised/100).toFixed(2)+"</td><td> </td><td> * </td></tr>";
    //else Modified By vinoth

    disp_service += "<tr><td> </td><td class='disp_calc'>( Revised Loan Amount / 1000 ) :</td> <td align=right class='disp_calc'>"+(final_revised/1000).toFixed(2)+"</td><td> </td><td> * </td></tr>";

    disp_service += "<tr><td> </td><td class='disp_calc'>Service Charge per Rs.1000 :</td> <td align=right class='disp_calc'>"+service_rate+"  </td><td>  </td><td>  </td></tr>";
    disp_service += "<tr><td> </td><td>Revised Service Charge :</td> <td colspan=3 align=right>Rs. "+final_ser_charge.toFixed(2)+"</td></tr>";
    disp_service += "</table>";

    final_revised = ini_loan_amt + final_ser_charge_rnd; //Rathna

    //alert(ini_loan_amt +" "+ final_ser_charge_rnd)

    jQuery("#loan_amt").val(final_revised.toFixed(2));
    jQuery("#actual_loan_amt").val(final_revised.toFixed(2));
    disp_actual_amt = "<table class='disp_calc' width=95% align=center>";
    disp_actual_amt += "<tr><td> </td><td class='disp_calc'>Initial Loan Amount :</td> <td align=right class='disp_calc'>"+ini_loan_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
    disp_actual_amt += "<tr><td> </td><td class='disp_calc'>Revised Service Charge (Rounded) :</td> <td align=right class='disp_calc'>"+final_ser_charge_rnd.toFixed(2)+"  </td><td>  </td><td>  </td></tr>";
    disp_actual_amt += "<tr><td> </td><td>Final Revised Loan Amount:</td> <td colspan=3 align=right>Rs. "+final_revised.toFixed(2)+"</td></tr>";
    disp_actual_amt += "</table><div id='disp_reset_service_charge'></div>";
    jQuery("#disp_reset_service_charge").html(disp_service+disp_actual_amt);
}

function revised_loan_amount_display(module){

    //Calculate revised Loan Amount
    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_charge = parseFloat(jQuery("#service_crg").val());
    else
        ser_charge = 0;

    if(!isNaN(parseFloat(jQuery("#actual_loan_amt").val())))
        actual_loan_amt = parseFloat(jQuery("#actual_loan_amt").val());
    else
        actual_loan_amt = 0;

    //actual_loan_amt_rnd = roundLoanAmount(actual_loan_amt);

    disp_actual_amt = "<table class='disp_calc' width=95% align=center>";
    disp_actual_amt += "<tr><td> </td><td class='disp_calc'>Initial Loan Amount :</td> <td align=right class='disp_calc'>"+ini_loan_amt.toFixed(2)+"</td><td> </td><td> + </td></tr>";
    disp_actual_amt += "<tr><td> </td><td class='disp_calc'>Service Charge (Rounded) :</td> <td align=right class='disp_calc'>"+ser_charge.toFixed(2)+"  </td><td>  </td><td>  </td></tr>";
    disp_actual_amt += "<tr><td> </td><td>Revised Loan Amount:</td> <td colspan=3 align=right>Rs. "+actual_loan_amt.toFixed(2)+"</td></tr>";
    disp_actual_amt += "</table><div id='disp_reset_service_charge'></div>";

    jQuery("#disp_actual_loan_amount").html(disp_actual_amt);
}





function final_loan_amount_display(module){
    rev_new_amt = parseFloat(jQuery("#actual_loan_amt").val());
    new_amt = parseFloat(jQuery("#loan_amt").val());


    to_ret = parseFloat(jQuery("#to_return_amt").val());
    to_rec = parseFloat(jQuery("#to_receive_amt").val());


    /*Changed on 7-Sep-2012 Revised amount getting*/
   type = jQuery("#ld_scheme_type").val().toLowerCase();

    if(!isNaN(parseFloat(jQuery("#actual_loan_amt").val())))
        actual_loan_amt = parseFloat(jQuery("#actual_loan_amt").val());
    else
        actual_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_charge = parseFloat(jQuery("#service_crg").val());
        //ser_charge = roundServiceAmount(service_charge);
    else
        ser_charge = 0;

    if(!isNaN(parseFloat(jQuery("#service_rate").val())))
        service_rate = parseFloat(jQuery("#service_rate").val());
    else
        service_rate = 0;

    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    final_revised = ini_loan_amt + ser_charge; //Rathna

    /*Used for Round the amount*/
    final_revised_new_num = Math.round(final_revised).toFixed(2);

    //final_revised_new_num = jQuery("#loan_amt").val();
    roundLoanAmount(final_revised_new_num);

    disp_new_amt = "<table class='disp_calc' width=95% align=center>";


    //disp_new_amt += "<tr><td> </td><td class='disp_calc'>Revised Amount :</td><td align=right class='disp_calc'>"+rev_new_amt.toFixed(2)+"</td><td> </td><td></td></tr>";
    disp_new_amt += "<tr><td> </td><td class='disp_calc'>Revised Amount :</td><td align=right class='disp_calc'>"+final_revised+"</td><td> </td><td></td></tr>";
    disp_new_amt += "<tr><td> </td><td class='disp_calc'>Rounded Amount :</td> <td align=right class='disp_calc'>"+final_revised_new_num+"</td><td> </td><td></td></tr>";
    disp_new_amt += "<tr><td> </td><td class='disp_calc'>To Return :</td> <td align=right class='disp_calc'>"+to_ret.toFixed(2)+"</td><td> </td><td></td></tr>";
    disp_new_amt += "<tr><td> </td><td class='disp_calc'>To Receive :</td> <td align=right class='disp_calc'>"+to_rec.toFixed(2)+"</td><td> </td><td></td></tr>";
    disp_new_amt += "</table>";



    jQuery("#disp_new_loan_amount").html(disp_new_amt);



}

/** Rathna Service Round renewal setup - 6 Oct 2012 **/

function serviceFlatResetCheck(module_name){

    type = jQuery("#ld_scheme_type").val().toLowerCase();
    service_rate = parseFloat(jQuery("#ld_service_rate").val());

    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_chr = parseFloat(jQuery("#service_crg").val());
    else
        ser_chr = 0;

    /*if(!isNaN(parseFloat(jQuery("#service_rate").val())))
        service_rate = parseFloat(jQuery("#service_rate").val());
    else
        service_rate = 0;*/

    //Calculate revised Loan Amount

    if(type=="flat"){

        //alert(" 1 - "+ser_chr);
        //alert(ini_new_amt);
        new_amt = ini_new_amt+ser_chr;
        ini_div = parseInt(ini_new_amt/1000);
        new_div = parseInt(new_amt/1000);
        //alert(ini_div+"  "+new_div);
        if(ini_div < new_div){
             ser_chr = service_rate * (new_amt / 1000);
             ser_chr = roundServiceAmount(ser_chr);
            //alert(" 2 - "+ser_chr);
            //Setting Service Charge
            jQuery("#service_crg").val(ser_chr.toFixed(2));
            jQuery("#service_rate").val(service_rate.toFixed(2));

            //Display Service Charge Amount Details
            reset_service_charge_display(module_name);

        }

    } else {

        new_amt = ini_new_amt+ser_chr;
        ini_div = parseInt(ini_new_amt/1000);
        new_div = parseInt(new_amt/1000);
         alert(ini_div+"  "+new_div);
        if(ini_div < new_div){
        ser_chr = service_rate * (new_amt / 100);
        ser_chr = roundServiceAmount(ser_chr);

        jQuery("#service_crg").val(ser_chr.toFixed(2));
        jQuery("#service_rate").val(service_rate.toFixed(2));
        reset_service_charge_display(module_name);

        }
    }
}


// Common Total Calculation based on module name
function setTotalAmount(man){


    presetNewAmount(man);


    totals=0.00;
    $(".calc").each(function(i) {
      if(!isNaN(parseFloat($(this).val())))
      {
        totals += parseFloat($(this).val());
      }
    });

    //alert(totals);

    //Decimal Method calculate minimum to 50 paise
    int_total =  parseInt(totals);
    dec_val =  parseInt((totals - int_total) * 100);

    /*
    // 50 paise round codings
    if(dec_val >= 0 && dec_val <= 25 )
        dec_total = 0.00;
    else if((dec_val >= 25 && dec_val <= 50) || (dec_val >= 50 && dec_val <= 75))
        dec_total = 0.50;
    else
        dec_total = 1.00;
    */
    //alert(totals+" =  " + int_total+"  +  "+dec_val);

    if(dec_val > 0)  dec_total = 1.00;
    else   dec_total = 0.00;

    //alert(totals+" =  " + int_total+"  +  "+dec_total);

    totals  = int_total + dec_total;


    //Round Method Round to 1 Rupee
    //totals = Math.round(totals);
    if(totals<0){

    lbl_total_amount = "Pay to customer";
    totals = totals * -1;

    }else{

    lbl_total_amount = "Customer to pay";

    }

    jQuery("#label_total_amount").html(lbl_total_amount);
    jQuery("#disp_total_amount").html("Rs."+totals.toFixed(2));
    jQuery("#total_amt").val(totals.toFixed(2));


    //Reset New amount fro Renewal, partial and excess
    resetNewAmount();

}

function roundServiceAmount(ser_amt){

    rev_ser_amt = parseFloat(ser_amt);
    amt = parseFloat(ser_amt/10);
    int_amount =  parseInt(amt);
    dec_val =  (amt - int_amount) * 10;
    //alert(dec_val);
    if(dec_val > 5){
        dec_val = 10;

    }else if(dec_val <= 5 && dec_val > 0){
        dec_val = 5;
    }else
        dec_val = 0;

    ser_amt = int_amount*10 + dec_val;

    return parseInt(ser_amt);
}

function roundLoanAmount(new_amt){

    rev_new_amt = parseFloat(new_amt);

    amt = parseFloat(new_amt/100);

    int_amount =  parseInt(amt);
    dec_val =  Math.round((amt - int_amount) * 100);

    to_rec = 0;
    to_ret = 0;

    if(dec_val <= 25){

        to_rec = dec_val;
        dec_val = 0;

    }else if(dec_val > 25 && dec_val <= 50){

        to_ret = (50 - dec_val);
        dec_val = 50;

    }else if(dec_val > 50 && dec_val <= 75){

        to_rec = (dec_val - 50);
        dec_val = 50;

    }else if(dec_val > 75){

        to_ret = (100 - dec_val);
        dec_val = 100

    }


    jQuery("#to_return_amt").val(to_ret.toFixed(2));
    //jQuery("#to_return_amount").html("Rs. "+to_ret.toFixed(2));

    jQuery("#to_receive_amt").val(to_rec.toFixed(2));
    //jQuery("#to_receive_amount").html("Rs. "+to_rec.toFixed(2));


    new_amt = (int_amount  * 100) + dec_val;



    //alert(new_amt+"  "+to_rec+" "+to_ret);
    //alert(new_amt.toFixed(2));

    jQuery("#loan_amt").val(new_amt.toFixed(2));


    new_scheme_id = jQuery("#new_scheme_id").val();
    jQuery("#old_scheme_id").val(new_scheme_id);

    display_scheme_details(new_scheme_id);


}

function roundLoanAmount_BLOCK(new_amt){

    rev_new_amt = parseFloat(new_amt);

    amt = parseFloat(new_amt/100);

    int_amount =  parseInt(amt);
    dec_val =  Math.round((amt - int_amount) * 100);

    to_rec = 0;
    to_ret = 0;

    if(dec_val <= 25){

        to_rec = dec_val;
        dec_val = 0;

    }else if(dec_val > 25 && dec_val <= 50){

        to_ret = (50 - dec_val);
        dec_val = 50;

    }else if(dec_val > 50 && dec_val <= 75){

        to_rec = (dec_val - 50);
        dec_val = 50;

    }else if(dec_val > 75){

        to_ret = (100 - dec_val);
        dec_val = 100

    }


    jQuery("#to_return_amt").val(to_ret.toFixed(2));
    //jQuery("#to_return_amount").html("Rs. "+to_ret.toFixed(2));

    jQuery("#to_receive_amt").val(to_rec.toFixed(2));
    //jQuery("#to_receive_amount").html("Rs. "+to_rec.toFixed(2));


    new_amt = (int_amount  * 100) + dec_val;



    //alert(new_amt+"  "+to_rec+" "+to_ret);

    /*Changed on 7-Sep-2012 by karthik*/
    /*Changed on 7-Sep-2012 Revised amount getting*/

   type = jQuery("#ld_scheme_type").val().toLowerCase();

    if(!isNaN(parseFloat(jQuery("#actual_loan_amt").val())))
        actual_loan_amt = parseFloat(jQuery("#actual_loan_amt").val());
    else
        actual_loan_amt = 0;

    if(!isNaN(parseFloat(jQuery("#service_crg").val())))
        ser_charge = parseFloat(jQuery("#service_crg").val());
        //ser_charge = roundServiceAmount(service_charge);
    else
        ser_charge = 0;

    if(!isNaN(parseFloat(jQuery("#service_rate").val())))
        service_rate = parseFloat(jQuery("#service_rate").val());
    else
        service_rate = 0;


    if(!isNaN(parseFloat(jQuery("#ini_loan_amt").val())))
        ini_loan_amt = parseFloat(jQuery("#ini_loan_amt").val());
    else
        ini_loan_amt = 0;

    final_revised = ini_loan_amt + ser_charge; //Rathna

    /*Used for Round the amount*/
    final_revised_new_num = Math.round(final_revised).toFixed(2);
    jQuery("#loan_amt").val(final_revised_new_num);

    /* New scheme id */
    new_scheme_id = jQuery("#new_scheme_id").val();
    jQuery("#old_scheme_id").val(new_scheme_id);

    display_scheme_details(new_scheme_id);


}



function display_scheme_details(new_scheme_id){


    if(new_scheme_id != ""){ // && new_scheme_id != old_ld_scheme_id){
        old_ld_scheme_id = new_scheme_id;
        new_amt = parseFloat(jQuery("#loan_amt").val());
        //var d = new Date();
        //'+(d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate())+'
        new_dt = jQuery("#paid_on").val();

        list_details('scheme_detail','scheme_details','id='+jQuery("#new_scheme_id").val()+'&loandate='+new_dt+'&loanamount='+new_amt.toFixed(2));

    }
}



function summary_details(module, $wise_type, $search){
    jQuery("#wise").val($wise_type);
    search_items(module);

}

function search_validation(module){

if(module.indexOf("report")>0){

    var from_date = jQuery("#sdate").val();
    var to_date = jQuery("#edate").val();


    arr_sdt = from_date.split("-");
    arr_edt = to_date.split("-");

    if(arr_sdt.length >= 2)
        sdate = new Date(arr_sdt[2], arr_sdt[1], arr_sdt[0], 0, 0, 0, 0);
    else{
        sdate = "";//new Date();
        //jQuery("#from_date").val(sdate.getDate()+"-"+(sdate.getMonth()+1)+"-"+sdate.getYear());
    }

    if(arr_edt.length >= 2)
        edate = new Date(arr_edt[2], arr_edt[1], arr_edt[0], 0, 0, 0, 0);
    else{
        edate = "";//new Date();
        //jQuery("#from_date").val(edate.getDate()+"-"+(edate.getMonth()+1)+"-"+edate.getYear());
    }

    if( sdate > edate ){
        jalert("Starting date should be less than end date");
        return false;
    }
    else
        return true;



}

}


function filter_group_list(content, args){

var options = '<option value="">-- Select Group --</option>';
for (var i = 0; i < content.length; i++) {
    options += '<option value="' + content[i].id + '">' + content[i].name + '</option>';
}


jQuery("#user_group_id1").html(options);


}


function fileter_group(branch_id){


    callAjaxRequest("list_details.php", "page=filter_group_list&branch_id="+branch_id+"&type=filter","null","POST","script",false);

}


function view_history(cus_id){
    print_preview('customer_wise_transaction_report','print_dialog','','search_customer_id='+cus_id,'Customer Wise Transaction Report',1090,700);
}


function allocation_detail(json_str){

    eval("obj = "+json_str);




    if(obj.allocated_amount != "" && obj.id != ""){

        jQuery("#amount_transf1").val(obj.allocated_amount);
        jQuery("#request_id").val(obj.request_id);
        jQuery("#trans_id").val(obj.id);


        jQuery("#request_branch_id1").val(obj.req_branch_id);

        disp = "Requested Branch : "+obj.requested_branch;
        disp += "<br>Requested Amount : Rs. "+parseFloat(obj.requested_amount).toFixed(2)+"<br>";
        disp += "<br>Current Allocated Amount : Rs. "+parseFloat(obj.allocated_amount).toFixed(2);
        disp += "<br>Current Allocated Branch : "+obj.allocated_branch+"<br>";
        disp += "<br>Status : ALLOCATED";

        jQuery("#amount_req").html(disp);

    }else{

        jQuery("#amount_transf1").val(obj.requested_amount);
        jQuery("#request_id").val(obj.request_id);
        jQuery("#trans_id").val("");

        jQuery("#request_branch_id1").val(obj.req_branch_id);

        disp = "Requested Amount : Rs. "+parseFloat(obj.requested_amount).toFixed(2)+"<br>";
        disp += "<br>Status : NOT ALLOCATED";

        jQuery("#amount_req").html(disp);

    }



}





last_wav_days = 0;
cur_wav_days = 0;
function reset_pay_interest(wav_days, pledge_id, pay_id, atype, module)
{

    if(last_wav_days==cur_wav_days){
        cur_wav_days = wav_days;
    }else{
        last_wav_days = cur_wav_days;
        cur_wav_days = wav_days;
    }
  //alert(wav_days);
  //no_of_days
  // callAjaxRequest("list_details.php", "page=wavier_interest&pledge_id="+pledge_id+"&wavier_days="+wav_days+"&pay_id="+pay_id+"&type="+atype+"&module="+module,"null","POST","script",false);

  get_interest_details(pledge_id, pay_id, module );

}

function auction_interest_details(interest_details, args){
    //alert(interest_details.no_of_days+" < "+interest_details.default_wavier_days);

        jQuery("#disp_interest_amount").html(" Rs."+parseFloat(interest_details.interest).toFixed(2));
        jQuery("#wavier_reason").html(interest_details.default_wavier_reason);
        jQuery("#disp_interest_days").html(interest_details.no_of_days - interest_details.default_wavier_days);
        jQuery("#disp_interest_rate").html(interest_details.interest_rate+" % ");
        jQuery("#disp_wavier_amount").html("Rs. "+parseFloat(interest_details.default_wavier_amount).toFixed(2));

        if(interest_details.penalty_status == 'Over Due'){

/*
********************************************************************
        Commented the overdue intial month calculation
        on 06-Nov-2011  To activate the initial month Calculation remove the below commented code
        If you remove this comment you have to remove the comment in following
                payinterest.class.php method getInterestDetails, forms/payinterest.html,
                elements/loan_interest_details.html and
                remove JS script comment form the function_common_module.js pay_wavier_interest();
**********************************************************************
*/

           /* jQuery("#normal_interest_days_disp").html(interest_details.normal_interest_days);
            jQuery("#normal_interest_rate_disp").html(interest_details.normal_interest_rate+" % ");
            jQuery("#normal_interest_amount_disp").html("Rs."+parseFloat(interest_details.normal_interest_amount).toFixed(2));

            jQuery("#penalty_interest_days_disp").html(interest_details.penalty_interest_days);
            jQuery("#penalty_interest_rate_disp").html(interest_details.penalty_interest_rate+" % ");
            jQuery("#penalty_interest_amount_disp").html("Rs."+parseFloat(interest_details.penalty_interest_amount).toFixed(2));
            */

        }


        //jQuery("#principle_amt").val(interest_details.principle_payment);
        jQuery("#no_of_days").val(interest_details.no_of_days - interest_details.default_wavier_days ); //
        jQuery("#interest_amt").val(parseFloat(interest_details.interest).toFixed(2));
        jQuery("#rate_of_interest").val(interest_details.interest_rate);
        jQuery("#wavier_amount").val(interest_details.default_wavier_amount);
        jQuery("#wavier_days").val(interest_details.default_wavier_days)
        //alert(interest_details.default_wavier_amount)

        if(interest_details.time_status != "Period End"){
            jQuery("#disp_time_status").hide();
        }
        else{
            jQuery("#disp_time_status").show();
        }

        setTotalAmount();

}


function reset_wavier(){
    //alert(last_wav_days);
    jQuery('#wavier_days').val(last_wav_days);
}



function reset_interest_details(interest_details, args){

//alert(interest_details);
//console.log(interest_details);exit;

      /*old code on Apr28-2016*
       if(parseInt(interest_details.no_of_days) < parseInt(interest_details.default_wavier_days)){
    /*old code on Apr28-2016*/
    // if(interest_details.default_wavier_days==1){

    //     var test =parseFloat(interest_details.interest)+parseFloat(interest_details.default_wavier_amount);
    // //console.log(test);

    // }
    // else{
    //     var test =parseFloat(interest_details.interest);

    // }

    if(parseInt(interest_details.cumulative_days) < parseInt(interest_details.default_wavier_days)){
        $diff = interest_details.default_wavier_days - interest_details.no_of_days;
        //alert("'"+interest_details.pay_id+"','"+interest_details.act_type+"','"+interest_details.module+"'");
        confirmYesFunction = "reset_pay_interest('"+(interest_details.default_wavier_days-$diff)+"','"+interest_details.pledge_id+"','"+interest_details.pay_id+"','"+interest_details.act_type+"','"+interest_details.module+"')";
        confirmNoFunction = "reset_wavier()";
        jconfirm('Wavier Days should not Exceed the Number of Days from the Loan Date. <ul> <li>Sent Wavier Days : </li><li>Maximum Wavier Days for this Loan :  </li></ul> <font color=red>Do we consider the maximum as wavier day ?</font> ');
    }else{
        //console.log(interest_details);exit;
        //jQuery("#disp_interest_amount").html(" Rs."+parseFloat(test.toFixed(2)));
        jQuery("#disp_interest_amount").html(" Rs."+parseFloat(interest_details.interest).toFixed(2));
       // $("input[name='pval[interest_amount]']").val("1");
        jQuery("#wavier_reason").html(interest_details.default_wavier_reason);
        /*
            TOTAL INTEREST DAYS WITH SUBTRACT OF WAVIER DAYS  (29-Aug-2016)
            interest_days_round_off=interest_details.cumulative_days - interest_details.default_wavier_days;
        */
        /*Interest days WITHOUT SUBTRACT OF WAVIER DAYS*/
        interest_days_round_off=interest_details.cumulative_days;
        /**/
        if(interest_days_round_off==0){interest_days_round_off=1}
        jQuery("#disp_interest_days").html(interest_days_round_off);
        jQuery("#disp_interest_rate").html(interest_details.interest_rate+" % ");
        jQuery("#disp_wavier_amount").html("Rs. "+parseFloat(interest_details.default_wavier_amount).toFixed(2));

        if(interest_details.penalty_status == 'Over Due'){

/*
********************************************************************
        Commented the overdue intial month calculation
        on 06-Nov-2011  To activate the initial month Calculation remove the below commented code
        If you remove this comment you have to remove the comment in following
                payinterest.class.php method getInterestDetails, forms/payinterest.html,
                elements/loan_interest_details.html and
                remove JS script comment form the function_common_module.js pay_wavier_interest();
**********************************************************************
*/

           /* jQuery("#normal_interest_days_disp").html(interest_details.normal_interest_days);
            jQuery("#normal_interest_rate_disp").html(interest_details.normal_interest_rate+" % ");
            jQuery("#normal_interest_amount_disp").html("Rs."+parseFloat(interest_details.normal_interest_amount).toFixed(2));

            jQuery("#penalty_interest_days_disp").html(interest_details.penalty_interest_days);
            jQuery("#penalty_interest_rate_disp").html(interest_details.penalty_interest_rate+" % ");
            jQuery("#penalty_interest_amount_disp").html("Rs."+parseFloat(interest_details.penalty_interest_amount).toFixed(2));
            */

        }


        //jQuery("#principle_amt").val(interest_details.principle_payment);
        jQuery("#no_of_days").val(interest_details.cumulative_days - interest_details.default_wavier_days ); //
        jQuery("#interest_amt").val(parseFloat(interest_details.interest).toFixed(2));
        jQuery("#rate_of_interest").val(interest_details.interest_rate);
        jQuery("#wavier_amount").val(interest_details.default_wavier_amount);
        jQuery("#wavier_days").val(interest_details.default_wavier_days)
        //alert(interest_details.default_wavier_amount)
        //console.log(interest_details);exit;
        if(interest_details.time_status != "Period End"){
            jQuery("#disp_time_status").hide();
        }
        else{
            jQuery("#disp_time_status").show();
        }
        setTotalAmount();
        show_div('wavier_detail');
    }

}

function get_interest_details(pledge_id, pay_id, module ){
   // alert("pledge_id");
    wavier_days = jQuery("#wavier_days").val();
    paid_date = jQuery("#paid_on").val();

    lcPage = "list_details";


    lcParam = "wavier_days="+wavier_days+"&";
    lcParam += "paid_date="+paid_date+"&";
    lcParam += "pledge_id="+pledge_id+"&";
    lcParam += "pay_id="+pay_id+"&";
    lcParam += "module="+module+"&";

    process_type = "paid_date_validation";

    jQuery.ajax({
           type: "POST",
           dataType : 'json',
           url: lcPage+".php?page="+process_type,
           data:"mod=Ajax&"+ lcParam ,
           success: function(jobj)
           {


                if(jobj.success){

                    if(typeof module != "undefined" && module != "auction_management")
                        lcParam = "page=reset_interest_details&"+lcParam;
                    else
                        lcParam = "page=auction_interest_details&"+lcParam;


                    callAjaxRequest(lcPage+'.php', lcParam, "#content_div", "POST", "script", false, false);
                    return false;

                }else{

                    //var styleclass='alert';

                    var Errorfields = jobj.args.fields.split(",");
                    var Errorlabels = jobj.args.labels.split(",");
                    var errList = "<ol>";
                    for(var c=0;c<Errorfields.length;c++)
                    {
                          class_name = Errorfields[c].replace(/#/i,'.');
                          disp_name = Errorfields[c].replace(/#/i,'#disp_');
                          label_name = Errorfields[c].replace(/#/i,'#label_');
                          $(Errorfields[c]).css("color","red");
                          $(class_name).css("color","red");
                          $(disp_name).css("color","red");
                          $(label_name).css("color","red");

                          if(Errorlabels[c] != "")
                            errList += "<li> * "+Errorlabels[c]+"</li>";
                    }
                    errList += "<ol>";


                if(jobj.isAlert){
                    jalert("<h4>"+jobj.content+"</h4>  "+errList);
                }else{
                    //jQuery("span").removeClass("required");
                    //jQuery(SplitMsg[1]).addClass("required");
                    show_status(jobj.content,0);
                }


           }
    }
    });


    //alert(pledge_id+", "+pay_id+",  "+wavier_days);
    //callAjaxRequest("list_details.php", "page=reset_interest_details&wavier_days="+wavier_days+"&pay_id="+pay_id+"&paid_date="+paid_date+"&pledge_id="+pledge_id+"&type=","null","POST","script",false);
}

/*Modified by 11-Sep-2012*/
/*Date Validateions*/
function date_validation()
{
    var from_date=document.getElementById("sdate").value;
    var to_date=document.getElementById("edate").value;
    var from_date=from_date.split("-");
    var to_date=to_date.split("-");
    var sfrom_date = new Date(from_date[2],from_date[1],from_date[0]);
    var sto_date = new Date(to_date[2],to_date[1],to_date[0]);
    if(sfrom_date > sto_date)
    {
        jalert("Invalid date");
        return false;

    }
    else
        return true;
}

