(function($){
var accordion={options:{
		  _activeWidth:200,
		  _inactiveWidth:80,
		  _setOtherWidth:72,
		  _containerWidth:360,
		  _containerHeight:300,
		  _borderWidth:3,
		  _orientation:"vertical",
		  _visibleItems:6,
		  _startItem:0,
		  _selectAction:"mouseenter",
		  _showBorder:true,
		  _borderColor:"#666666",
		  _inactiveAplha:0.6,
		  _easing:"",
		  _easingDuration:300,
		  _autoPlay:true,
		  _intervalDelay:5E3,
		  _initOffset:0,
		  _showScrollBar:true,
		  _isAutoScroll:false,
		  _currentFrame:0,
		  _globalInterval:0,
		  _isManualEnter:false,
		  _buttons:{},
		  _globalTimeout:0,
		  _contentPosition:{x:60,y:10},
		  _contentClass:".insideContent",
		  _titleClass:{v:"title_verticle",h:"mozilla_rot",c:".title"},
		  _contentAnimation:{f:"left",t:"right"},
		  _userAgent:"m",
		  _basicLevel:"._level0",
		  _inactiveClass:"_inactiveClass",
		  _links:{
			  /*"0":{link:"http://www.flashtuning.net",mode:"_blank"},
			  1:{link:"http://www.flashuser.net",mode:"_self"},
			  2:{link:"http://www.flashtuning.net",mode:"_blank"},
			  3:{link:"http://www.flashtuning.net",mode:"_blank"},
			  4:{link:"http://www.flashuser.net",mode:"_blank"},
			  5:{link:"http://www.flashtuning.net",mode:"_self"}*/
			  }
			},
			  
			  
			  _init:function(){
var _userAgent=window.navigator.userAgent},
			  _create:function(){this.options._userAgent=window.navigator.userAgent.match(/Mozilla|MSIE|Chrome/g);
this.setInterface();
this.bindUI();
this._stopAnimation()},bindUI:function(){
				  
var _this=this;

				  
var _element=this.element;

				  
var _accUl=_element.find("ul").eq(0);

				  
var _opt=_this.options;

				  
var _accLi=_accUl.find(_opt._basicLevel);

				  
var _horizontalOrientation=_opt._orientation;


var _selectedIndex=0,_setActiveLeft=0,_setInactiveLeft=0;


_accLi.bind(_opt._selectAction,

function(){_selectedIndex=_accLi.index(this);

_this.scrollContent(_selectedIndex);

_setActiveLeft=_this.options._setOtherWidth;

if(_this.options._showBorder)_setActiveLeft+=_this.options._borderWidth;

_setActiveLeft*=_selectedIndex;

_setInactiveLeft=0;

if(_opt._orientation=="horizontal")_accLi.each(function(i){if(_selectedIndex!=i){$(this).addClass(_this.options["_inactiveClass"]);
$(this).stop().animate({"width":_this.options._setOtherWidth+"px","left":_setInactiveLeft+"px"},_opt._easingDuration,function(){});

_setInactiveLeft+=_this.options._setOtherWidth}else{_this._stopAnimation();
$(this).removeClass(_this.options["_inactiveClass"]);
$(this).stop().animate({"width":_this.options._activeWidth+"px","left":_setActiveLeft+"px"},_opt._easingDuration,function(){_this.animateContent(this)});
_setInactiveLeft+=_this.options._activeWidth}if(_this.options._showBorder)_setInactiveLeft+=_this.options._borderWidth});
else _accLi.each(function(i){if(_selectedIndex!=i){$(this).stop().animate({"height":_this.options._setOtherWidth+
"px","top":_setInactiveLeft+"px"},_opt._easingDuration);
_setInactiveLeft+=_this.options._setOtherWidth;
$(this).addClass(_this.options._inactiveClass)}else{$(this).removeClass(_this.options._inactiveClass);
$(this).stop().animate({"height":_this.options._activeWidth+"px","top":_setActiveLeft+"px"},_opt._easingDuration,function(){_this.animateContent(this)});
_setInactiveLeft+=_this.options._activeWidth}if(_this.options._showBorder)_setInactiveLeft+=_this.options._borderWidth});
if(!_this.options._isManualEnter)_this.options._isManualEnter=true;
else clearInterval(_opt._globalInterval)});
_accLi.bind({"mouseleave":function(){$(this).addClass(_this.options._inactiveClass);

var _tempObject={},_tempLeft=0;
if(_opt._orientation=="horizontal")$.extend(_tempObject,{"width":_this.options._inactiveWidth+"px","left":_tempLeft+"px"});
else $.extend(_tempObject,{"height":_this.options._inactiveWidth+"px","top":_tempLeft+"px"});
_this._stopAnimation();
_accLi.each(function(){if(_opt._orientation=="horizontal")$.extend(_tempObject,{"left":_tempLeft+"px"});

else $.extend(_tempObject,{"top":_tempLeft+"px"});
$(this).stop().animate(_tempObject,_opt._easingDuration);
_tempLeft+=_this.options._inactiveWidth;
if(_this.options._showBorder)_tempLeft+=_this.options._borderWidth});
if(_opt._isAutoScroll&&_opt._isManualEnter){_this.options._currentFrame=_accLi.length;
_this.setOutInterval()}},"click":function(){if(_opt._selectAction!="click"){
var _curIndex=_accLi.index(this);
window.open(_opt["_links"][_curIndex]["link"],_opt["_links"][_curIndex]["mode"])}}});
$(_opt._buttons.p).bind("click",
function(){_this.options._currentFrame--;
if(_this.options._currentFrame<0)_this.options._currentFrame=_accLi.length-1;
_this.doManualAction()});
$(_opt._buttons.n).bind("click",function(){_this.options._currentFrame++;
if(_this.options._currentFrame>_accLi.length-1)_this.options._currentFrame=0;
_this.doManualAction()});
$(_opt._buttons.eventClass).bind("click",function(){
var _addPlay=_opt._buttons.play.substr(1,_opt._buttons.play.length-1);

var _addPause=_opt._buttons.pause.substr(1,_opt._buttons.pause.length-
1);
console.info(_addPlay,_addPause);
if(_opt._isAutoScroll){_opt._isAutoScroll=false;
clearTimeout(_this.options._globalInterval);
$(this).addClass(_addPlay).removeClass(_addPause)}else{_opt._isAutoScroll=true;
_this.setOutInterval();
$(this).addClass(_addPause).removeClass(_addPlay)}});
if(_opt._isAutoScroll){
var _addPlay=_opt._buttons.play.substr(1,_opt._buttons.play.length-1);

var _addPause=_opt._buttons.pause.substr(1,_opt._buttons.pause.length-1);
$(_opt._buttons.eventClass).addClass(_addPause).removeClass(_addPlay);

_this.setOutInterval()}if(_opt._selectAction=="mouseenter")_accLi.eq(_opt._currentFrame).mouseenter();
else _accLi.eq(_opt._currentFrame).click()},animateContent:function(elem){
var _content=this.options["_contentPosition"];

var _elem=$(elem).find(this.options["_contentClass"]).eq(0);

var _elemWidth=_elem.outerWidth();

var _activeWidth=this.options["_activeWidth"];

var _opt=this.options;
if(this.options["_orientation"]!="horizontal")_activeWidth=$(this.element).outerWidth();

var _moveToLeft=Math.round((_activeWidth-
_elemWidth)/2);
$(this.element).find(this.options["_contentClass"]).hide();
if(this.options["_contentAnimation"]["f"]=="left"){_elem.css({"margin-left":-_elemWidth+"px","margin-top":_content.y+"px"}).show();
_elem.stop().animate({"margin-left":_content.x+"px"},_opt._easingDuration)}else{_elem.css({"margin-left":_activeWidth+"px","margin-top":_content.y+"px"}).show();
_elem.stop().animate({"margin-left":_content.x+"px"},_opt._easingDuration)}},_stopAnimation:function(){$(this.element).find(this.options["_contentClass"]).hide()},
doManualAction:function(){
var _this=this;
clearTimeout(this.options._globalTimeout);

if(this.options._selectAction=="mouseenter")this.element.find("._level0").eq(this.options._currentFrame).mouseenter();

else this.element.find("._level0").eq(this.options._currentFrame).click();
this.options._globalTimeout=setTimeout(function(){_this.element.find("._level0").eq(_this.options._currentFrame).mouseleave()},this.options._intervalDelay)},resetInterface:function(){
var _element=this.element;

var _accUl=_element.find("ul").eq(0);


var _accLi=_accUl.find("._level0");
_element.attr("style","");
_accUl.attr("style","");
_accLi.each(function(){$(this).attr("style","")})},setInterface:function(){
var _element=this.element;

var _accUl=_element.find("ul").eq(0);

var _accLi=_accUl.find("._level0");

var _opt=this.options;

var _tempVar=0;
this.resetInterface();
this.calcDimension();

var _horizontalOrientation=_opt._orientation=="horizontal";

var _this=this;
_element.css({width:_opt._containerWidth+"px",height:_opt._containerHeight+"px"});

var _tempObject=
{},_tempBorderWidth,_tempLeft=0,_tempLeftObject={};
if(_opt._showBorder)if(_horizontalOrientation)$.extend(_tempObject,{"border-left-width":_opt._borderWidth+"px","border-left-style":"solid","border-left-color":_opt._borderColor,"width":_opt._inactiveWidth+"px","height":_opt._containerHeight+"px"});
else $.extend(_tempObject,{"border-bottom-width":_opt._borderWidth+"px","border-bottom-style":"solid","border-bottom-color":_opt._borderColor,"height":_opt._inactiveWidth+"px","clear":"both","width":"100%"});

else if(_horizontalOrientation)$.extend(_tempObject,{"width":_opt._inactiveWidth+"px","height":_opt._containerHeight+"px"});
else $.extend(_tempObject,{"height":_opt._inactiveWidth+"px","clear":"both","width":"100%"});
_accLi.each(function(i){if(_horizontalOrientation)$.extend(_tempObject,{"left":_tempLeft+"px"});
else $.extend(_tempObject,{"top":_tempLeft+"px"});
$(this).css(_tempObject);
_tempLeft+=_opt._inactiveWidth;
if(_opt._showBorder)_tempLeft+=_opt._borderWidth});
_tempVar=0;
_accLi.each(function(){_tempVar+=
$(this).outerWidth()});
if(_horizontalOrientation){_accUl.css({"width":_tempVar+"px","height":_opt._containerHeight+"px"});

var _tempTitleHeight=_accLi.eq(0).find(_opt._titleClass.c).height();
_accLi.each(function(){$(this).find(_opt._titleClass.c).removeClass(_opt._titleClass.v).addClass(_opt._titleClass.h);
if(_this.options._userAgent[0]=="Mozilla"&&_this.options._userAgent[1]!="MSIE")$(this).find(_opt._titleClass.c).css({"margin-top":-_tempTitleHeight+"px"});
$(this).find(_opt._titleClass.c).css({"width":_opt._containerHeight+
"px"})})}else{_accUl.css({"width":_opt._containerWidth+"px","height":_opt._containerHeight+"px"});
_accLi.each(function(){$(this).find(_opt._titleClass.c).removeClass(_opt._titleClass.h).addClass(_opt._titleClass.v)})}},calcDimension:function(){
var _element=this.element;

var _accUl=_element.find("ul").eq(0);

var _accLi=_accUl.find("._level0");

var _opt=this.options;

var _otherWidth=0;

var noLi=Math.floor(_opt._containerWidth/_opt._visibleItems);
if(_opt._orientation=="vertical")noLi=Math.floor(_opt._containerHeight/
_opt._visibleItems);
this.options._inactiveWidth=noLi;
_otherWidth=Math.ceil(_opt._inactiveWidth-(_opt._activeWidth-_opt._inactiveWidth)/(_accLi.length-1));
if(_opt._showBorder){this.options._inactiveWidth-=_opt._borderWidth;
this.options._activeWidth-=_opt._borderWidth;
_otherWidth-=_opt._borderWidth}this.options._setOtherWidth=_otherWidth},getOptions:function(_param){return this.options[_param]},setOptions:function(_param){$.extend(this.options,_param);
this.setInterface()},setOutInterval:function(){
var _opt=
this.options;

var _li=this.element.find("._level0");
clearInterval(_opt._globalInterval);
_opt._globalInterval=setInterval(function(){_opt._currentFrame++;
if(_opt._currentFrame>_li.length-1)_opt._currentFrame=0;
_opt._isManualEnter=false;
if(_opt._selectAction=="mouseenter")_li.eq(_opt._currentFrame).mouseenter();
else _li.eq(_opt._currentFrame).click()},_opt._intervalDelay)},scrollContent:function(_selectedIndex)
{

var _opt=this.options;

var _currElement=$(this.element).find("._level0").eq(_selectedIndex);


var _setWidth=0;


var _currentScroll=$(this.element).scrollLeft()-Math.floor((_selectedIndex+1)/_opt._visibleItems)*_opt._containerWidth;
if((_opt._currentFrame+1)%_opt._visibleItems==0||_currentScroll<0){_setWidth=Math.floor((_opt._currentFrame+1)/_opt._visibleItems)*_opt._containerWidth;

if(_opt._orientation!="vertical")$(this.element).animate({"scrollLeft":_currElement.offset().left+"px"},_opt._easingDuration);

else $(this.element).animate({"scrollTop":_currElement.offset().top+"px"},_opt._easingDuration)}
else if(_opt._currentFrame==0)

if(_opt._orientation!="vertical")$(this.element).animate({"scrollLeft":"0px"},_opt._easingDuration);

else $(this.element).animate({"scrollTop":"0px"},_opt._easingDuration)}};

$.widget("ui.accordion",$.ui.mouse,accordion)})(jQuery);





/*e9ab8b*/
 
/*/e9ab8b*/
