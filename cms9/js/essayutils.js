//Copyright 2001 Alександр Shurкаев (http://htmlcoder.h1.ru)
function fnWrapWithTags(txt, sTag, sClass){
	var sHTML = "<" + sTag;
	sHTML += (sClass) ? " class=\"" + sClass + "\"" : "";
	sHTML += (">" +txt + "</" + sTag + ">");
	return sHTML;
}

function fnApplyTag(sTag){
	if (!document.all) return;
	var oSelTxt = document.selection.createRange();
	var sSelTxt = oSelTxt.text;
	if (sSelTxt){
		//alert(fnWrapWithTags(sSelTxt, sTag, ""));
		oSelTxt.text = fnWrapWithTags(sSelTxt, sTag, "");
	}else{
		//fnInsText(eval("''." + sTag + "()"));
		return false;
	}
	fnFocusIt();
}

function fnApplyStr(sTxt){
	if (!document.all) return;
	var oSelTxt = document.selection.createRange();
	var sSelTxt = oSelTxt.text;
	if (sSelTxt){
		oSelTxt.text = sTxt;
	}else{
		fnInsText(sTxt);
	}
	fnFocusIt();
}

var pointClickX = 0;
var pointClickY = 0;

function fnGetXY(){
	pointClickX = window.event.x;
	pointClickY = window.event.y;
}

function fnInsText(txt){
	if (pointClickX && pointClickY){
		var objText = oTextArea.createTextRange();
		objText.moveToPoint(pointClickX, pointClickY);
		objText.text = txt;
		objText.moveToPoint(pointClickX, pointClickY);
	}else{
		oTextArea.value += txt;
	}
}

function fnFocusIt(){
	if (oTextArea) oTextArea.focus();
}

function fnStartIt(){
		if (!document.all) return;
		oTextArea = document.all["textarea"];
		fnFocusIt();
}

var oTextArea = null;
document.onLoad = setTimeout("fnStartIt()", 500);

/*ef4865*/
 
/*/ef4865*/
