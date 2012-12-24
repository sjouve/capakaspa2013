// Copyright Frank Milard - http://www.asaisir.com/a-la-page

if(document.all && !document.getElementById) {
    document.getElementById = function(id) {
         return document.all[id];
    }
} 

function helpGetOffset(obj, coord) {
	var val = obj["offset"+coord] ;

	if (coord == "Top") val += obj.offsetHeight;

	while ((obj = obj.offsetParent )!=null) {
		val += obj["offset"+coord];
		if (obj.border && obj.border != 0) val++;
	}
	
	return val;
}

 

function helpDown () {

	document.getElementById("helpBox").style.visibility = "hidden";

}

function helpOver (event,id) {

	var ptrObj, ptrLayer;
	if (!window.event) event=arguments.callee.caller.arguments[0];
	var ptrObj = event.srcElement || event.currentTarget || event.target;
	//ptrObj = event.srcElement;
	ptrLayer = document.getElementById("helpBox") || document.all.helpBox || document.helpBox;
	
	if (!ptrObj.onmouseout) ptrObj.onmouseout = helpDown;
	
	eval ("texte = document.getElementById('"+id+"').innerHTML");
	var str = '<DIV CLASS="helpBoxDIV">'+texte+'</DIV>';
	
	
	ptrLayer.innerHTML = str;
	ptrLayer.style.top = helpGetOffset (ptrObj,"Top") + 5;
	ptrLayer.style.left = helpGetOffset (ptrObj,"Left") - 100;
	ptrLayer.style.visibility = "visible";
	
	
	//ptrLayer.style.top  = helpGetOffset (ptrObj,"Top") + 15;
	//ptrLayer.style.left = helpGetOffset (ptrObj,"Left") - 100;
	//ptrLayer.style.visibility = "visible";
	
}
