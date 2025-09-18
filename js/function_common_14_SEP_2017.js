
/********************** Start **************************************/
//variable assigned for page loading image
var loader ="<center><IMG SRC='./images/content-loading.gif' > <font color='#A33D98'> Processing</font></center>";
//variable assigned for progress loading image
var progress ="<IMG SRC='./images/load-img.gif' >  <font color='#A33D98'>Processing</font>";
var status_change ="<IMG SRC='./images/dots_white.gif' >";
//Adjust the speed of fade out for display notice
var FADE_SPEED = 2000;
//Adjust the speed of scroll
var SCROLL = 500;
//Adjust the speed of fade toggle
var FADE_TOGGLE = 500;
var error_msg = "<table width='98%' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='alert_border'><tr class='table_cont black'><td class='red_alert'><strong>Please Enter Page Number</strong></td></tr></table>"
var error_msg_search = "<table width='98%' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='alert_border'><tr class='table_cont black'><td class='red_alert'><strong>Please Select Category</strong></td></tr></table>"
var error_msg_drag = "<table width='98%' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='alert_border'><tr class='table_cont black'><td class='red_alert'><strong>Please drag and drop the menu and then save</strong></td></tr></table>"
var error_msg_category= "<table width='98%' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='alert_border'><tr class='table_cont black'><td class='red_alert'><strong>Select category name</strong></td></tr></table>"
var field_missing= "<table width='98%' align='center' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' class='alert_border'><tr class='table_cont black'><td class='red_alert'><strong>Required field missing</strong></td></tr></table>"


jQuery.fn.fadeToggle = function(speed, easing, callback) {
    return this.animate({opacity: 'toggle'}, speed, easing, callback);
};

function sessionTimeOut(){

jalert("Session Time Out. Please login the application again");

}


function select_element(obj,lbl){
    if(obj.value == lbl){
        obj.value = '';
    }
}

function deselect_element(obj,lbl){
    if(obj.value == ''){
        obj.value = lbl;
    }
}

function show_status(content,val)
{
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

    $(".header-floating-message-querybuilder").slideUp();
    $("#common-floating-message").css({ border:border_color});
    $("#common-floating-message").css({ backgroundColor:back_color});
    $("#common-floating-message").css("color",color);
    $("#common-floating-message").hide();
    $("#common-floating-message").html('<div style="margin-top:8px;font-weight:bold"><center>'+content+'</center></div>');
    $("#common-floating-message").slideDown(1000);
    $("#common-floating-message").fadeTo(1000, 1);
    $("#common-floating-message").slideUp(1000);
    $(".header-floating-message-querybuilder").slideDown(6000);
}

function remove_red(field)
{
    try{
        jQuery("#"+field).css('color','black');
        jQuery("#disp_"+field).css('color','black');
        jQuery("#label_"+field).css('color','black');
        jQuery("."+field).css('color','black');
    }catch(e){}
}
/**
  * Functions used for create, edit, view, paging and delete functions in all modules
**/

function create_function(div_id, module, search_args)
{

    var success = 0;

    $.ajax({
           type: "GET",
           url: module + ".php?"+search_args,
           data:{
                type : "create",
                mod : "Ajax"

           },
           success: function(msg){
             // alert(msg);

            msg = msg.replace(/^[\s]+/g,"");
            if(msg!='session_timed_out'){
                var success = 1;
                $("#"+div_id).html(msg);
            }
           else{
                sessionTimeOut();
           }
           change_ajax_message("Page loaded");

          },
          complete: function(outputData){
               hide_ajax_status();
          },
          beforeSend: function(outputData){
               show_ajax_status("Loading....",0);
          }

         });
    //echo "madffsfdsf";exit;
    return false;
}


function reset_function(div_id, module, id, page_value, search_args, branch_id){

    edit_function(div_id, module, id, page_value, search_args, branch_id);

}

function edit_function(div_id, module, id, page_value, search_args, branch_id)
{
    //alert(id);
    $.ajax({
           type: "GET",
           url: module + ".php?"+search_args+"type=edit",
           data:{
                id : id,
                branch_id : branch_id,
                pageno : page_value,
                mod : "Ajax"

           },
           success: function(msg){
               //alert(msg);
                msg = msg.replace(/^[\s]+/g,"");
                 if(msg!='session_timed_out'){
                    SplitMsg = msg.split("~~");
                    //alert(SplitMsg[0]);
                    $("#"+div_id).html(SplitMsg[0]);

                 }
                 else{

                      sessionTimeOut();
                 }
                change_ajax_message(module+" Page Loaded");
           },
           complete: function(outputData){
                hide_ajax_status();
           },
           beforeSend: function(outputData){
                show_ajax_status("Loading....",0);
            }
         });
    return false;
}

function back(div_id, module, page_value, search_args) {

    jQuery.ajax({
           type: "POST",
           url: module + ".php?"+search_args+"type=back&pageno="+page_value,
           data: {
               mod : "Ajax"
               },
           success: function(msg){
            msg = msg.replace(/^[\s]+/g,"");

             if(msg!='session_timed_out'){
                SplitMsg = msg.split("~~");
                jQuery("#"+div_id).html(SplitMsg[0]);
             }
             else{
                  sessionTimeOut();
             }
           }
         });
    return false;
}

/**
 * End of fcommon functions
**/



function show_search(){
         $('#search_table').slideToggle('slow');
         $('#serach_button_div').html('<a href="#" id="serach_button_show" class="greybtn" onclick="hide_search();return false;">Close</a>');

}

function hide_search(){
         $('#search_table').slideUp('slow');
         $('#serach_button_div').html('<a href="#" id="serach_button_show" class="greybtn" onclick="show_search();return false;">Search</a>');
}

