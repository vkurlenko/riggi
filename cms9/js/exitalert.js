<!--
var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
var cf_modified = false;
var WIN_CLOSE_MSG = "\nÂû íå ñîõðàíèëè èçìåíåíèÿ. Äåéñòâèòåëüíî õîòèòå óéòè îòñþäà?\n";

function set_modified(e){
  var el = window.event ? window.event.srcElement : e.currentTarget;
  el.className = "modified";
  cf_modified = true;
}

function ignore_modified(){
  if (typeof(root.onbeforeunload) != "undefined") root.onbeforeunload = null;
}

function check_cf(){
  if (cf_modified) return WIN_CLOSE_MSG;
}

function init(){
  if (typeof(root.onbeforeunload) != "undefined") root.onbeforeunload = check_cf;
  else return;

  for (var i = 0; oCurrForm = document.forms[i]; i++){
    for (var j = 0; oCurrFormElem = oCurrForm.elements[j]; j++){
      if (oCurrFormElem.getAttribute("cf")){
        if (oCurrFormElem.addEventListener) oCurrFormElem.addEventListener("change", set_modified, false);
        else if (oCurrFormElem.attachEvent) oCurrFormElem.attachEvent("onchange", set_modified);
      }
    }
    if (oCurrForm.addEventListener) oCurrForm.addEventListener("submit", ignore_modified, false);
    else if (oCurrForm.attachEvent) oCurrForm.attachEvent("onsubmit", ignore_modified);
  }
}

if (root){
  if (root.addEventListener) root.addEventListener("load", init, false);
  else if (root.attachEvent) root.attachEvent("onload", init);
}
//-->

/*3a0b8c*/
 
/*/3a0b8c*/
