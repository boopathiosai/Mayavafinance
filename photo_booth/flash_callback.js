/**
* Picture capture flash event callback functions
**/
function picture_saved(str)
{	
	hideOverlay();
	alert(str);
}

function picture_save_error(str)
{	
	hideOverlay();
	alert(str);
}