function drop_detail_view(div_id)
    {
    collapse_all_view();
    display = jQuery("#"+div_id).css('display');
    if(display=='none'){
        jQuery("#"+div_id).show("slide", { direction: "up",  mode : "hide" }, 500);
    }else{
        jQuery("#"+div_id).hide("slide", { direction: "up",  mode : "hide" }, 500);
    }


}

function collapse_all_view(){
    jQuery(".display_view").each(function(i)
    {
        this.style.display = "none";
    });
}


/* Created by Rathna on 01-Oct-2011*/
function list_details(div_id, module, addparam){

    param = "page="+module+"&"+addparam;
    callAjaxRequest('list_details.php', param, "#"+div_id, "POST", "html", true);
    return true;

}



//Change status for all module

function change_status(div_id, module, id, status,branch_id)
{

    confirmYesFunction = "change_status_action('"+div_id+"', '"+module+"', '"+id+"','"+ status+"','"+branch_id+"')";
    confirmNoFunction = "welcome='No'";

    jconfirm("Changing the status will affect all it's related modules. Please confirm the action");


}

function change_status_action(div_id, module, id, status,branch_id){

jQuery.ajax({
           type: "POST",
           url: module + ".php?type=change_status&status="+status+"&id="+id,
           data : {
                 branch_id : branch_id,
                 mod : "Ajax"
            },
           success: function(msg)
           {
                  msg = msg.replace(/^[\s]+/g,"");

                   if(msg!='session_timed_out'){
                        SplitMsg = msg.split("~~");
                        msg = SplitMsg[0];
                        if(SplitMsg[0] == 'Error') {

                            alert(SplitMsg[1]);
                        }
                        else {
                            var image_name = 'disable.gif';
                            var alt_text = 'enable';
                            var status = 1;
                        if(msg == 1) {
                            image_name = 'enable.gif';
                            alt_text = 'disable';
                            status = 0;
                        }

                        jQuery("#"+div_id).html('<a href="javascript:;" title="click here to '+alt_text+'" onclick=\'change_status("status_div'+id+'", "'+module+'", '+id+', '+status+');\' class="status_row"><img src="images/'+image_name+'" /></a>');
                        }
                   }
                   else{
                       sessionTimeOut();
                   }

                   show_status(SplitMsg[1]);
           }
         });
    return false;

}

//Change status for inbox message
function change_status1(div_id, module,id,status,branch_id)
{
    jQuery.ajax({
           type: "POST",
           url: module + ".php?type=change_status&status="+status+"&id="+id,
           data : {
                 branch_id : branch_id,
                 mod : "Ajax"
            },
           success: function(msg)
           {
                  msg = msg.replace(/^[\s]+/g,"");

                   if(msg!='session_timed_out'){
                        SplitMsg = msg.split("~~");
                        msg = SplitMsg[0];
                        if(SplitMsg[0] == 'Error') {

                            alert(SplitMsg[1]);
                        }
                        else {
                            var image_name = 'read.png';
                            var alt_text = 'unread';
                            var status = 1;
                        if(msg == 1) {
                            image_name = 'unread.png';
                            alt_text = 'read';
                            status = 0;
                        }

                        //window.location.reload();
                        jQuery("#"+div_id).html('<a href="javascript:;" title="click here to '+alt_text+'" onclick=\'change_status1("status_div'+id+'", "'+module+'", '+id+', '+status+');\' class="status_row"><img src="images/'+image_name+'" /></a>');
                        }
                   }
                   else{
                       sessionTimeOut();
                   }

                   show_status(SplitMsg[1]);
           }
         });
    return false;
}


//Print Action
function print_page() {

   if(navigator.appName == "Microsoft Internet Explorer")
    isIE = true;
   else
    isIE = false;

   if(!isIE){

        var oIframe = document.getElementById('print_frame');
        module_title = oIframe.title;
        var oDoc = (oIframe.contentWindow || oIframe.contentDocument);

        if (oDoc.document) oDoc = oDoc.document;

        oDoc.write("<html>");
        oDoc.write("<head>");
        oDoc.write("<title>"+module_title+"</title>");
        oDoc.write("<link rel=\"stylesheet\" type=\"text/css\"  href=\"css/print_style.css\">");
        oDoc.write("</head>");
        oDoc.write("<body onload='this.focus(); this.print();'>");
        oDoc.write("<div style=\"testStyle\">");
        oDoc.write(jQuery("#print_content").html());
        oDoc.write("</div>");
        oDoc.write("</body>");
        oDoc.write("</html>");

        oDoc.close();

    }
    else
    {
        var str_content = "<html>\n<head>\n <link rel=\"stylesheet\" type=\"text/css\"  href=\"css/print_style.css\">\n</head><body><div style=\"testStyle\">\n"+jQuery("#print_content").html()+"\n</div>\n</body>\n</html>";
        var WindowObject = window.open('', 'PrintWindow', 'width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes');
        WindowObject.document.writeln(str_content);
        WindowObject.document.close();
        WindowObject.focus(); WindowObject.print(); WindowObject.close();
        //WindowObject.onLoad = function(){ alert("niside"); WindowObject.focus(); WindowObject.print(); WindowObject.close(); }
    }



}

// Created By Moses C rathna on 9/28/2011
//Customer Slip Print Preview Process
function prints_previews(page,div_id,search_query,param,title, width, height,status)
{

    if(status!='pledge_report_print'){
     $.each(arr_data, function(){

            if(search_query.indexOf('search_'+this.name) <= 0 && this.value!=""){
                search_status = true;
                search_query += "&search_"+this.name+"="+this.value;
            }

        });
    }
        //alert("Search Query : " + search_query);

    loading_html = jQuery("#page_loading").html();
    openDialog(div_id,loading_html,title, width, height);
    process_type = "preview"
    callAjaxRequest(page+".php?type="+process_type+search_query+"&status="+status,param, "#"+div_id, "POST", "html", true, false);
    return false;
}

