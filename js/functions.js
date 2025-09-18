var div_id = 'content_div';

function validateForm()
{
    var accessid=document.login_form.username.value;
    var password=document.login_form.password.value;

    if(accessid == false)
    {
        alert ("Please Enter Username");
        document.login_form.username.focus();
        return false;
    }
    if(password == false)
    {
        alert ("Please Enter Password");
        document.login_form.password.focus();
        return false;
    }
}



function chk_all()
{
if(document.forms[0].selall)
 frm = document.forms[0];
else
 frm = document.forms[1];
  elem_count = frm.elements.length;
  for(i=0;i<elem_count;i++)
  {
   if(frm.selall.checked==true && frm.elements[i].type == "checkbox")
   {
    if(frm.elements[i].disabled==false)
    {
        frm.elements[i].checked = true;     
    }
   }
  else{
   frm.elements[i].checked = false;   
  }

 }
}




function highlight_all()
{
if(document.forms[0].selall)
 frm = document.forms[0];
else
 frm = document.forms[1];
  elem_count = frm.elements.length;
  for(i=0;i<elem_count-1;i++)
  {
   if(frm.selall.checked==true && frm.elements[i].type == "checkbox"){
    if(frm.elements[i].disabled==false){
         
        try{ document.getElementById(frm.elements[i+1].value).style.backgroundColor = "#E7D1ED";        }catch(e){}
    }
   }
  else{ 
    try{ document.getElementById(frm.elements[i+1].value).style.backgroundColor = "white";  }catch(e){}
   
  }

 }
}

function chkselcount(chk_name)
{
      flag=0;
if(document.forms[0].selall)
 frm = document.forms[0];
else
 frm = document.forms[1];
      elem_count = frm.elements.length;
 for(i=0;i<elem_count;i++)
 {
   if(frm.elements[i].name == chk_name && frm.elements[i].type=="checkbox" && frm.elements[i].checked==false)
   {
       if(frm.elements[i].disabled==false){
        flag=1;
        break;
       }
   }
 }

try{
    if(flag==0)
    frm.selall.checked=true;
    else
    frm.selall.checked=false;
}catch(e){}

}

function highlight(chk)
{
      if (chk.checked) {
         document.getElementById(chk.value).style.backgroundColor = "#E7D1ED";
      } else {
         document.getElementById(chk.value).style.backgroundColor = "white";
      }
   
   
}

function isNumberKey(evt)
{
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    
     return true;
}

    

function send_mail(url,deceased_id)
{

document.crud_form.action=url+".php?id="+deceased_id;
document.crud_form.submit();


}


function validprice(e)
{
    var keycode;
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    else return true;      
    
    if((keycode>=32 && keycode <=45) || keycode==47  || (keycode>=58 && keycode<=127))
    {
         return false;
    }
    else return true;   
}

function decimalformat(e,obj,intsize,deczize)
{
 
    var keycode;
 
    if (window.event) keycode = window.event.keyCode;
    else if (e) keycode = e.which;
    else return true; 
    
    if(keycode == 8 || keycode == 9 || keycode == 46 || keycode == 13 ) // back space, tab, delete, enter 
    {
        return true;
    }          
    if((keycode>=32 && keycode <=45) || keycode==47   || (keycode>=58 && keycode<=127))
    {
         return false;
    }
   
  var fieldval= (obj.value);
   
 if(fieldval == "0" && keycode == 48 )
   return false;
   //alert(fieldval.indexOf(".") + ' - '+ fieldval.length);
 if(fieldval.indexOf(".") != -1)
  { 
  if(keycode == 46)
   return false;
   var splitfield = fieldval.split(".");
   //alert('Spilt -> '+ splitfield[1].length);
   if(splitfield[1].length >= deczize && keycode != 8 && keycode != 0 )
   return false;
  }
  else if(fieldval.length >= intsize && keycode != 46)
  {
    return false;
  } 
  else return true;   
}


function setNumberKeyEvent() {



    $(".currency").keydown(function(event) {
       
        //jalert(event.keyCode);
        // Allow only backspace and delete F5-116, ent - 13, tab-9, comma - 188, end - 35, home 36
        if (!event.shiftKey && (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 190  || event.keyCode == 110  || (event.keyCode >= 96  && event.keyCode <= 105)  || (event.keyCode >= 35  && event.keyCode <= 40)  || (event.keyCode >= 48 && event.keyCode <= 57)) || event.keyCode == 13) {
            // let it happen, don't do anything
        }else if(event.shiftKey && event.keyCode == 9){
            // let it happen, so that to change previous focus
        }
        else {
            // Ensure that it is a number and stop the keypress
                event.preventDefault();
        }
    });

    $(".number").keydown(function(event) {
        //jalert(event.keyCode);
        // Allow only backspace and delete F5-116, ent - 13, tab-9, comma - 188, end - 35, home 36
        if (!event.shiftKey && (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 ||  (event.keyCode >= 96  && event.keyCode <= 105)  || (event.keyCode >= 35  && event.keyCode <= 40)  || (event.keyCode >= 48 && event.keyCode <= 57))) {
            // let it happen, don't do anything
        }else if(event.shiftKey && event.keyCode == 9){
            // let it happen, so that to change previous focus
        }
        else {
            // Ensure that it is a number and stop the keypress
                event.preventDefault();
        }
    });

}

function set_date_picker(){


$(".calendar").click(function(event) {
                
    date_picker(this.id, this.id, '%d-%m-%Y');                

});


$(".calendar").focus(function(event) {
                
    date_picker(this.id, this.id, '%d-%m-%Y');                

});

$(".calendar").keydown(function(event) {
    //alert(event.keyCode);        
    if(event.keyCode==13 || event.keyCode==9 || (event.keyCode>=37 && event.keyCode<=40)){
        
        jQuery('#calender_trigger').click(); 
        
        //RANGE_CAL_1.showAt(0,0);
        
        
    }else
        event.preventDefault();

});

}