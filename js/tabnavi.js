
function initTopNav() {
  
   /*$('a.trigger').keydown(function(event){
        if (event.keyCode == 13){
            $(this).next().show();
        }
   });*/

   $('a.trigger').focus(function(i){
        /*$('#menuwrapper li ul').hide();
        $(this).next().show();
        /*anylinkmenu.showmenu(i)
        $(this).mouseover();*/
   });

  
}  
  
  
$(document).ready(function() {
    initTopNav();
});