//This function is applicable for Pre-Acution and Acution notice Process
function print_pre(page,div_id,id,param,title, width, height,status)
{
    loading_html = jQuery("#page_loading").html();
    openDialog(div_id,loading_html,title, width, height);
    process_type = "preview"
    callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);
    //callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status+"&search_sdate="+sdate+"&search_edate="+edate+"&search_category_id[]="+cat_id, param, "#"+div_id, "POST", "html", true, false);
    return false;
}

//For print preview  process
function print_preview_wsrc(page,div_id,id,param,title, width, height,status)
{
    //alert(page);
    /*Changed on 31-Aug-2012*/
    //alert(max_days);

    /*Get the search field values*/

    var sdate = jQuery("#sdate").val();
    var edate = jQuery("#edate").val();
    var cat_id = jQuery("#branch_id").val();



    //Days finding
    //Total time for one day
    var one_day=1000*60*60*24;

    //Here we need to split the inputed dates to convert them into standard format for furter execution
    if(sdate == '' || edate == ''){
        date1 = new Date();
        date2 = new Date(date1);
        date2.setDate(date2.getDate() - max_days);

        sdate = date1.getDate()+"-"+date1.getMonth()+"-"+date1.getFullYear();
        edate = date2.getDate()+"-"+date2.getMonth()+"-"+date2.getFullYear();
        /*alert(sdate+" "+edate);*/
    }else if(page != 'customer_wise_transaction_report'){
        var x=sdate.split("-");
        var y=edate.split("-");
        //date format(Fullyear,month,date)
        var date1=new Date(x[2],(x[1]-1),x[0]);
        var date2=new Date(y[2],(y[1]-1),y[0])
        var month1=x[1]-1;
        var month2=y[1]-1;
    }


    /*For the module, Pay intrest history of custome wise report. Condition else part works*/
    if(page !='customer_wise_transaction_report'){
    //Calculate difference between the two dates, and convert to days
    diff = Math.ceil((date2.getTime()-date1.getTime())/(one_day));

    }
    else{
        var diff = 0;
    }

    //callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);
    /*if((sdate == '') && (edate== ''))
    {
        jalert("Please select the date interval between "+ max_days + "days");
        return false;
    }

    else if(diff <= max_days){*/
            loading_html = jQuery("#page_loading").html();
            openDialog(div_id,loading_html,title, width, height);
            process_type = "preview";

            //Rathna
            /*if(param.indexOf('search_sdate') < 0)
                param += "&search_sdate="+sdate;
            if(param.indexOf('search_edate') < 0)
                param += "&search_edate="+edate;*/


            if(page=='category_wise_report'){
            callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status+"&search_sdate="+sdate+"&search_edate="+edate+"&search_category_id[]="+cat_id, param, "#"+div_id, "POST", "html", true, false);
            }else if(page == "customer_wise_transaction_report"){
            callAjaxRequest(page+".php?type="+process_type+"&id="+id, param, "#"+div_id, "POST", "html", true, false);
            }else{
            callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);
            }
            return false;
    /*    }
    else
    {
                jalert("Selected date difference must be less than "+ max_days + "days");
                return false;

    }*/
}

// For Dialog Activity - Not in Use
// Written By Moses C Rathna
// Dated : 9/28/2011

function openDialog(div_id,msg,title, width, height){

    jQuery("#"+div_id).html(msg);
    //jQuery("#"+div_id).css('visibility','visible');
    jQuery("#"+div_id).dialog({"show":"slide","hide":"slide","title":title,"width":width,"height":height});
    jQuery("#"+div_id).dialog("open");

}


function delete_selected(div_id,module,page_value, search_args,id){
    is_checked = false;
    //alert(search_args);exit;
    //obj = jQuery("input[type=checkbox]");

    //alert(obj.length);



    jQuery("input[type=checkbox]").not( $("#selall") ).each(
    function(i){
        //jalert(i);
        if(this.checked ==true)
        {
            is_checked = true;
        }
    }
    );

    if(!is_checked && (id=='' || typeof id == "undefined")){
        jalert("Please select atleast one item from the list to delete?",'warning');
        return false;
    }

    formPage = module;
    formSearch = search_args;
    extra_param = "&type=delete&page=delete_selected&"+formSearch;
    //alert(formPage);exit;

    if(typeof id != "undefined" && id!=""){
        formParam = "id="+id+extra_param;
    }else{
        formParam = jQuery("form").serialize()+extra_param;
    }
    //alert(formPage+'mani'+formParam);exit;
    confirmYesFunction = 'callAjaxRequest("'+formPage+'.php","'+formParam+'", "#content_div", "POST", "json", false, true);';
    confirmNoFunction = "welcome='No'";

    if(module=="product_pledge"){
        msg_note = "<br><b>Note:- <i>This delete action will delete all its related datas including some of cash flow records.</i></b>";
        msg_note += "<br> Cash flow data will be valuated by checking any other transaction done with this pledged details";
    }else if(module=="branch"){
        msg_note = "<br><b>Note:- <i>This delete action will transfer all its related datas transfer to head office branch including cash flow.</i></b>";
    }else if(module == "redemption"){
        msg_note = "This delete action will delete all its related datas";
    }
    else{
      msg_note="";
    }

    if(id=='' || typeof id == "undefined")
    msg = "Are you  sure to delete all selected records from the list?"+msg_note;
    else
    msg = 'Are you sure want to delete this record?'+msg_note;


    jconfirm(msg,'warning');

}


function form_enter_focus(obj,len,div_id,module,page_no,add_param,id){
     // alert(div_id);
        // focus the first field..
        obj.focus();

        $("input,select").not(":hidden").keypress(function (evt) {
            //jalert(evt.keyCode);
            if (evt.keyCode == 13) {
                //Get Button value or Link Text
                //iname = $(this).val();
                iname = $(this).html();

                //Check Action Button Press
                if (iname !== 'SAVE'){




                    //Get Form fields
                    var fields = $(this).parents('form:eq(0),body').find('button,input:text,input:password,input:radio,input:checkbox,textarea,select');
                    //Get Current focus index
                    var index = fields.index( this );

                    //Schme module flot cheking
                    if(module =='schemes'){


                        if(index==5 && $("#interest_float").attr("checked") == true){

                            $('input[name=mval[]]').focus();
                        }

                        if(index==6){

                            $('input[name=pval[status]]').focus();
                        }


                        //len = fields.length;


                    }

                    if ( index > -1 && ( index + 1 ) < len) {
                        //Focus next index control
                        fields.eq( index + 1 ).focus();
                    }
                    else
                    {
                      //Form Save Action
                      save_action(div_id,module,page_no,add_param,id);
                    }
                    return false;
                }
            }
        });

        //Remove red color from all form controls
        $("input").focus(function(event){ remove_red(this.id); });
}


var isSaveActionInitiated = false;


function disable_save_action(){

    if(!isSaveActionInitiated){
        isSaveActionInitiated = true;

        jQuery(".greybtn").each(function(){
            //alert(this.innerText.toUpperCase());
            if(this.innerHTML.toUpperCase() == "SAVE" || this.innerHTML.toUpperCase() == "RENEW" || this.innerHTML.toUpperCase() == "REDEEM" || this.innerHTML.toUpperCase() == "PAY" || this.innerHTML.toUpperCase() == "BID"){
                this.className = this.className.replace(/greybtn/i,"");
                this.className += " greybtn-off";
            }
        });

        return true;

    }else return false;

}


function enable_save_action(){

    isSaveActionInitiated = false;

    jQuery(".greybtn-off").each(function(){
        if(this.innerHTML.toUpperCase() == "SAVE" || this.innerHTML.toUpperCase() == "RENEW" || this.innerHTML.toUpperCase() == "REDEEM" || this.innerHTML.toUpperCase() == "PAY" || this.innerHTML.toUpperCase() == "BID"){
            this.className = this.className.replace(/greybtn-off/i,"");
            this.className += " greybtn";
        }
    });



}



function save_action(div_id,module,page_value, search_args, id){


    if(disable_save_action()){
      //alert(module);exit;
        formPage = module;
        formSearch = search_args;

        if(typeof id != "undefined" && id!=""){
            extra_param = "&type=update&page=save_action&"+formSearch+"&id="+id;
        }else{
            extra_param = "&type=add&page=save_action&"+formSearch;
        }

       // alert(module+'.php?'+extra_param);exit;

        formParam = jQuery("form").serialize();

        //Server Side Validation
        if(form_client_validate()){
            process_type = "formvalidation"
            jQuery.ajax({
                   type: "POST",
                   dataType : 'json',
                   url: formPage+".php?type="+process_type,
                   data:"mod=Ajax&"+ formParam+"&"+search_args ,

                   success: function(jobj)
                   {

                        if(jobj.success){
                            //+"&from_module="+jobj.args.from_module
                            formParam = jQuery("form").serialize();
                            //alert(module+'.php?'+extra_param);
                            callAjaxRequest(formPage+'.php', formParam+extra_param, "#content_div", "POST", "json", false, false);
                            jQuery("#formact_dialog").dialog("close");
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

                            isSaveActionInitiated = false;

                            enable_save_action();

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
        }
    }

}

function form_actions(page,div_id,id,type,addparam, title, width, height, action){

    //jQuery("#"+div_id).dialog("option","title",title);
    //jQuery("#"+div_id).dialog("option","width",width);
    //jQuery("#"+div_id).dialog("option","height",height);


    //jQuery("#"+div_id).html('<table bgcolor="#ffffff" width=100% height=100% cellpading=10 cellspacing=10><tr><td align=center valign=middle><img src="images/loading.gif"><h3>Loading... </h3></td></tr></table>');

    loading_html = jQuery("#page_loading").html();
    openDialog(div_id,loading_html,title, width, height);

    param = "page="+page+"&disp="+div_id+"&type="+type+"&id="+id+"&"+addparam;

    //alert(page+'.php?'+param);
    //alert(typeof action);
    if(action!='' && typeof action != "undefined"){

        jQuery(".ui-dialog-buttonset > button").each(
        function(i){
            if(i==0) this.innerHTML = "<span class='ui-button-text'>"+action+"</span>";
        }
        );
    }else{

        jQuery(".ui-dialog-buttonset > button").each(
        function(i){
            if(i==0) this.innerHTML = "<span class='ui-button-text'>Save</span>";
        }
        );


    }

   simpleAjaxRequest(page+'.php', param,"#"+div_id);



    return true;
}

var formAct = "";
var formParam = "";
var formId = "";
var formPage = "";
var formSearch = "";
var formTitle = "";



function form_client_validate()
{
   if(formPage=="allocation_of_branch")
   {
     //return amount_transfer_client();
   }
   if(formPage=="amount_transfer")
   {
     //return cheque_transfer_client();
     //return transfer_date_val();

   }
   if(formPage=="amount_received")
   {
     //return date_val();
   }

   return true;
}

function form_action_save(){
    //alert("inside form action");
    if(formAct == "delete" || formAct == "view"){
        formParam += "&disp=content_div&page="+formPage+"&"+ jQuery("#"+formId).serialize();
        callAjaxRequest(formPage+'.php', formParam, "#content_div", "POST", "json", false, false);

    }else{

        //Server Side Validation
        if(form_client_validate()){
            process_type = "formvalidation"
            jQuery.ajax({
                   type: "POST",
                   dataType : 'json',
                   url: formPage+".php?type="+process_type,
                   data:"mod=Ajax&"+ jQuery("#"+formId).serialize(),
                   success: function(jobj)
                   {

                        if(jobj.success){

                            formParam += "&disp=content_div&page="+formPage+"&"+ jQuery("#"+formId).serialize();
                            //alert(formPage+'.php'+formParam);
                            callAjaxRequest(formPage+'.php', formParam, "#content_div", "POST", "json", false, false);
                            jQuery("#formact_dialog").dialog("close");
                            return false;


                        }else{

                            //var styleclass='alert';


                            var Errorfields = jobj.args.fields.split(",");
                            var Errorlabels = jobj.args.labels.split(",");
                            var errList = "<ol>";
                            for(var c=0;c<Errorfields.length;c++)
                            {
                                  /*$(Errorfields[c]).css("color","red");
                                  errList += "<li> * "+Errorlabels[c]+"</li>";*/
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
        }else return false;

    }



}


function ajaxCompAction(rout,rtype, isShow){

    //alert(rtype);
    if(rtype=="json"){
        page = rout.page;
        status = rout.status;
        display = "#"+rout.disp;
        content = rout.content;
        disp = rout.disp;
        if(rout.isAlert) isAlert = rout.isAlert; else isAlert = false;
        if(rout.args) args = rout.args; else args = "";
    }

    if(rtype=="xml"){
        x=rout.documentElement;
        y=x.childNodes;

        page = y[0].text;
        status = y[1].text;
        disp = y[2].text;
        content = y[3].text;
        display = "#"+disp;
        isAlert = y[4];
        args = y[5];
    }

    if(rtype=="html"){
        rout1 = rout.split("\&");
        page = rout1[0].substring(rout1[0].indexOf("=")+1);
        status = "Displayed";
        content = "";
        disp = "button_action";
        display = "#"+disp;
        isAlert = false;
        args = "";
    }

    if(rtype == "script"){
        //jalert(rout);
        eval(rout);
    }

    if(typeof page =="undefined") page = "";
    if(typeof content =="undefined") content = "";
    if(typeof args =="undefined") args = "";
    if(typeof disp =="undefined") disp = "";

    if(isShow){

        disp_page = page.replace(/_/," ");
        disp_page = disp_page.replace(/_/," ");
        disp_page = disp_page.replace(/_/," ");
        change_ajax_message(disp_page+' Loaded- Success');

    }

    //alert("page = "+page+" \n Status = "+status+" \n Display "+display+" \n Content =  "+content+" \n  Disp  =  "+disp);


    if(content != "" && isAlert){
        content = content.replace(/\&lt;/g,"<").replace(/\&gt;/g,">").replace(/\&quot;/g,"\"");
        jalert(content);
        //show_ajax_status(content,0);
        //slid_hide_ajax_status();
        //shownotice(content);
    }

    module_page_action(page,disp,args,content);

}


var is_resulted = false;

function module_page_action(page,disp, args, content){
    // All Ajax module Complete action based on modules
    //alert(page);
    // Pay Interest Save Action Functional Calling
    if(page=="pay_interest" && (args=="save" || args=="update")){
        simpleAjaxRequest("pay_interest.php","type=refresh&"+formSearch, "#"+disp);
    }

    // Pay Interest Delete Action Functional Calling
    else if(page=="pay_interest" &&  args=="delete" ){
        simpleAjaxRequest("pay_interest.php","type=refresh&"+formSearch, "#"+disp);
        jQuery("#formact_dialog").dialog("close");
    }

    // Customer Auto Complete Event Functional Calling
    else if(page=="customer_auto_list"){

        customer_auto_list(content, args);
    }

    // Product Auto Complete Event Functional Calling
    else if(page=="product_auto_list"){
        product_auto_list(content, args);
    }

    // Cashflow Auto Complete Event Functional Calling
    else if(page=="cashflow_auto_list"){
            cashflow_auto_list(content, args);
    }

    // Privilege Usergroup Filtering
    else if(page=="filter_group_list"){
            filter_group_list(content, args);
    }

    // Pay Interest - Wavier Or Date of Interest Modification
    else if(page=="reset_interest_details"){
        //pay_wavier_interest(content, args);
        reset_interest_details(content, args);
    }
    // Auction Date of Auction and Interest Modification
    else if(page=="auction_interest_details"){
        //pay_wavier_interest(content, args);
        auction_interest_details(content, args);
    }

    // Branch Amount Transaction
    else if(page=="raise_request" || page=="allocation_of_branch" || page=="amount_transfer" || page=="amount_received"){
         simpleAjaxRequest(page+".php","type=refresh&"+formSearch, "#content_div");
    }
    //Delete Selected Common Action
    else if(page == "save_action"){
        //alert("inside "+args.module+"  "+args.pledge_id)
        if(args.from_module == "" || typeof args.from_module == "undefined" )
            simpleAjaxRequest(args.module+".php","type=refresh&"+args.search, "#content_div");
        else
            if(args.from_module == ""){
              simpleAjaxRequest(args.from_module+".php","type=create&"+args.search+"customerid="+args.customer_id+"&id="+
              args.pledge_id, "#content_div");
            }else{
              simpleAjaxRequest(args.from_module+".php","type=edit&"+args.search+"customerid="+args.customer_id+"&id="+
              args.pledge_id, "#content_div");

            }

        //enable_save_action();


    }
    else if(page == "message_send"){
        //alert("inside "+args.module+"  "+args.search)
        //simpleAjaxRequest("sent_items.php","type=refresh&"+args.search, "#content_div");

        //isSaveActionInitiated = false;
        //jQuery("#save_action_btn").removeClass("greybtn-off");
        //jQuery("#save_action_btn").addClass("greybtn");

        location.href= 'sent_items.php';


    }
    else if(page == "delete_selected"){
        if(args.isAlert){

        jalert(content,'warning');

        }
        simpleAjaxRequest(args.module+".php","type=refresh&"+args.search, "#content_div");
        jQuery("#formact_dialog").dialog("close");
    }
}

/*************************************
/*  By Moses C Rathna
/*
/*  Common Ajax and JQuery Functions
/*
/*  Created On 01-Oct-2011
/************************************/

var pledgeProductCount = 0;

// Common Alert and Confirm Message box display
// Arg1 : msg = Html Message to display
// Arg2 : type = ['alert','error','warning']

function jalert(msg,type){
        if(typeof type=="undefined" || type == "") type = "alert";
        jQuery("#alert_box").dialog("option","title",type.toUpperCase()+" Message");
        jQuery("#alert_box").dialog("option","width",600);
        jQuery("#alert_box").dialog("option","height","auto");
        msg = '<div class="msg-content"><p><img class="img-lft" src="images/'+type+'.png" />'+msg+'</p></div>';
        jQuery("#alert_box").html(msg);

        /*ele = jQuery("#alert_box");
        sh = ele.get(0).scrollHeight;
        h = 250;
        //alert(sh+"  "+h);
        if(sh > h){
        jQuery("#alert_box").dialog("option","height","auto");
        }*/

        jQuery("#alert_box").dialog({show: 'slide', hide: 'slide'});
        jQuery("#alert_box").dialog("open");
}


// Arg1 : msg = Html Message to display
// Arg2 : type = ['alert','error','warning']
// confirmYesFunction variable contain confirm action JS function or JS code
// confirmNoFunction variable contain non confirm action JS function or JS code [ If no action just put Js empty variable assign (Just to ignore eval error) ]

confirmYesFunction = "welcome='Yes'";
confirmNoFunction = "welcome='No'";
function jconfirm(msg,type){
        if(typeof type=="undefined" || type == "") type = "alert";
        msg = '<div class="confirm-content"><p><img class="img-lft" src="images/'+type+'.png" />'+msg+'</p></div>';
        jQuery("#confirm_box").html(msg);
        jQuery("#confirm_box").dialog({show: 'drop', hide: 'drop'});
        return jQuery("#confirm_box").dialog("open");
}



function show_ajax_status(content,val)
{
    if(val == -1){
        var color='#990000';
        var back_color ="#FFEAEA";
        var border_color ="1px solid #990000";
    }
    if(val == 0){
        var color='#5A2154';
        var back_color ="#E7D1ED";
        var border_color ="1px solid #5A2154";
    }
    if(val == 1){
        var color='#446423';
        var back_color ="#EFF5EA";
        var border_color ="1px solid #446423 ";
    }

    $(".header-floating-message-querybuilder").slideUp(1000);
    $("#common-floating-message").css({ border:border_color});
    $("#common-floating-message").css({ backgroundColor:back_color});
    $("#common-floating-message").css("color",color);
    $("#common-floating-message").html('<div style="margin-top:8px;font-weight:bold"><center>'+content+'</center></div>');
    //$("#common-floating-message").hide();
    $("#common-floating-message").fadeTo(500, 1);
    $("#common-floating-message").slideDown(500);

}

function change_ajax_message(content){
    $("#common-floating-message").html('<div style="margin-top:8px;font-weight:bold"><center>'+content+'</center></div>');
}

function hide_ajax_status(){
    //$("#common-floating-message").slideDown(500);
    //$("#common-floating-message").fadeTo(500, 1);
    $("#common-floating-message").slideUp(10);
    //$(".header-floating-message-querybuilder").slideDown(1000);
}

function slid_hide_ajax_status(){
    //$("#common-floating-message").slideDown(500);
    $("#common-floating-message").fadeTo(1000, 1);
    $("#common-floating-message").slideUp(1000);
    //$(".header-floating-message-querybuilder").slideDown(1000);
}

function simpleAjaxRequest(url, param, display){

    jQuery(display).load(url,param, function(msg){});

}

function callAjaxRequest(url, param, displayId, postType, retType, isdisplay, isShow){

    if(typeof isShow == "undefined") isShow = true;

    //alert(url);

    var cType='application/x-www-form-urlencoded';
    var objJson = false;
    jQuery.ajax(

        {

            type: postType,
            url: url,
            cache: false,
            data: param,
            dataType : retType,
            contentType : cType,

            success: function(outputData){
            //alert(outputData);
                if(isShow)  hide_ajax_status();
                if(isdisplay==false){
                    ajaxCompAction(outputData,retType,isShow);

                }else{
                    jQuery(displayId).html(outputData);
                    ajaxCompAction(param,retType,isShow);

                }


            },

            error: function(err){
                errMessage = "Error on loading...";
                if(isShow)  show_ajax_status(errMessage,-1);
            },

            complete: function(outputData){
                if(isShow)  hide_ajax_status();
            },

            beforeSend: function(outputData){
                if(isShow)  show_ajax_status("Loading....",0);
            }

        }
    );

    return true;

}
/*************************
/*      End
/*************************/
// Common Date picker function
// Arg 1 : Remove red display id
// Arg 2 : Date Picker text field id
// Arg 3 : Date picket date format
// Created by Rathna on 8 Nov 2011 5:53 PM
var RANGE_CAL_1;
function date_picker(disp_id, field_id, dt_format ){

    //alert(dt_format);
    remove_red(disp_id);
    RANGE_CAL_1 = new Calendar({
          inputField: disp_id,
          dateFormat: dt_format,
          trigger: field_id,
          bottomBar: false,
          onSelect: function() {
              var date = Calendar.intToDate(this.selection.get());
              this.hide();
          }
      });

}

//To block the prvilege status
function delete_restrict()
{
    jalert("You have no privilege to delete!");
}
function edit_restrict()
{
    jalert("You have no privilege to edit!");
}
function search_multiple_branch_report(module){

   var selectedValues = $('#sub_branch_id').val();
   search_param = jQuery("#search_form").serialize();
    //alert(search_param);
    arr_data = jQuery("#search_form").serializeArray();
    //alert(arr_data);
    search_status = false;
    search_query = "";
    $.each(arr_data, function(){
        //alert(this.name+"  "+this.value)
        if(this.value!=""){
            search_status = true;
            search_query += "&search_"+this.name+"="+this.value+"&searchOption="+selectedValues;
        }

    });
    if(!search_status){
        jalert("Atleast one search item require");
        return false;
    }
    callAjaxRequest(module+'.php', 'type=search&'+search_query, '#content_div', 'POST', 'html', true, true);
}
function search_items(module){

    search_param = jQuery("#search_form").serialize();
    //alert(search_param);
    arr_data = jQuery("#search_form").serializeArray();
    //alert(arr_data);
    search_status = false;
    search_query = "";


    $.each(arr_data, function(){
        //alert(this.name+"  "+this.value)
        if(this.value!=""){
            search_status = true;
            search_query += "&search_"+this.name+"="+this.value;
        }

    });


    if(!search_status){
        jalert("Atleast one search item require");
        return false;
    }

    callAjaxRequest(module+'.php', 'type=search&'+search_query, '#content_div', 'POST', 'html', true, true);


}


function search_enter_action(module){

    $("#search_form").keypress(function (evt) {
        if(evt.keyCode == 13) {
            search_items(module);
        }
    });

    set_date_picker();

}


function serialize_json(a){

    var o = {};

    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;

}

function show_div(div_id){
    jQuery("#"+div_id).show("slide", { direction: "up",  mode : "hide" }, 500);
}

function hide_div(div_id){
    jQuery("#"+div_id).hide("slide", { direction: "up",  mode : "show" }, 500);
}

function drop_div(div_id){
    if(jQuery("#"+div_id).css('display') == "none"){
        show_div(div_id);
    }else{
        hide_div(div_id);
    }
}

//Request amount display for the Allocation of branch
function allocation_amt(id,branch_id)
{
   jQuery.ajax({

            type: "POST",
            url: "allocation_of_amount.php",
            data:{

                "id" : id,
                "branch_id":branch_id
            },
            success: function(msg)
           {
            //msg= msg.replace(/^[\s]+/g,"");
            /*if(msg=="Amount allocated")
                       {
                            jalert("Already amount allocated");
                            $("#amount_transf1").attr("disabled",true);
                       }
            else
                    {

                        $("#amount_transf1").removeAttr("disabled");
                    }
            */
                jQuery("#amount_req").html(msg);
           }
        });
        return true;
}


function show_sub_list(module,fld_name,fld_value){
	    img_id = '#img_drop_'+fld_value;
    img_src = jQuery(img_id).attr("src");
    div_id = 'sublist_'+fld_value;
    //alert(img_src);
    if(img_src.indexOf('plus-sign.jpg') > 0){

        jQuery(img_id).attr("src","./images/minus-sign.jpg");

        //alert(rdate)
        search_param = jQuery("#search_form").serialize();
        //alert(search_param);
        arr_data = jQuery("#search_form").serializeArray();
        //alert(arr_data);
        search_status = false;
        search_query = "";


        $.each(arr_data, function(){
            //alert(this.name+"  "+this.value)

            if(this.name == 'sdate' || this.name == 'edate' || this.name == 'date'  ){
                if(fld_name == 'date'){
                    if(fld_value != '') search_query += "&search_"+this.name+"="+fld_value;
                }else{
                    if(this.value != '') search_query += "&search_"+this.name+"="+this.value;
                }
            }else if(this.name == fld_name){
                search_query += "&search_"+this.name+"="+fld_value;
            }else if(this.value!=""){
                search_status = true;
                search_query += "&search_"+this.name+"="+this.value;
            }

        });
        if(module == 'category_wise_report') {
            //For the category wise report module
            search_query += "&cat_id=" + fld_value;
            //alert("Search Query : " + search_query);
        }
        //alert("Search Query : " + search_query);


        jQuery('#'+div_id).html(loader);

        show_div(div_id);

        callAjaxRequest(module+'.php', 'type=sublist&'+search_query, '#'+div_id, 'POST', 'html', true, true);

    }else{

        jQuery(img_id).attr("src",'./images/plus-sign.jpg');
        hide_div(div_id);

    }


}


function show_sub_list_by_query(module, search_query, fld_value){

    //alert(fld_name+" , "+fld_value)
    img_id = '#img_drop_'+fld_value;
    img_src = jQuery(img_id).attr("src");
    div_id = 'sublist_'+fld_value;

    if(img_src.indexOf('plus-sign.jpg') > 0){

        jQuery(img_id).attr("src","./images/minus-sign.jpg");
        search_param = jQuery("#search_form").serialize();
        arr_data = jQuery("#search_form").serializeArray();
        search_status = false;

        $.each(arr_data, function(){


            if(search_query.indexOf('search_'+this.name) <= 0 && this.value!=""){
                search_status = true;
                search_query += "&search_"+this.name+"="+this.value;
            }

        });
		//alert(search_query);


        jQuery('#'+div_id).html(loader);
        show_div(div_id);
        callAjaxRequest(module+'.php', 'type=sublist&'+search_query, '#'+div_id, 'POST', 'html', true, true);

    }else{

        jQuery(img_id).attr("src",'./images/plus-sign.jpg');
        hide_div(div_id);

    }


}



/************ All Dialog box config function *************/

jQuery(document).ready(function() {

        //Form Action Box
        jQuery("#formact_dialog").dialog( {
                dialogClass: 'confirm',
                buttons: {
                    "Save": function() {
                        form_action_save();

                    },


                    "Cancel": function() {
                        jQuery(this).dialog("close");
                    }
                },
                autoOpen: false,
                resizable: true,
                modal: true,
                width:800,
                height:600,
                create: function(){
                    jQuery(this).dialog("close");
                }
                });


        //Confirm Box
        jQuery("#confirm_box").dialog( {
            dialogClass: 'confirm',
            buttons: {
                "Yes": function() {
                    jQuery(this).dialog("close");
                    eval(confirmYesFunction);
                },
                "No": function() {
                    jQuery(this).dialog("close");
                    eval(confirmNoFunction);
                }
            },
            autoOpen: false,
            resizable: true,
            modal: true,
            width:800,
            height:'auto',
            position:['center',150],
            create: function(){
                jQuery(this).dialog("close");
            }
        });

        jQuery("#confirm_box").dialog("close");


        //Alert Box
        jQuery("#alert_box").dialog({
        dialogClass: 'alert',
        width:600,
        buttons:{
            "OK" : function(){
                jQuery(this).dialog("close");
            }
        },
        create: function(){
            jQuery(this).dialog("close");
        }
        });
        jQuery("#alert_box").dialog("close");



        //Print Preview Box
        jQuery("#print_dialog").dialog( { dialogClass: 'confirm',
        buttons: {
            "Print": function() {
                print_page();
            },

            "Close": function() {
                jQuery(this).dialog("close");
            }
        },
        modal: true,
        width:800,
        height:600,
        create: function(){
            jQuery(this).dialog("close");


        }
        });
        jQuery("#print_dialog").dialog("close");
        collapse_all_view();

});

/*XL Sheet and PDF optimization report*/
function xls_report(values)
{

/*Changed on 31-Aug-2012*/


    /*Get the search field values*/
    /*var sdate = jQuery("#sdate").val();
    var edate = jQuery("#edate").val();

    var search_res = values+"&search_sdate="+sdate+"&search_edate="+edate;
    //alert(ss);
    //Days finding
    //Total time for one day

    var one_day=1000*60*60*24;
    //Here we need to split the inputed dates to convert them into standard format for furter execution
    var x=sdate.split("-");
    var y=edate.split("-");
    //date format(Fullyear,month,date)
    var date1=new Date(x[2],(x[1]-1),x[0]);
    var date2=new Date(y[2],(y[1]-1),y[0])
    var month1=x[1]-1;
    var month2=y[1]-1;

    //Calculate difference between the two dates, and convert to days
    diff = Math.ceil((date2.getTime()-date1.getTime())/(one_day));

    //callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);
    if((sdate == '') && (edate== ''))
    {
        jalert("Please select the date interval between "+ max_days + "days in report filter search");
        return false;
    }

    else if(diff <= max_days){
            var res= window.location=search_res;
            //document.location=values;
            //alert(a);
            return true;
        }
    else
    {
                jalert("Selected date difference must be less than "+ max_days + "days");
                return false;

    }*/

    window.location=values;

}



function pdf_report(values)
{

/*Changed on 31-Aug-2012*/


    /*Get the search field values*/
    /*var sdate = jQuery("#sdate").val();
    var edate = jQuery("#edate").val();

    var search_res = values+"&search_sdate="+sdate+"&search_edate="+edate;
    //alert(ss);
    //Days finding
    //Total time for one day

    var one_day=1000*60*60*24;
    //Here we need to split the inputed dates to convert them into standard format for furter execution
    var x=sdate.split("-");
    var y=edate.split("-");
    //date format(Fullyear,month,date)
    var date1=new Date(x[2],(x[1]-1),x[0]);
    var date2=new Date(y[2],(y[1]-1),y[0])
    var month1=x[1]-1;
    var month2=y[1]-1;

    //Calculate difference between the two dates, and convert to days
    diff = Math.ceil((date2.getTime()-date1.getTime())/(one_day));

    //callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);
    if((sdate == '') && (edate== ''))
    {
        jalert("Please select the date interval between "+ max_days + "days in report filter search");
        return false;
    }

    else if(diff <= max_days){
            var res= window.location=search_res;
            //document.location=values;
            //alert(a);
            return true;
        }
    else
    {
                jalert("Selected date difference must be less than "+ max_days + "days");
                return false;

    }*/

    window.location=values;

}

function print_preview(page,div_id,id,param,title, width, height,status)
{
    loading_html = jQuery("#page_loading").html();
    openDialog(div_id,loading_html,title, width, height);

    process_type = "preview"
    callAjaxRequest(page+".php?type="+process_type+"&id="+id+"&status="+status, param, "#"+div_id, "POST", "html", true, false);

    return false;

}


