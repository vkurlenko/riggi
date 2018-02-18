/*! Cross-browser console log */
/*;window.log = function(){
  log.history = log.history || [];   // store logs to an array for reference
  log.history.push(arguments);
  arguments.callee = arguments.callee.caller;  
  if(this.console) console.log( Array.prototype.slice.call(arguments) );
};*/
// make it safe to use console.log always
//(function(b){function c(){}for(var d="assert,count,debug,dir,dirxml,error,exception,group,groupCollapsed,groupEnd,info,log,markTimeline,profile,profileEnd,time,timeEnd,trace,warn".split(","),a;a=d.pop();)b[a]=b[a]||c})(window.console=window.console||{});

/*
 * jQuery Address Plugin v1.5
 * http://www.asual.com/jquery/address/
 *
 * Copyright (c) 2009-2010 Rostislav Hristov
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Date: 2012-11-18 23:51:44 +0200 (Sun, 18 Nov 2012)
 */
(function ($) {

    $.address = (function () {

        var _trigger = function(name) {
               var ev = $.extend($.Event(name), 
                 (function() {
                            var parameters = {},
                                parameterNames = $.address.parameterNames();
                            for (var i = 0, l = parameterNames.length; i < l; i++) {
                                parameters[parameterNames[i]] = $.address.parameter(parameterNames[i]);
                            }
                            return {
                                value: $.address.value(),
                                path: $.address.path(),
                                pathNames: $.address.pathNames(),
                                parameterNames: parameterNames,
                                parameters: parameters,
                                queryString: $.address.queryString()
                            };
                        }).call($.address)
                    );

               $($.address).trigger(ev);
               return ev;
            },
            _array = function(obj) {
                return Array.prototype.slice.call(obj);
            },
            _bind = function(value, data, fn) {
                $().bind.apply($($.address), Array.prototype.slice.call(arguments));
                return $.address;
            },
            _unbind = function(value,  fn) {
                $().unbind.apply($($.address), Array.prototype.slice.call(arguments));
                return $.address;
            },
            _supportsState = function() {
                return (_h.pushState && _opts.state !== UNDEFINED);
            },
            _hrefState = function() {
                return ('/' + _l.pathname.replace(new RegExp(_opts.state), '') + 
                    _l.search + (_hrefHash() ? '#' + _hrefHash() : '')).replace(_re, '/');
            },
            _hrefHash = function() {
                var index = _l.href.indexOf('#');
                return index != -1 ? _crawl(_l.href.substr(index + 1), FALSE) : '';
            },
            _href = function() {
                return _supportsState() ? _hrefState() : _hrefHash();
            },
            _window = function() {
                try {
                    return top.document !== UNDEFINED && top.document.title !== UNDEFINED ? top : window;
                } catch (e) { 
                    return window;
                }
            },
            _js = function() {
                return 'javascript';
            },
            _strict = function(value) {
                value = value.toString();
                return (_opts.strict && value.substr(0, 1) != '/' ? '/' : '') + value;
            },
            _crawl = function(value, direction) {
                if (_opts.crawlable && direction) {
                    return (value !== '' ? '!' : '') + value;
                }
                return value.replace(/^\!/, '');
            },
            _cssint = function(el, value) {
                return parseInt(el.css(value), 10);
            },
            
            // Hash Change Callback
            _listen = function() {
                if (!_silent) {
                    var hash = _href(),
                        diff = decodeURI(_value) != decodeURI(hash);
                    if (diff) {
                        if (_msie && _version < 7) {
                            _l.reload();
                        } else {
                            if (_msie && !_hashchange && _opts.history) {
                                _st(_html, 50);
                            }
                            _old = _value;
                            _value = hash;
                            _update(FALSE);
                        }
                    }
                }
            },

            _update = function(internal) {
                var changeEv = _trigger(CHANGE),
                    xChangeEv = _trigger(internal ? INTERNAL_CHANGE : EXTERNAL_CHANGE);
                
                _st(_track, 10);

                if (changeEv.isDefaultPrevented() || xChangeEv.isDefaultPrevented()){
                  _preventDefault();
                }
            },

            _preventDefault = function(){
              _value = _old;
              
              if (_supportsState()) {
                  _h.popState({}, '', _opts.state.replace(/\/$/, '') + (_value === '' ? '/' : _value));
              } else {
                  _silent = TRUE;
                  if (_webkit) {
                      if (_opts.history) {
                          _l.hash = '#' + _crawl(_value, TRUE);
                      } else {
                          _l.replace('#' + _crawl(_value, TRUE));
                      }
                  } else if (_value != _href()) {
                      if (_opts.history) {
                          _l.hash = '#' + _crawl(_value, TRUE);
                      } else {
                          _l.replace('#' + _crawl(_value, TRUE));
                      }
                  }
                  if ((_msie && !_hashchange) && _opts.history) {
                      _st(_html, 50);
                  }
                  if (_webkit) {
                      _st(function(){ _silent = FALSE; }, 1);
                  } else {
                      _silent = FALSE;
                  }
              }
              
            },

            _track = function() {
                if (_opts.tracker !== 'null' && _opts.tracker !== NULL) {
                    var fn = $.isFunction(_opts.tracker) ? _opts.tracker : _t[_opts.tracker],
                        value = (_l.pathname + _l.search + 
                                ($.address && !_supportsState() ? $.address.value() : ''))
                                .replace(/\/\//, '/').replace(/^\/$/, '');
                    if ($.isFunction(fn)) {
                        fn(value);
                    } else if ($.isFunction(_t.urchinTracker)) {
                        _t.urchinTracker(value);
                    } else if (_t.pageTracker !== UNDEFINED && $.isFunction(_t.pageTracker._trackPageview)) {
                        _t.pageTracker._trackPageview(value);
                    } else if (_t._gaq !== UNDEFINED && $.isFunction(_t._gaq.push)) {
                        _t._gaq.push(['_trackPageview', decodeURI(value)]);
                    }
                }
            },
            _html = function() {
                var src = _js() + ':' + FALSE + ';document.open();document.writeln(\'<html><head><title>' + 
                    _d.title.replace(/\'/g, '\\\'') + '</title><script>var ' + ID + ' = "' + encodeURIComponent(_href()).replace(/\'/g, '\\\'') + 
                    (_d.domain != _l.hostname ? '";document.domain="' + _d.domain : '') + 
                    '";</' + 'script></head></html>\');document.close();';
                if (_version < 7) {
                    _frame.src = src;
                } else {
                    _frame.contentWindow.location.replace(src);
                }
            },
            _options = function() {
                if (_url && _qi != -1) {
                    var i, param, params = _url.substr(_qi + 1).split('&');
                    for (i = 0; i < params.length; i++) {
                        param = params[i].split('=');
                        if (/^(autoUpdate|crawlable|history|strict|wrap)$/.test(param[0])) {
                            _opts[param[0]] = (isNaN(param[1]) ? /^(true|yes)$/i.test(param[1]) : (parseInt(param[1], 10) !== 0));
                        }
                        if (/^(state|tracker)$/.test(param[0])) {
                            _opts[param[0]] = param[1];
                        }
                    }
                    _url = NULL;
                }
                _old = _value;
                _value = _href();
            },
            _load = function() {
                if (!_loaded) {
                    _loaded = TRUE;
                    _options();
                    var complete = function() {
                            _enable.call(this);
                            _unescape.call(this);
                        },
                        body = $('body').ajaxComplete(complete);
                    complete();
                    if (_opts.wrap) {
                        var wrap = $('body > *')
                            .wrapAll('<div style="padding:' + 
                                (_cssint(body, 'marginTop') + _cssint(body, 'paddingTop')) + 'px ' + 
                                (_cssint(body, 'marginRight') + _cssint(body, 'paddingRight')) + 'px ' + 
                                (_cssint(body, 'marginBottom') + _cssint(body, 'paddingBottom')) + 'px ' + 
                                (_cssint(body, 'marginLeft') + _cssint(body, 'paddingLeft')) + 'px;" />')
                            .parent()
                            .wrap('<div id="' + ID + '" style="height:100%;overflow:auto;position:relative;' + 
                                (_webkit && !window.statusbar.visible ? 'resize:both;' : '') + '" />');
                        $('html, body')
                            .css({
                                height: '100%',
                                margin: 0,
                                padding: 0,
                                overflow: 'hidden'
                            });
                        if (_webkit) {
                            $('<style type="text/css" />')
                                .appendTo('head')
                                .text('#' + ID + '::-webkit-resizer { background-color: #fff; }');
                        }
                    }
                    if (_msie && !_hashchange) {
                        var frameset = _d.getElementsByTagName('frameset')[0];
                        _frame = _d.createElement((frameset ? '' : 'i') + 'frame');
                        _frame.src = _js() + ':' + FALSE;
                        if (frameset) {
                            frameset.insertAdjacentElement('beforeEnd', _frame);
                            frameset[frameset.cols ? 'cols' : 'rows'] += ',0';
                            _frame.noResize = TRUE;
                            _frame.frameBorder = _frame.frameSpacing = 0;
                        } else {
                            _frame.style.display = 'none';
                            _frame.style.width = _frame.style.height = 0;
                            _frame.tabIndex = -1;
                            _d.body.insertAdjacentElement('afterBegin', _frame);
                        }
                        _st(function() {
                            $(_frame).bind('load', function() {
                                var win = _frame.contentWindow;
                                _old = _value;
                                _value = win[ID] !== UNDEFINED ? win[ID] : '';
                                if (_value != _href()) {
                                    _update(FALSE);
                                    _l.hash = _crawl(_value, TRUE);
                                }
                            });
                            if (_frame.contentWindow[ID] === UNDEFINED) {
                                _html();
                            }
                        }, 50);
                    }
                    _st(function() {
                        _trigger('init');
                        _update(FALSE);
                    }, 1);
                    if (!_supportsState()) {
                        if ((_msie && _version > 7) || (!_msie && _hashchange)) {
                            if (_t.addEventListener) {
                                _t.addEventListener(HASH_CHANGE, _listen, FALSE);
                            } else if (_t.attachEvent) {
                                _t.attachEvent('on' + HASH_CHANGE, _listen);
                            }
                        } else {
                            _si(_listen, 50);
                        }
                    }
                    if ('state' in window.history) {
                        $(window).trigger('popstate');
                    }
                }
            },
            _enable = function() {
                var el, 
                    elements = $('a'), 
                    length = elements.size(),
                    delay = 1,
                    index = -1,
                    sel = '[rel*="address:"]',
                    fn = function() {
                        if (++index != length) {
                            el = $(elements.get(index));
                            if (el.is(sel)) {
                                el.address(sel);
                            }
                            _st(fn, delay);
                        }
                    };
                _st(fn, delay);
            },
            _popstate = function() {
                if (decodeURI(_value) != decodeURI(_href())) {
                    _old = _value;
                    _value = _href();
                    _update(FALSE);
                }
            },
            _unload = function() {
                if (_t.removeEventListener) {
                    _t.removeEventListener(HASH_CHANGE, _listen, FALSE);
                } else if (_t.detachEvent) {
                    _t.detachEvent('on' + HASH_CHANGE, _listen);
                }
            },
            _unescape = function() {
                if (_opts.crawlable) {
                    var base = _l.pathname.replace(/\/$/, ''),
                        fragment = '_escaped_fragment_';
                    if ($('body').html().indexOf(fragment) != -1) {
                        $('a[href]:not([href^=http]), a[href*="' + document.domain + '"]').each(function() {
                            var href = $(this).attr('href').replace(/^http:/, '').replace(new RegExp(base + '/?$'), '');
                            if (href === '' || href.indexOf(fragment) != -1) {
                                $(this).attr('href', '#' + encodeURI(decodeURIComponent(href.replace(new RegExp('/(.*)\\?' + 
                                    fragment + '=(.*)$'), '!$2'))));
                            }
                        });
                    }
                }
            },
            UNDEFINED,
            NULL = null,
            ID = 'jQueryAddress',
            STRING = 'string',
            HASH_CHANGE = 'hashchange',
            INIT = 'init',
            CHANGE = 'change',
            INTERNAL_CHANGE = 'internalChange',
            EXTERNAL_CHANGE = 'externalChange',
            TRUE = true,
            FALSE = false,
            _opts = {
                autoUpdate: TRUE, 
                crawlable: FALSE,
                history: TRUE, 
                strict: TRUE,
                wrap: FALSE
            },
            _browser = $.browser, 
            _version = parseFloat(_browser.version),
            _msie = !$.support.opacity,
            _webkit = _browser.webkit || _browser.safari,
            _t = _window(),
            _d = _t.document,
            _h = _t.history, 
            _l = _t.location,
            _si = setInterval,
            _st = setTimeout,
            _re = /\/{2,9}/g,
            _agent = navigator.userAgent,
            _hashchange = 'on' + HASH_CHANGE in _t,
            _frame,
            _form,
            _url = $('script:last').attr('src'),
            _qi = _url ? _url.indexOf('?') : -1,
            _title = _d.title, 
            _silent = FALSE,
            _loaded = FALSE,
            _juststart = TRUE,
            _updating = FALSE,
            _listeners = {}, 
            _value = _href();
            _old = _value;
            
        if (_msie) {
            _version = parseFloat(_agent.substr(_agent.indexOf('MSIE') + 4));
            if (_d.documentMode && _d.documentMode != _version) {
                _version = _d.documentMode != 8 ? 7 : 8;
            }
            var pc = _d.onpropertychange;
            _d.onpropertychange = function() {
                if (pc) {
                    pc.call(_d);
                }
                if (_d.title != _title && _d.title.indexOf('#' + _href()) != -1) {
                    _d.title = _title;
                }
            };
        }
        
        if (_h.navigationMode) {
            _h.navigationMode = 'compatible';
        }
        if (document.readyState == 'complete') {
            var interval = setInterval(function() {
                if ($.address) {
                    _load();
                    clearInterval(interval);
                }
            }, 50);
        } else {
            _options();
            $(_load);
        }
        $(window).bind('popstate', _popstate).bind('unload', _unload);

        return {
            bind: function(type, data, fn) {
                return _bind.apply(this, _array(arguments));
            },
            unbind: function(type, fn) {
                return _unbind.apply(this, _array(arguments));
            },
            init: function(data, fn) {
                return _bind.apply(this, [INIT].concat(_array(arguments)));
            },
            change: function(data, fn) {
                return _bind.apply(this, [CHANGE].concat(_array(arguments)));
            },
            internalChange: function(data, fn) {
                return _bind.apply(this, [INTERNAL_CHANGE].concat(_array(arguments)));
            },
            externalChange: function(data, fn) {
                return _bind.apply(this, [EXTERNAL_CHANGE].concat(_array(arguments)));
            },
            baseURL: function() {
                var url = _l.href;
                if (url.indexOf('#') != -1) {
                    url = url.substr(0, url.indexOf('#'));
                }
                if (/\/$/.test(url)) {
                    url = url.substr(0, url.length - 1);
                }
                return url;
            },
            autoUpdate: function(value) {
                if (value !== UNDEFINED) {
                    _opts.autoUpdate = value;
                    return this;
                }
                return _opts.autoUpdate;
            },
            crawlable: function(value) {
                if (value !== UNDEFINED) {
                    _opts.crawlable = value;
                    return this;
                }
                return _opts.crawlable;
            },
            history: function(value) {
                if (value !== UNDEFINED) {
                    _opts.history = value;
                    return this;
                }
                return _opts.history;
            },
            state: function(value) {
                if (value !== UNDEFINED) {
                    _opts.state = value;
                    var hrefState = _hrefState();
                    if (_opts.state !== UNDEFINED) {
                        if (_h.pushState) {
                            if (hrefState.substr(0, 3) == '/#/') {
                                _l.replace(_opts.state.replace(/^\/$/, '') + hrefState.substr(2));
                            }
                        } else if (hrefState != '/' && hrefState.replace(/^\/#/, '') != _hrefHash()) {
                            _st(function() {
                                _l.replace(_opts.state.replace(/^\/$/, '') + '/#' + hrefState);
                            }, 1);
                        }
                    }
                    return this;
                }
                return _opts.state;
            },
            strict: function(value) {
                if (value !== UNDEFINED) {
                    _opts.strict = value;
                    return this;
                }
                return _opts.strict;
            },
            tracker: function(value) {
                if (value !== UNDEFINED) {
                    _opts.tracker = value;
                    return this;
                }
                return _opts.tracker;
            },
            wrap: function(value) {
                if (value !== UNDEFINED) {
                    _opts.wrap = value;
                    return this;
                }
                return _opts.wrap;
            },
            update: function() {
                _updating = TRUE;
                this.value(_value);
                _updating = FALSE;
                return this;
            },
            title: function(value) {
                if (value !== UNDEFINED) {
                    _st(function() {
                        _title = _d.title = value;
                        if (_juststart && _frame && _frame.contentWindow && _frame.contentWindow.document) {
                            _frame.contentWindow.document.title = value;
                            _juststart = FALSE;
                        }
                    }, 50);
                    return this;
                }
                return _d.title;
            },
            value: function(value) {
                if (value !== UNDEFINED) {
                    value = _strict(value);
                    if (value == '/') {
                        value = '';
                    }
                    if (_value == value && !_updating) {
                        return;
                    }
                    _old = _value;
                    _value = value;
                    if (_opts.autoUpdate || _updating) {
                        _update(TRUE);
                        if (_supportsState()) {
                            _h[_opts.history ? 'pushState' : 'replaceState']({}, '', 
                                    _opts.state.replace(/\/$/, '') + (_value === '' ? '/' : _value));
                        } else {
                            _silent = TRUE;
                            if (_webkit) {
                                if (_opts.history) {
                                    _l.hash = '#' + _crawl(_value, TRUE);
                                } else {
                                    _l.replace('#' + _crawl(_value, TRUE));
                                }
                            } else if (_value != _href()) {
                                if (_opts.history) {
                                    _l.hash = '#' + _crawl(_value, TRUE);
                                } else {
                                    _l.replace('#' + _crawl(_value, TRUE));
                                }
                            }
                            if ((_msie && !_hashchange) && _opts.history) {
                                _st(_html, 50);
                            }
                            if (_webkit) {
                                _st(function(){ _silent = FALSE; }, 1);
                            } else {
                                _silent = FALSE;
                            }
                        }
                    }
                    return this;
                }
                return _strict(_value);
            },
            path: function(value) {
                if (value !== UNDEFINED) {
                    var qs = this.queryString(),
                        hash = this.hash();
                    this.value(value + (qs ? '?' + qs : '') + (hash ? '#' + hash : ''));
                    return this;
                }
                return _strict(_value).split('#')[0].split('?')[0];
            },
            pathNames: function() {
                var path = this.path(),
                    names = path.replace(_re, '/').split('/');
                if (path.substr(0, 1) == '/' || path.length === 0) {
                    names.splice(0, 1);
                }
                if (path.substr(path.length - 1, 1) == '/') {
                    names.splice(names.length - 1, 1);
                }
                return names;
            },
            queryString: function(value) {
                if (value !== UNDEFINED) {
                    var hash = this.hash();
                    this.value(this.path() + (value ? '?' + value : '') + (hash ? '#' + hash : ''));
                    return this;
                }
                var arr = _value.split('?');
                return arr.slice(1, arr.length).join('?').split('#')[0];
            },
            parameter: function(name, value, append) {
                var i, params;
                if (value !== UNDEFINED) {
                    var names = this.parameterNames();
                    params = [];
                    value = value === UNDEFINED || value === NULL ? '' : value.toString();
                    for (i = 0; i < names.length; i++) {
                        var n = names[i],
                            v = this.parameter(n);
                        if (typeof v == STRING) {
                            v = [v];
                        }
                        if (n == name) {
                            v = (value === NULL || value === '') ? [] : 
                                (append ? v.concat([value]) : [value]);
                        }
                        for (var j = 0; j < v.length; j++) {
                            params.push(n + '=' + v[j]);
                        }
                    }
                    if ($.inArray(name, names) == -1 && value !== NULL && value !== '') {
                        params.push(name + '=' + value);
                    }
                    this.queryString(params.join('&'));
                    return this;
                }
                value = this.queryString();
                if (value) {
                    var r = [];
                    params = value.split('&');
                    for (i = 0; i < params.length; i++) {
                        var p = params[i].split('=');
                        if (p[0] == name) {
                            r.push(p.slice(1).join('='));
                        }
                    }
                    if (r.length !== 0) {
                        return r.length != 1 ? r : r[0];
                    }
                }
            },
            parameterNames: function() {
                var qs = this.queryString(),
                    names = [];
                if (qs && qs.indexOf('=') != -1) {
                    var params = qs.split('&');
                    for (var i = 0; i < params.length; i++) {
                        var name = params[i].split('=')[0];
                        if ($.inArray(name, names) == -1) {
                            names.push(name);
                        }
                    }
                }
                return names;
            },
            hash: function(value) {
                if (value !== UNDEFINED) {
                    this.value(_value.split('#')[0] + (value ? '#' + value : ''));
                    return this;
                }
                var arr = _value.split('#');
                return arr.slice(1, arr.length).join('#');                
            }
        };
    })();
    
    $.fn.address = function(fn) {
        var sel;
        if (typeof fn == 'string') {
            sel = fn;
            fn = undefined;
        }
        if (!$(this).attr('address')) {
            var f = function(e) {
                if (e.shiftKey || e.ctrlKey || e.metaKey || e.which == 2) {
                    return true;
                }
                if ($(this).is('a')) {
                    e.preventDefault();
                    var value = fn ? fn.call(this) : 
                        /address:/.test($(this).attr('rel')) ? $(this).attr('rel').split('address:')[1].split(' ')[0] : 
                        $.address.state() !== undefined && !/^\/?$/.test($.address.state()) ? 
                                $(this).attr('href').replace(new RegExp('^(.*' + $.address.state() + '|\\.)'), '') : 
                                $(this).attr('href').replace(/^(#\!?|\.)/, '');
                    $.address.value(value);
                }
            };
            $(sel ? sel : this).live('click', f).live('submit', function(e) {
                if ($(this).is('form')) {
                    e.preventDefault();
                    var action = $(this).attr('action'),
                        value = fn ? fn.call(this) : (action.indexOf('?') != -1 ? action.replace(/&$/, '') : action + '?') + 
                            $(this).serialize();
                    $.address.value(value);
                }
            }).attr('address', true);
        }
        return this;
    };
    
})(jQuery);


  /*
 *
 * NoClickDelay
 * http://cubiq.org/
 *
 */
function NoClickDelay(el) {
  this.element = el;
  if( window.Touch ) this.element.addEventListener('touchstart', this, false);
}

NoClickDelay.prototype = {
  handleEvent: function(e) {
    switch(e.type) {
      case 'touchstart':
        this.onTouchStart(e);
        break;
      case 'touchmove':
        this.onTouchMove(e);
        break;
      case 'touchend':
        this.onTouchEnd(e);
        break;
    }
  },

  onTouchStart: function(e) {
    //e.preventDefault();
    this.moved = false;

    this.element.addEventListener('touchmove', this, false);
    this.element.addEventListener('touchend', this, false);
  },

  onTouchMove: function(e) {
    this.moved = true;
  },

  onTouchEnd: function(e) {
    this.element.removeEventListener('touchmove', this, false);
    this.element.removeEventListener('touchend', this, false);

    if( !this.moved ) {
      e.preventDefault();
      var theTarget = document.elementFromPoint(e.changedTouches[0].clientX, e.changedTouches[0].clientY);
      if(theTarget.nodeType == 3) theTarget = theTarget.parentNode;

      var theEvent = document.createEvent('MouseEvents');
      theEvent.initEvent('click', true, true);
      theTarget.dispatchEvent(theEvent);
    }
  }
};

/*
   * Environment
   */
  var ENV = {};
  ENV.isTouchDevice = !!( 'ontouchstart' in window );
  ENV.touchClickEvent = ENV.isTouchDevice ? 'touchend' : 'click';
  ENV.isHandledDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( navigator.userAgent );

var commentEditor = 'ckeditor'; // 'ckeditor' || 'tinymce'

var configCKEditorSettings = {
  contentsCss : '/css/edit_settings.css',
  extraPlugins : 'basicstyles,link',
  toolbar: [
  {
    name: 'actions', 
    items : ['Undo','Redo','-','Bold','Italic','Underline','Strike','-','Link','Unlink','-','BulletedList','NumberedList']
    }
  ],
  toolbarCanCollapse : false,
  skin: 'v2_arch',
  language: 'ru'
};

var configCKEditorText = {
  contentsCss : '/css/edit_text.css',
  extraPlugins : 'basicstyles,link',
  toolbar: [
  {
    name: 'actions', 
    items : ['Undo','Redo','-','Bold','Italic','Underline','Strike','-','Link','Unlink','-','BulletedList','NumberedList']
    }
  ],
  toolbarCanCollapse : false,
  skin: 'v2_arch',
  language: 'ru'
};

var configCKEditorComment = {
  contentsCss : '/css/edit_comment.css',
  extraPlugins : 'blockquote,simpleimage,simpledialog,simplevideo,mmvirtualkeyboard',
  toolbar: [
  {
    name: 'actions', 
    items : []
    }
  ],
  toolbarCanCollapse : false,
  skin: 'arch',
  language: 'ru'
};

var i18n = {
  blog_activation: '��������� �����'
};

(function($){    

  updateImage = function(data) {
    var $widget = $(this);
    var $cont = $widget.parents('.registry_forms-oblique:first');
    var $img = $cont.children('.element_photo');
    if ( $img.length < 1 )
      $img = $('<div class="element_photo"><img/></div>').prependTo($cont);
    $img.find('img:first').attr('src', data.src);
    $widget.find('input.form-file-adv-source:first').val(data.source);
  };

  ajaxUpload = function(file, callback) {
    var $file = $(file);
    var $widget = $file.parents('.form-file-adv-container:first');
    var $loader = $('<div class="file-loader"></div>').hide().appendTo($widget);
    if ( $file.val() === '' ) {
      alert('�������� ���������� ��� ��������.');
      return false;
    }
    if ( ! /jpg$/i.test($file.val()) &&
      ! /jpeg$/i.test($file.val()) &&
      ! /gif$/i.test($file.val()) &&
      ! /png$/i.test($file.val()) ) {
      alert('��������� ����� ������ ����������� ������� jpg, gif ��� png.');
      return false;
    }
    $loader.height($widget.height()).show();
    $.ajaxFileUpload({
      url: $('#imageuploadurl').val(),
      secureuri: false,
      fileElementId: $file.attr('id'),
      dataType: 'json',
      success: function (data, status)
      {
        if ( typeof data.error != 'undefined' ) {
          alert(data.error);
        } else {
          callback.call($widget, data);
        }
        $loader.hide();
      },
      error: function (data, status, e)
      {
        //setTimeout(10000);
        alert(e);
        $loader.hide();
      }
    });
    return false;
  };    

  /**
   * Init and enable comment text field editor (TinyMCE or CKEditor)
   * 
   * @param {HTMLElement} [textarea] textarea object for editor
   */
  function initCommentEditor(textarea) {
    var block_id = 'tec' + (Math.random() + '').replace(/\./,'');
    $(textarea).attr('id',block_id);
    if ( commentEditor == 'tinymce' ) {
      return tinyMCE.init($.extend({
        mode : "exact",
        elements : block_id
      }, configTinyMCEComment || {} ));
    } else {
      if ( typeof $(textarea).data('editor-toolbar') === 'string' )
        configCKEditorComment.toolbar[0].items = $(textarea).data('editor-toolbar').split(',');
      return CKEDITOR.replace( block_id, configCKEditorComment || {} );
    }
  }
  
  /**
   * Disable and remove comment text field editor (TinyMCE or CKEditor)
   * 
   * @param {HTMLElement} [textarea] textarea object for editor
   */
  function removeEditor(textarea) {
    var block_id = $(textarea).attr('id');
    if ( !block_id ) return ;
    if ( commentEditor == 'tinymce' ) {
      tinyMCE.execCommand('mceRemoveControl', false, block_id);
    } else {
      if (CKEDITOR.instances[block_id]) {
        CKEDITOR.instances[block_id].destroy();
      }
    }
  }
  
  /**
   * Init text editor
   * 
   * @param {HTMLElement} [textarea] textarea object for editor
   */
  function initTextEditor(textarea) {
    var block_id = $(textarea).attr('id');
    if ( !block_id ) 
      return null;
    if ( commentEditor == 'tinymce' ) {
      return tinyMCE.init($.extend({
        mode : "exact",
        elements : block_id
      }, configTinyMCEText || {} ));
    } else {
      if ( typeof $(textarea).data('editor-toolbar') === 'string' )
        configCKEditorComment.toolbar[0].items = $(textarea).data('editor-toolbar').split(',');
      return CKEDITOR.replace( block_id, configCKEditorText || {} );
    }
  }
  
  /**
   * Init text editor for user settings
   * 
   * @param {HTMLElement} [textarea] textarea object for editor
   */
  function initSettingsEditor(textarea) {
    var block_id = $(textarea).attr('id');
    if ( !block_id ) 
      return null;
    if ( commentEditor == 'tinymce' ) {
      return tinyMCE.init($.extend({
        mode : "exact",
        elements : block_id
      }, configTinyMCESettings || {} ));
    } else {
      if ( typeof $(textarea).data('editor-toolbar') === 'string' )
        configCKEditorComment.toolbar[0].items = $(textarea).data('editor-toolbar').split(',');
      return  CKEDITOR.replace( block_id, configCKEditorSettings || {} );
    }
  }  
  
  /**
   * Get editor value
   */
  function getEditorValue(textarea) {
    var block_id = $(textarea).attr('id');
    if ( !block_id ) return textarea.value;
    if ( commentEditor == 'tinymce' ) {
      return tinyMCE.get(block_id).getContent();
    } else {
      return CKEDITOR.instances[block_id].getData();
    }
  }
  
  /*
   *   DOM Ready section
   */
  $(function(){
    
    new mmVirtualKeyboard();
    
    $('.write-textarea textarea').each(function(){     
      initCommentEditor(this);
    });
    
    if ( $('.write-textarea textarea[data-editor-toolbar*="Blockquote"]').length > 0 ) {
      (function(){
        var $menu,
        selectedText = '',
        selectedHtml = '',
        showDuration = 200,
        hideDuration = 100,
        selectionEndContainer = null;
            
        /**
         * Insert quote to active comment editor
         */
        function insertQuoteToComment(selectedText, selectedHtml, selectionEndContainer) {
          var $editor = $('.write-textarea textarea[data-editor-toolbar*="Blockquote"]:first'), //find first editor which should be actual
          editor = CKEDITOR.instances[$editor.attr('id')]; 
          //          console.log($editor, editor, selectedText, selectedHtml);
          
          var author = '', title = '', author_atr = '', title_atr = '';
          if ( typeof selectionEndContainer === 'object' ) {
            var $data_container = $(selectionEndContainer).parents('[data-author]:first');
//            console.log($(selectionEndContainer), $data_container, $data_container.data('author').length);
            if ( $data_container.length > 0 && $data_container.data('author') !== '*' )
              author = '<p class="comment-quote-name">' + $data_container.data('author') + '</p>';
              author_atr = ' data-author="' + $data_container.data('author') + '"';
              if ( $data_container.data('title') ) {
                title = '<p class="comment-quote-title">' + $data_container.data('title') + '</p>';
                title_atr = ' data-title="' + $data_container.data('title') + '"';
              }
          }
          editor.focus();
          
          var selection = editor.getSelection(),
            cursorNode = selection.getRanges()[0].startContainer,
            cursorParents = cursorNode.getParents(true),
            blockquote = CKEDITOR.dom.element.createFromHtml('<blockquote' + author_atr + title_atr + '>' + author + title + '<p>' + selectedText.replace(/\n/,'<br/>') + '</p></blockqoute>'),
            place = blockquote;
          //place = CKEDITOR.dom.element.createFromHtml('<p></p>');
          if ( cursorParents.length >= 3 ) {
            place.insertAfter(cursorParents[cursorParents.length - 3]);
          } else {
            var body = cursorParents[cursorParents.length - 2];
            //            console.log( body.getChildCount() === 1 && body.getChild(0).$.tagName.toUpperCase() === 'BR');
            if ( body.getChildCount() === 1 && body.getChild(0).$.tagName.toUpperCase() === 'BR' )
              place.replace(body.getChild(0));
            else
              place.appendTo(body);
          }
          if ( !place.getNext() || place.getNext().getName() !== 'p' )
            CKEDITOR.dom.element.createFromHtml('<p><br type="_moz"></p>').insertAfter(place);
          //console.log(cursorParents[cursorParents.length - 3]);
          selection.selectElement(place);
          var range = selection.getRanges()[0];
          range.collapse(false);
          selection.selectRanges([range]);
//          editor.execCommand('blockquote');
//          editor.insertHtml('<span style="font-weight:bold;display:block;">������ ����������</span>' + selectedText);
//          //editor.insertText();
//          place.remove();
        }
        
        function showQuoteMenu(evt) {
          if ( typeof $menu === 'undefined' ) {
            $menu = $('<ul class="quotemenu"><li>����������</li></ul>').appendTo(document.body);
            $menu.children('li').click(function(){
              insertQuoteToComment(selectedText, selectedHtml, selectionEndContainer);
            })
          }
          
          selectedText = evt.data.selectedText;
          selectedHtml = evt.data.selectedHtml;
          selectionEndContainer = evt.data.selectionEndContainer;
          
          var cursorXY = mouseXY(evt);
          
          $menu
          .css({
            left: cursorXY.x + 10,
            top: cursorXY.y - 45
          })
          .fadeIn(showDuration);
        }
        
        function hideQuoteMenu() {
          // to prevent hide menu before click on it
          setTimeout(function(){
            $menu.fadeOut(hideDuration);
          },0);            
        }
        
        $('.content, h1, #gallery_slider-subtitle').quotable(showQuoteMenu, hideQuoteMenu);
      })()      
    }
    
    /*
     *  UI Checkboxes
     */
    $('.f-checkbox').UICheckbox();
        
    
    /*
     * �������� ���������
     */
    $('.messgae_manage').each(function(){
      var $manager = $(this),
      $checkAll = $manager.find('input[type="checkbox"].check_all'),
      $fieldsChk = $manager.find('.message_list input[type="checkbox"]'), 
      $sortBy = $manager.find('.mm-sort select'),
      $actBlockUser = $manager.find('.msg-act-blockuser'),
      $actUnBlockUser = $manager.find('.msg-act-unblockuser'),
      $actDelRow = $manager.find('.msg-act-delrow'),
      $actMarkSpam = $manager.find('.msg-act-markspam'),
      $actMarkNotSpam = $manager.find('.msg-act-marknotspam'),
      $actMarkRead = $manager.find('.msg-act-read');
        
        
      // ������ ������� ������ ���� ��������� ������ ��� ������ �� ����� ������ ������ � ������
      var dependOnRowActions = [$actBlockUser, $actUnBlockUser, $actDelRow, $actMarkSpam, $actMarkNotSpam, $actMarkRead];
      
      // ������ ������� �� ������� �������� �������� ������� � �������� ������
      var deleteRowActions = [$actBlockUser, $actUnBlockUser, $actDelRow, $actMarkSpam, $actMarkNotSpam];
      
      /*
       * ������ ���������� �� ������� ������� �������� � �������� ������� � �������� ������
       */
      $.each(deleteRowActions, function(){
        $(this).click(function(){
          var $rows = $fieldsChk.filter(':checked').map(function(index, elm){
            return $(elm).parents('.ml-row:first').get(0);
          });
          $rows.append($('<div class="ml-row-overlay"></div>').css('opacity', 0.7));
          $.post(this.href, 
            $manager.serialize(),
            function(){
              $rows.fadeOut(500, function(){
                $(this).remove();
                // ��������� ���������
                $fieldsChk = $manager.find('.message_list input[type="checkbox"]');
                $manager.data('isAtLeastOneRowChecked', $fieldsChk.filter(':checked').length > 0); 
                $manager.trigger('atLeastOneRowChackedChange');
              });
            }
            );          
          return false;
        });
      });
      
      /*
       * �������� ��� �����������
       */
      $actMarkRead.click(function(){
        var $rows = $fieldsChk.filter(':checked').map(function(index, elm){
          return $(elm).parents('.ml-row:first').get(0);
        });
        $rows.append($('<div class="ml-row-overlay"></div>').css('opacity', 0.7));
        $.post(this.href, 
          $manager.serialize(),
          function(){
            $fieldsChk.filter(':checked').removeAttr('checked');
            $rows
            .removeClass('ml-unread')
            .children('.ml-row-overlay').fadeOut(500, function(){
              $(this).remove();
            });
          }
          );          
        return false;
      });
      
      /*
       * ��������/��������� �������, ������� ������� �� ������ ����-�� ����� ������
       */
      $manager.bind('atLeastOneRowChackedChange', function(){
        if ( $manager.data('isAtLeastOneRowChecked') ) {
          $.each(dependOnRowActions, function(){
            this.removeClass('msg-act-disabled')
          });          
        } else {
          $.each(dependOnRowActions, function(){
            this.addClass('msg-act-disabled')
          });
        }
      });
          
      // ������ �� ���� �� ���� ����� ����������
      $manager.data('isAtLeastOneRowChecked', $fieldsChk.filter(':checked').length > 0); 
      // ������� ������� ����� ��� ����������
      $manager.trigger('atLeastOneRowChackedChange');      
      
      /*
       * ������� ������/������ ���� �� ������ �������� � ������
       */      
      $fieldsChk.change(function(){        
        if ( $fieldsChk.filter(':checked').length > 0 && !$manager.data('isAtLeastOneRowChecked') ) {
          $manager.data('isAtLeastOneRowChecked', true);
          $manager.trigger('atLeastOneRowChackedChange');
        } else if ( $fieldsChk.filter(':checked').length < 1 && $manager.data('isAtLeastOneRowChecked') ) {
          $manager.data('isAtLeastOneRowChecked', false);
          $manager.trigger('atLeastOneRowChackedChange');
        }
      });
          
      /*
       * �����/������ ���� ��������� � ����� ������ � �����������
       */
      $checkAll.change(function(){
        if ( $(this).is(':checked') ) {
          $fieldsChk.each(function(){ // ������ ����� each ����� ��� �������� ����������� ��� ������� ��������, � �� ��� ���� �����
            $(this).attr('checked',true).trigger('change');
          })
        } else {
          $fieldsChk.each(function(){ // ������ ����� each ����� ��� �������� ����������� ��� ������� ��������, � �� ��� ���� �����
            $(this).attr('checked',false).trigger('change');
          })
        }
      });
      
      /*
       * �������� ����� ����� ��������� ���� ����������
       */
      $sortBy.change(function(){
        $manager.submit();
      });
    }); // �������� ���������
    
    $('form.real_register_form').append('<input type="hidden" name="nobot" value="1"/>');
    
    $('#popup-body .sec').each(function() {
      var $title = $('.sec-title span', this),
      $body  = $('.sec-body', this);
      var speed = 200;          
          
      $title.click(function() {
        if ( $body.is(':visible') )
          $body.slideUp(speed);
        else
          $body.slideDown(speed);
      })
    });
    
    $('#popup-head').each(function() {
      var $head = $(this),
      $body = $('#popup-body'),
      $form = $head.find('form:first'),
      $title = $head.find('.sec-title span'),
      $close = $head.find('button:first'),
      $submit= $head.find('button:last');
          
      var speed = 200;
          
      $title.add($close).click(function() {
        if ( $head.height() > 42 ) {
          $head.animate({
            height: 42
          },speed);
          $body.animate({
            top: 42
          },speed);
        } else {
          var size = $head.get(0).scrollHeight;
          $head.animate({
            height: size
          },speed);
          $body.animate({
            top: size
          },speed);
        }         
        return false;
      });
      
      $submit.submitable(function(XMLHttpRequest, textStatus) {
        if ( XMLHttpRequest.status == 200 )
          eval("var data = " + XMLHttpRequest.responseText);
        else
          var data = {
            text: '��������� �������������� ������ ��� ���������� �������, ���������� ��������� ��� ������.'
          };
        $('<table class="centered"><tr><td>' + data.text + '<br /><br /><a href="#">�������</a></td></tr></table>')     
        .find('a').click(function() {          
          $form.find('.write-overlay').remove();
          $form.find('textarea').val('');
          $close.trigger('click');
          return false;
        }).end()
        .appendTo($form.find('.write-overlay').removeClass('write-loader'));
      },function() {
        var $textarea = $form.find('textarea'),
              editor =  CKEDITOR.instances[$textarea.attr('id')];
        if ( editor ) {
          editor.updateElement();
        }              
        $('<div class="write-overlay write-loader"></div>').appendTo(this);
      })
    })
    
    $('.help').click(function() {
      var win = window.open(this.href, "", "dependent,resizable=no,scrollbars=no,menubar=no,status,width=775,height=670");
      win.focus();
      return false;
    })

    $('.illustration, .new_project-ill, .bdi-ill, .project_i-title').each(function() {
      var $ill = $(this);
      var $img = $ill.children('img');
      
      if ( $img.length < 1 ) return;
      
      var img = new Image();
      img.onload = function() {
        $img
        .css({
          background: 'url('+this.src+') no-repeat',
          height: this.height,
          width: this.width
        })
        .attr('src', '/img/s.gif');
        this.onload = null;
      }
      img.src = $img.attr('src');      
    });

    $('.func-bookmark').click(function() {
      if ( $(this).hasClass('func-bookmark-disabled') ) return false;
      $.get(this.href);
      $(this).addClass('func-bookmark-disabled');
      return false;
    })

    $('.func-comment-like:not(.direct_link)').each(function() {
      var $btn = $(this);
      var $comment = $btn.parents('.comment:first');
      var $like = $comment.find('.comment-like');
      $btn.click(function() {
        if ( $like.length < 1 )
          $like = $('<div class="comment-like"></div>').hide().appendTo($comment);
        $like.load($btn.attr('href'),function() {
          if ( $like.is(':hidden') )
            $like.slideDown();
        });
        return false;
      });
    });     

    $('.func-comment-reply:not(.direct_link)').each(function() {
      var $cform = $('.comment-write');
      var $btn = $(this);
      var $comment = $btn.parents('.comment-content:first');
      $btn.click(function() {
        if ( $comment.next('.comment-write').length > 0 ) return false;
        var $cf = $cform.clone(),
        $f = $cf.children('form')
        $cft = $f.find('textarea:first')
            
        // close other
        $('.comments .write-form-close').trigger('click');
        
        $cft.text('')
        .css({
          'visibility': 'visible',
          'display' : 'block'
        })          
        .parent().children(':not(textarea)').remove();                  
        
        $f.attr('action', this.href);
        $('<a href="#" class="write-form-close"></a>')
        .click(function() {
          removeEditor($cft.get(0));
          $cf.slideUp(function() {            
            $cf.remove();
          })
          return false;
        })
        .prependTo($f);                
        
        $cf.hide().insertAfter($comment).slideDown(function() {
          $f.find('input[type="text"]:first').focus();
        });
        
        initCommentEditor($cft.get(0));

        return false;
      })
      
    });

    $('.most').each(function() {
      var $most = $(this);
      var $form = $most.children('form:first');
      var $bm = $most.find('.most-bm a');
      var $body = $most.find('.most-body:first');
      // TODO save loaded data
      $bm.each(function() {
        var $li = $(this).parent();
        var $ul = $li.parent();
        var d = $(this).attr('href').match(/\#most\_([a-z]+)\=([a-z]+)/);
        var $value = $form.find('input[name="most_'+d[1]+'"]');
        $(this).click(function() {
          if ( !$li.hasClass('not-selected') ) return false;
          $ul.children().addClass('not-selected');
          $li.removeClass('not-selected');
          $value.val(d[2]);
          $.post($form.attr('action'), $form.serialize(), function(data) {
            $body.html(data);
          }, $form.attr('method'));
          return false;
        })
      })
    });
    
    // clear CKEDITORS on reset button press
    $('button[type="reset"]').each(function(){
      var $form = $(this).parents('form:first'),
          $textareas = $form.find('textarea');
      $(this).click(function(){
        $textareas.each(function(){
          if ( CKEDITOR && this.id && CKEDITOR.instances[this.id] )
            CKEDITOR.instances[this.id].setData('');
        });
      });
    });
    
    $('.write-list').each(function() {
      if ( $(this).parents('.write_reference:first').length > 0 ) return; // ����� ������ � ����� �������� �����������
      var $list = $(this);
      var $btn = $list.parent().find('.write-list-button:first');
      var $field = $list.parent().find('.write-list-input input');
      var $id = $list.parents('form').find('input[name="sm_to"]');
      var getList = function(str) {
        var data = typeof str != 'undefined' ? {
          str: str
        } : {};
        $.getJSON($btn.attr('href'), data, function(data) {
          $list.text('');
          if ( data.length > 0 )
            for ( var i = 0; i < data.length; i++ )
              $list.append('<a href="#"><b>' + data[i].id+ '</b><span>' + data[i].name + '</span><i>' + data[i].status + '</i></a>');
          else
            $list.html('<span class="write-list-empty">������������� �� �������</span>');
          $list.show();
        })
      };
      $list.delegate('a', 'click', function() {
        $field.val($(this).find('span:first').text());
        $id.val($(this).find('b:first').text())
        $list.hide();
        $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
        return false;
      });
      $list.delegate('a', 'mouseover', function(){
        $(this).addClass('hover');
      }).delegate('a', 'mouseout', function(){
        $(this).removeClass('hover');
      });
      $btn.click(function() {
        if ( $btn.hasClass('write-list-button-red') ) {
          $list.hide();
          $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
        } else {
          $btn.addClass('write-list-button-red').text('������� ������');
          getList();
        }
        return false;
      });
      $field.bind('keydown', function(evt) {
        var code = (evt.keyCode ? evt.keyCode : evt.which),
            $links = $list.find('a'),
            $current = $links.filter('.hover');
        switch ( code ) {
          // key down
          case 40:
            if ( $links.length > 0 ) {
              if ( $current.length > 0 ) {
                if ( $current.next('a').length > 0 ) {
                  $current.removeClass('hover');
                  $current.next('a').addClass('hover');
                }                                
              } else {
                $links.eq(0).addClass('hover');
              }
            }
            evt.preventDefault();
            break;
          // key up
          case 38: 
            if ( $links.length > 0 ) {
              if ( $current.length > 0 ) {
                if ( $current.prev('a').length > 0 ) {
                  $current.removeClass('hover');
                  $current.prev('a').addClass('hover');
                }                                
              }
            }
            evt.preventDefault();
            break;
          // enter
          case 13: 
            if ( $current.length > 0 ) {
              $current.trigger('click');
            }
            evt.preventDefault();  
            break;        
        }        
      });      
      $field.bind('keyup', function(evt){
        var code = (evt.keyCode ? evt.keyCode : evt.which);
        // do not request list but do not stop tab function
        if ( code === 9 || code === 40 || code === 38 || code === 13 )
          return;
        if ( $field.val().length > 2 )
          getList($field.val());
        else {
          $list.hide();
          $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
        }
      });
      // prevent form send on enter for Opera
      $field.bind('keypress', function(evt){
        var code = (evt.keyCode ? evt.keyCode : evt.which);
        if ( code === 13 )
          return false;
      });
    });

    /*
     * ���������� ������ ��� ��������� ������
     */
    $('.write_reference').each(function(){
      var $form = $(this);
      
      $form.find('.write-list-cont').each(function() {
        var $cont = $(this),
        $list = $cont.find('.write-list'),
        $btn = $cont.find('.write-list-button:first'),
        $field = $cont.find('.write-list-input input'),
        $value = $cont.find('input.write-list-value'),
        $params = $cont.find('input.write-list-param');
            
        var isSub = $cont.hasClass('.write-list-cont-sub'); // ��� ������ ��������?
            
        var getList = function(str) {
          var data = typeof str != 'undefined' ? {
            str: str
          } : {};
          $.getJSON($btn.attr('href') + ( $params.length > 0 ? ($btn.attr('href').indexOf('?') >= 0 ? '&' : '?') + $params.serialize() : '' ), data, function(data) {
            $list.text('');
            if ( data.length > 0 )
              for ( var i = 0; i < data.length; i++ )
                $list.append('<a href="#"><b>' + data[i].id+ '</b><span>' + data[i].name + '</span></a>');
            else
              $list.html('<span class="write-list-empty">������������� �� �������</span>');
            $list.show();
          })
        };
        
        $list.delegate('a', 'click', function() {
          $field.val($(this).find('span:first').text());
          $value.val($(this).find('b:first').text());
          if ( !isSub ) { // ���� ��� �� ������ ��������
            $form.find('.write-list-cont-sub').slideDown() // ���������� ������ ��������, ��� ������� �������� �� ������
            .find('input[name="object_company_id"]').val($(this).find('b:first').text()); // ��������� id ��������, ��� �������� ������ ��������
          }
          $list.hide();
          $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
          return false;
        });
        
        $btn.click(function() {
          if ( $btn.hasClass('write-list-button-red') ) {
            $list.hide();
            $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
          } else {
            $btn.addClass('write-list-button-red').text('������� ������');
            getList();
          }
          return false;
        });
        
        $field.keyup(function(evt) {
          if ( evt.keyCode == 40 ) {
            if ( $list.find('a').length > 0 ){
            //TODO
            }
          } else {
            if ( $field.val().length > 2 )
              getList($field.val());
            else {
              $list.hide();
              $btn.removeClass('write-list-button-red').html('������� �� ������&nbsp;<span>&rarr;</span>');
            }

          }
        });
      });          
    });

    $('.rate').each(function() {
      var $widget = $(this);
      var $scale = $widget.find('.rate-scale:first');
      var $set = $scale.find('a');
      var $value = $widget.find('.rate-value:first');
      var $yValue = $widget.find('.rate-your:first span');
      var $votes = $widget.find('.rate-votes:first span');
      var rate;
      var voted = $widget.hasClass('rate-voted');
      $scale.hover(function() {
        if ( !voted ) {
          rate = $value.text();
          $widget.addClass('rate-change');
        }
      }, function() {
        $widget.removeClass('rate-change');
        if ( !voted ) {
          $value.text(rate);
        }
      })
      $set.each(function(val){
        $(this).mouseover(function() {
          $value.text(val+1);
        })
        $(this).click(function() {
          voted = true;
          $.getJSON(this.href, function(data, textStatus, xhr) {
            $yValue.text(val+1);
            $votes.text(data.votes);
            $value.text(data.rate);
            $widget.removeClass('rate-change');
            $widget.addClass('rate-voted');
            $scale.get(0).className = $scale.get(0).className.replace(/rate\-scale\-\d/, 'rate-scale-' + parseInt(data.rate));
          })
          return false;
        })
      })
    })

    $('.func-like').each(function(){
      var title = document.title,
          url = window.location.href,
          addToFav;
      if (navigator.appName === "Microsoft Internet Explorer") {
        addToFav = function(){
          window.external.AddFavorite(url, title);
        }
      } else if(window.opera && window.print) { // Opera Browser
        addToFav = function(){
          var elem = document.createElement('a');
          elem.setAttribute('href',url);
          elem.setAttribute('title',title);
          elem.setAttribute('rel','sidebar');
          elem.click();
        }
      }
      else if (window.sidebar) { // Other Browsers wich support it
        addToFav = function(){
          window.sidebar.addPanel(title, url, "");
        }
      }
      if ( typeof addToFav === 'function' ) {
        $(this).click(function(){
          addToFav();
          return false;
        });
      } else {
        $(this).hide();
      }
    });

    $('.expand').each(function() {
      var $block = $(this).next('.expandable');
      if ( $block.length > 0 )
        $(this).click(function() {
          $block.slideToggle(200);
        })
    });

    $('.submitOnChange').each(function(){
      var $form = $(this).parents('form:first');
      $(this).change(function(){
        $form.submit();
      });
    });

    $('.register_form .submit').submitable();

    $('.bdi-ill').each(function(){
      var $image = $(this).find('.brand-logo img:first');
      var $form = $(this).find('form:first');
      var $file = $form.find('input[type="file"]:first');
      var $btn = $form.find('input[type="submit"]');
      var $widget_ajax = $('<div class="editable-widget-ajax"></div>').hide().appendTo($form);
      
      $btn.click(function() {
        if ( $file.val() === '' ) {
          alert('�������� ���������� ��� �������� � ���� ����.');
          return false;
        }
        if ( ! /jpg$/i.test($file.val()) &&
          ! /jpeg$/i.test($file.val()) &&
          ! /gif$/i.test($file.val()) &&
          ! /png$/i.test($file.val()) ) {
          alert('��������� ����� ������ ����������� ������� jpg, gif ��� png.');
          return false;
        }
        $widget_ajax.height($form.height()).show();
        $.ajaxFileUpload({
          url: $form.attr('action'),
          secureuri: false,
          fileElementId: $file.attr('id'),
          dataType: 'json',
          success: function (data, status)
          {            
            if ( typeof data.error != 'undefined' ) {
              alert(data.error);
            } else {
              $image.attr('src', data.src);
            }
            $widget_ajax.hide();
          },
          error: function (data, status, e)
          {
            alert(e);
            $widget_ajax.hide();
          }
        });
        return false;
      });
    });

    /*
     *  Editables
     */
    var $activeEditableCloseBtn = null;
    $('.editable').each(function(index){
      var $editable = $(this);
      var $editable_tbody = $editable.parents('tbody:first');

      var $widget_tbody = $('<tbody class="editable-tbody"><tr><td colspan="2"></td></tr></tbody>').hide().insertAfter($editable_tbody);
      var $widget_td = $widget_tbody.find('>tr>td:first');
      var $widget = $('<div class="editable-widget"></div>').appendTo($widget_td);
      var $widget_ajax = $('<div class="editable-widget-ajax"></div>').hide().appendTo($widget);

      var $btn_save = $('<button class="button-min">���������</button>').appendTo($widget);

      var $btn_edit = $('<a href="#" class="editable_btn">�������������</a>').insertAfter($editable);
      var $btn_close = $('<a href="#" class="editable_close">��������</a>')
      .css('display', 'inline') // to prevent become a block on show()
      .hide()
      .insertAfter($editable);

      var $widget_src, $widget_val, 
      loadWidget = function() {},
      unloadWidget = function() {},
      getWidgetVal = function() {
        return $widget_val.val()
      },
      setWidgetVal = function(val) {
        $widget_val.val(val)
      };

      var updateContent = function(content) {
        $widget_ajax.height($widget.height()).show();        
        $.post($editable.attr('updateURL'), {
          content: content
        }, function(data, textStatus, XMLHttpRequest) {
          $editable.html(data.text);
          $btn_close.trigger('click');
          $widget_ajax.hide();
        }, 'json'
        )
      }

      $btn_edit.click(function() {
        if ( $activeEditableCloseBtn != null )
          $activeEditableCloseBtn.trigger('click');
        setWidgetVal($editable.html());
        $btn_edit.hide();
        $btn_close.show();
        $editable.hide();
        $widget_tbody.css('display',''); // to prevent become a block in FF3
        loadWidget();
        $activeEditableCloseBtn = $btn_close;
        return false;
      });

      $btn_close.click(function(){
        $activeEditableCloseBtn = null;
        $btn_edit.show();
        $btn_close.hide();
        $editable.show();
        $widget_tbody.hide();
        unloadWidget();
        return false;
      });

      $btn_save.click(function() {
        updateContent(getWidgetVal());
      });

      if ( $editable.hasClass('editable-text')) {
        $widget_src = $('<div class="registry_forms-min"><div class="form_element_fix"><textarea class="form-text"></textarea></div></div>')
        $widget_val = $widget_src.find('textarea:first');
      }
      if ( $editable.hasClass('editable-textline')) {
        $widget_src = $('<div class="registry_forms-min"><div class="form_element_fix"><input type="text" class="form-text"/></div></div>')
        $widget_val = $widget_src.find('input:first');
      }
      if ( $editable.hasClass('editable-richtext')) {
        var richeditor_id = 'tinymce' + index;
        $widget_src = $('<div style="width: 100%; overflow: hidden;"><textarea cols="50" rows="10" name="'+richeditor_id+'" id="'+richeditor_id+'"></textarea></div>');
        $widget_val = $widget_src.find('textarea:first');
        loadWidget = function() {
          initSettingsEditor($widget_val.get(0));
        }
        unloadWidget = function() {
          removeEditor($widget_val.get(0));
        }
        getWidgetVal = function() {
          return getEditorValue($widget_val.get(0));
        }
        setWidgetVal = function(val) {
          $widget_val.val(val);
        }
      }
      $widget_src.prependTo($widget);      
    });

    /*
     *   Expand text
     */
    $('.project-info .textual, .project_i-concept .textual').each(function(){
      var $p = $(this).children();
      if ( $p.filter('p').length < 2 )
        return;
      var $btn_show = $('<a href="#" class="more-arr"> &rarr;</a>')
      var $show = $('<span>&hellip; </span>').append($btn_show);
      var $btn_hide = $('<a href="#" class="more-arr"> &larr;</a>').hide();
      
      var isFirst = true;
      var $first = $('nothing'), $notFirst = $('nothing');
      $p.each(function() {        
        if ( isFirst ) {
          $first = $first.add(this);
        } else {
          $notFirst = $notFirst.add(this);
        }
        isFirst = isFirst && this.tagName.toUpperCase() != 'P';        
      });
      $notFirst.addClass('more-hidden');
      var $hidden = $p.filter('.more-hidden').hide();

      $first.last().append($show);
      $notFirst.last().append($btn_hide);

      $btn_show.click(function(){
        $show.hide();
        $hidden.show();
        $btn_hide.css('display', 'inline');
        return false;
      });
      $btn_hide.click(function(){
        $show.show();
        $hidden.hide();
        $btn_hide.hide();
        return false;
      })
    });    

    /*
     *   Init Tabs
     */
    $.fn.initTabs();


    /*
     *   Project SLIDE
     */
    $('ul.project-list').each(function(){
      var $links = $(this).find('li');
      var $contents = $(this).parents('.new_projects:first').find('.new_project-container .new_project');
      var interval = null;
      var goInterval = 5000;
      if ( $links.length != $contents.length )
        return ;

      var goToNext = function() {
        var $next = $links.filter('.selected').next('li');
        if ( $next.length == 0 )
          $next = $links.eq(0);
        selectItem.call($next.get(0));
      }

      var selectItem = function(){
        if ( !$(this).hasClass('selected') ) {
          $links.filter('.selected').removeClass('selected');
          $(this).addClass('selected');
          $contents.filter('.selected').removeClass('selected');
          $contents.eq($(this).attr('_num')).addClass('selected');
        }
      }

      var run = function() {
        interval = setInterval(goToNext, goInterval);
      }

      var stop = function() {
        clearInterval(interval);
      }

      $('.new_project-container').add(this).hover(function(){
        stop();
      }, function(){
        run();
      });

      $links.each(function(num){
        $(this).attr('_num', num);
      });

      $links.mouseover(selectItem);

      run();
    });

    /*
	 * --------------  init scroll gallery  --------------
	 */
    $('.scrollPanel').jScrollGallery();

    /*
     * --------------  Content scroll to top button  --------------
     */

    $(document).mousewheel(function() {
      $('html, body').stop();
    });

    function filter(link) {
      return link.href && link.hash && (/.*#.+/.test(link.href));
    };

    $('a[href*=#]').each(function() {
      if (filter(this)) {      
        var $scrollable = $.browser.webkit ? $('body') : $('html');
        var $target = $('a[name="' + this.hash.slice(1) + '"]');        
        if ($target.length > 0) {
          $(this).click(function() {
            var targetOffset = 0;//because of Opera scroll to the beginning only $target.offset().top;
            var linkOffset = $(this).offset().top;
            var duration = Math.abs(targetOffset - linkOffset);
            $scrollable.animate({
              scrollTop : targetOffset
            }, duration * 0.7, 'easeOutQuint');
            return false;
          });
        }
      }
    });

    /*
    *  ie6 fix for :hover
    */
    $.ie6CSSHover();

    /*
    *  Labels
    */
    $('label>input').each(function(){
      var $input = $(this),
      $label = $input.parent();

      if ( $.trim($input.val()) != '' )
        $label.addClass('label-enter');

      $input.focus(function(){
        if ( $.trim($(this).val()) == '' )
          $label.addClass('label-focus')
      });

      $input.blur(function(){
        if ( $.trim($(this).val()) == '' )
          $label.removeClass('label-focus').removeClass('label-enter');
      });

      $input.keyup(function(){
        if ( $.trim($(this).val()) == '' )
          $label.removeClass('label-enter');
        else
          $label.addClass('label-enter');
      });
    })

    /*
    *  Menu main dropdown forms
    */
    $('.dropdown-title').each(function(){
      var $title = $(this),
      $cont  = $title.parent(),
      $drop  = $cont.find('.dropdown');

      var menuWidth = 956;

      var isFull = $drop.hasClass('dropdown-full');

      var $popup = $([
        '<div class="popup popup-simple">',
        '<div class="popup_inner">',
        '<table class="popup_shadow">',
        '<tr><td colspan="2" class="pus-ltb"><img src="/img/s.gif"/></td><td rowspan="2" class="pus-bclt"><img src="/img/s.gif"/></td><td class="pus-te" rowspan="2"><img src="/img/s.gif" height="30" /></td><td rowspan="2" class="pus-bcrt"><img src="/img/s.gif"/></td><td colspan="2" class="pus-rtb"><img src="/img/s.gif"/></td></tr>',
        '<tr><td class="pus-lt"><img src="/img/s.gif"/></td><td class="pus-t"><img src="/img/s.gif" width="0" height="1"/></td><td class="pus-t" width="100%"></td><td class="pus-rt"><img src="/img/s.gif"/></td></tr>',
        '<tr><td class="pus-l pus-h"><img src="/img/s.gif"/></td><td class="pus-bg" colspan="5"></td><td class="pus-r pus-h"></td></tr>',
        '<tr><td class="pus-lb"><img src="/img/s.gif"/></td><td class="pus-b" colspan="5"></td><td class="pus-rb"><img src="/img/s.gif"/></td></tr>',
        '</table>',
        '<div class="popup_content"><div class="popup_te"></div><div class="popup_c"></div></div>',
        '</div>',
        '</div>'
        ].join(''));

      var $popup_content = $popup.find('.popup_content:first'),
      $popup_content_c = $popup_content.find('.popup_c'),
      $popup_content_t = $popup_content.find('.popup_te'),
      $pus_h = $popup.find('.pus-h'),
      $pus_te = $popup.find('.pus-te:first img'),
      $pus_t1 = $popup.find('.pus-t:first img'),
      $pus_bcrt = $popup.find('.pus-bcrt:first'),
      $pus_rtb = $popup.find('.pus-rtb:first'),
      $pus_rt = $popup.find('.pus-rt:first'),
      $popup_content_li = $cont.find('li');

      var setPopupPos = function(offset, contentSize) {
        var overRight = menuWidth - ( $cont.position().left + $cont.width() + 39  );
        overRight = overRight < 0 ? overRight : 0;
        if ( overRight < 0 ) {
          $pus_bcrt.removeClass('pus-bcrt').addClass('pus-te');
          $pus_rtb.removeClass('pus-rtb').addClass('pus-rte');
          $pus_rt.removeClass('pus-rt').addClass('pus-r');
        }

        if ( isFull ) {
          $popup.css({
            left: - $cont.position().left, // -13
            top: offset.top - 13 // -13
          })
        } else {
          $popup.css({
            left: offset.left - 13 + overRight, // -13
            top: offset.top - 13 // -13
          })
          $pus_t1.css({
            width: - overRight // - overRight
          })
          $popup_content_t.css({
            marginLeft: - overRight + 26 // - overRight + 26
          })
        }
      }

      var setTitlePos = function() {
        if ( isFull ) {
          $pus_t1.css({
            width: $cont.position().left - 39 + 1
          })
          $popup_content_t.css({
            marginLeft: $cont.position().left - 12
          })
        }else {
          
        }
      }
      
      var setTitleSize = function(contentSize, itemWidth) {
        $pus_te.css({
          width: itemWidth - 22 // -11 -11
        })
        $popup_content_t.css({
          width: itemWidth
        })
        
      }

      var setPopupSize = function(size, itemWidth) { // .popup_content size
        size.width = Math.max(size.width, itemWidth + 52);
        $popup_content.css(size);
        $popup.css({
          width: size.width + 26 // +13 +13
        });
        $pus_h.css({
          height: size.height - 20 // -20
        });
        if ( $.browser.msie && $.browser.version < 7 ) {
          $popup_content_li.css({
            width: isFull ? '100%' : size.width - 18
          });          
        }
      }

      var getContentSize = function() {
        // make it invisible and append to body to mesure it
        var $mesurer = $('<div></div>')
        .css({
          left: 0,
          top: 0,
          position: 'absolute',
          visibility: 'hidden',
          zoom: 1
        })
        .wrapInner($drop.show())
        .appendTo(document.body);
        var width = $mesurer.outerWidth(),
        height = $mesurer.outerHeight();
        width = Math.max($title.outerWidth(true) - 1, width);
        // return style to default and append to the destination place in content block
        $drop
        .unwrap()
        .appendTo($popup_content_c);
        return {
          width: width,
          height: height
        };
      }

      var showPopup = function(){
        var cont_offset = $cont.offset();
        var contentSize = getContentSize();
        var itemWidth = $cont.width();
        cont_offset.top = -1;
        cont_offset.left = -25;
        setPopupPos(cont_offset, contentSize);
        setTitlePos();
        setTitleSize(contentSize, itemWidth);
        setPopupSize(contentSize, itemWidth);
        $title.addClass('dropdown-title-active');
        if ( !$.browser.msie ) {
          $popup.fadeIn(100);
        } else {
          $popup.show();
        }
      }

      var hidePopup = function(){
        if ( !$.browser.msie ) {
          $popup.fadeOut(100);
        } else {
          $popup.hide();
        }
        $title.removeClass('dropdown-title-active');        
      }

      $title.mouseover(function(){
        if ( !$popup.is(':visible'))
          showPopup();
      });

      $cont.mouseleave(function(){
        hidePopup();
      })

      $popup.prependTo($cont);
      
    })

    
    /*
    *  Menu account dropdown forms
    */
    var $menu_account_items = $('#menu_account li').each(function(){
      var $li = $(this),
      $span = $li.find('>span'),
      $form = $li.find('>form'),
      $first_input = $form.find('input:first');

      if ( $form.length < 1 ) return ;

      var form_fix = $form.is('#f_form_authorize') ? 1 : 0;

      var $popup = $([
        '<div class="popup">',
        '<div class="popup_inner">',
        '<table class="popup_shadow">',
        '<tr><td rowspan="2" class="pus-lt"><img src="/img/s.gif"/></td><td rowspan="2" class="pus-t pus-title"><img src="/img/s.gif"/></td><td rowspan="3" class="pus-crt"><img src="/img/s.gif"/></td><td width="100%" height="20px" colspan="2"><img src="/img/s.gif" width="100%" height="20px"/></td><td></td></tr>',
        '<tr><td rowspan="2" class="pus-t pus-100"></td><td rowspan="2" class="pus-rt"><img src="/img/s.gif"/></td><td class="pus-ie67fix"><img src="/img/s.gif"/></td></tr>',
        '<tr><td class="pus-l pus-f"><img src="/img/s.gif"></td><td class="pus-bg"></td><td></td></tr>',
        '<tr><td class="pus-l pus-h"><img src="/img/s.gif"/></td><td colspan="3" class="pus-bg"></td><td class="pus-r pus-h"></td><td></td></tr>',
        '<tr><td class="pus-lb"><img src="/img/s.gif"/></td><td colspan="3" class="pus-b"></td><td class="pus-rb"><img src="/img/s.gif"/></td><td></td></tr>',
        '</table>',
        '<div class="popup_title"></div>',
        '<div class="popup_content"></div>',
        '</div>',
        '</div>'
        ].join(''));

      var $popup_title = $popup.find('.popup_title:first'),
      $popup_content = $popup.find('.popup_content:first'),
      $pus_title = $popup.find('.pus-title:first img'),
      $popup_ct = $popup_title.add($popup_content),
      $pus_h = $popup.find('.pus-h'),
      $pus = $popup.find('.popup_shadow');

      var setTitleWidth = function(width) { // .popup_title size
        $popup_title.width(width);
        $pus_title.css({
          width: width - 22
        } // -11 -11
        ); 
      }

      var setPopupPos = function(offset) {
        $popup.css({
          left: offset.left - 13, // -13
          top: offset.top - 13 // -13
        })
      }

      var setPopupSize = function(size) { // .popup_content size
        $popup_content.css(size)
        $popup.css({
          width: size.width + 30 // +13 +13 +4
        });
        $pus_h.css({
          height: size.height - 20 // -20
        });
      }

      var getContentSize = function() {
        // make it invisible and append to body to mesure it
        $form
        .css({
          left: 0,
          top: 0,
          position: 'absolute',
          visibility: 'hidden'
        })
        .appendTo(document.body);
        var width = $form.outerWidth(),
        height = $form.outerHeight();
        // return style to default and append to the destination place in content block
        $form
        .appendTo($popup_content)
        .css({
          left: 'auto',
          top: 'auto',
          position: 'static',
          visibility: 'visible'
        });
        return {
          width: width,
          height: height
        };
      }

      var showPopup = function(){
        $menu_account_items.trigger('openAnother');
        var span_offset = {};
        span_offset.top = -1;
        span_offset.left = 1 - form_fix;
        setTitleWidth($span.width() + 20 + form_fix ); // + li border size if first form + 2 * il padding size
        setPopupPos(span_offset);
        setPopupSize(getContentSize());
        $span
        .addClass('active');
        if ( !$.browser.msie ) {
          $popup.fadeIn(100);
        } else { // separately animate not transparent layers and show shadow
          $popup_ct.hide();
          $pus.hide();
          $popup.show();
          setTimeout(function(){
            $pus.show();
          }, 50);
          $popup_ct.fadeIn(100);
        }
        $first_input.focus();
      }

      var hidePopup = function(){
        if ( !$.browser.msie ) {
          $popup.fadeOut(100, function(){
            $span
            .removeClass('active');
          });
        } else { // separately animate not transparent layers and hide shadow
          $popup_ct.fadeOut(100, function(){
            $popup.hide();
          });
          setTimeout(function(){
            $pus.hide();
          }, 50);
          $span
          .removeClass('active');
        }
      }

      $span.click(function(){
        if ( $form.is(':visible') ) {
          hidePopup();
        } else {
          showPopup();
        }
      });

      $li.bind('openAnother', function(){
        if ( $form.is(':visible') ) {
          hidePopup();
        }
      })

      $popup.hide().appendTo($li);
      $form
      .appendTo($popup_content)
      .show();
    });

    $('#mmWin-ActivateBlog').data('mmWin-ajax-submit-collback',function(XMLHttpRequest, textStatus){
      if ( XMLHttpRequest.status == 200 )
        eval("var data = " + XMLHttpRequest.responseText);
      else
        var data = {
          text: '��������� �������������� ������ ��� ���������� �������, ���������� ��������� ��� ������.'
        };
      var $form = $.fn.mmWin().find('form:first');
      $form.find('#mmWinFormOverlay').hide();
      $('<div id="mmWinFormText"><div class="mmWin-content">' + data.text + '<div></div>')
      .height($form.height())
      .appendTo($form);
      $form.find('input[type=text], textarea').val('');
      $('#mmWin-ActivateBlog').data('$mmWin-active-btn')
      .text(i18n.blog_activation)
      .addClass('red-disabled');
      $(document.body).one('click', function(){
        window.mmWinHide();
        return false;
      });
      window.mmWinHide(5000);
    });
    
    $('a.func-message').click(function(){
      $('#mmWin-SendLink').mmWinShow();
      return false;
    });
    
    $('a.callback').click(function(){
      $('#mmWin-Callback').mmWinShow();
      return false;
    });
    
    $('a.func-activate_blog').click(function(){
      if ( $(this).hasClass('red-disabled') )
        return false;
      $('#mmWin-ActivateBlog')
      .data('$mmWin-active-btn', $(this))
      .mmWinShow();
      return false;
    });

    $('a.func-ask-q').click(function(){
      var $form;

      //$('#mmWin-SendMessage .mmWin-header-text:first').text('��������� ���������:');
      //$('#mmWin-SendMessage form').attr('action', this.href);
      if ( $( this ).attr( 'data-spec' ) && $( this ).attr( 'data-spec' ) !== '' ) {
        $form = $('#mmWin-SendMessage-' + $( this ).attr( 'data-spec' ) );
      } else {
        $form = $('#mmWin-SendMessage');
      }
      $form.mmWinShow();
      var data = this.hash.match(/\#user_id=(\d+)\&user_name=([^\&]+)/);
      $form.find('#sm_to').val(data[1]);
      $form.find('.mmWin-data-to').text(decodeURIComponent(data[2].replace(/\+/g,  " ")));
      return false;
    });

    $('.func-add_to_friends').click(function() {
      if ( $(this).hasClass('func-add_to_friends-disabled') ) return false;
      //$('#mmWin-Friendship .mmWin-header-text:first').text('����������� ������:');
      //$('#mmWin-Friendship form').attr('action', this.href);
      $('#mmWin-Friendship').mmWinShow();
      var data = this.hash.match(/\#user_id=(\d+)\&user_name=([^\&]+)/);
      $('#mmWin-Friendship').find('#sm_to').val(data[1]);
      $('#mmWin-Friendship').find('.mmWin-data-to').text(decodeURIComponent(data[2].replace(/\+/g,  " ")));
      return false;
    });

    $('.mmWin').each(function(){
      var $win = $(this);
      $win.find('.mmWinHide').click(function(){
        $win.mmWinHide();
      });
      $win.bind('show.mmWin', function(){
        $(this).find('#mmWinFormOverlay, #mmWinFormText').remove();
      });
    });

    

  });
  // End of DOM ready section

  /**
   *  Show/Hide window
   *  
   *  @param [timeout] {Number} timeout in milisecond before window hide
   */
  window.mmWinHide = function(timeout){
    if ( typeof timeout != 'undefined' && typeof parseInt(timeout) == 'number' )
      window.mmWinHideTimer = setTimeout(function(){
        jQuery.fn.mmWin().mmWinHide();
      }, parseInt(timeout));
    else
      jQuery.fn.mmWin().mmWinHide();
  }
  
  window.mmWinHideTimer = null;

  $.fn.mmWin = function() {
    return $('#mmWin').children();
  }

  $.fn.mmWinShow = function(){
    return this.each(function(){
      var $win = $(this),
      $wrap = $win.wrap('<div id="mmWin" style="display: none;"></div>').parent();
      var mmWin = {};
      var ps = getPageSize(), psc = getPageScroll();
      mmWin.$overlay = $('<div></div>').css({
        background: '#000',
        height: ps[1],
        left: 0,
        opacity: 0,
        position: 'absolute',
        top: 0,
        width: ps[0],
        zIndex: 20
      });
      mmWin.$overlay
      .appendTo(document.body)
      .show()
      .animate({
        opacity: 0.9
      },200, function(){
        $win.trigger('show.mmWin');
        $wrap.css('visibility', 'visible');
      });
      $wrap.css({
        left: 0,
        visibility: 'hidden',
        position: 'absolute',
        top: 0,
        zIndex: 21
      }).show();
      $win.show();
      $wrap.css({
        top: psc[1] + Math.max(ps[3] - $wrap.height(), 0) / 2,
        left: (ps[0] - $wrap.width()) / 2
      });
      $win.data('mmWin', mmWin);
    });
  };

  $.fn.mmWinHide = function(){
    return this.each(function(){
      var $win = $(this);
      var mmWin = $win.data('mmWin');
      $win.hide();
      $win.unwrap();
      mmWin.$overlay.animate({
        opacity: 0
      }, 200, function(){
        $(this).remove();
      });
      if ( window.mmWinHideTimer != null )
        clearTimeout(window.mmWinHideTimer);
    });
  };

  /*
 *  Form submitting
 */
  $.fn.submitable = function(callback, callforward) {
    return this.each(function(){
      var $submit = $(this);
      var $form = $submit.parents('form:first');
      if ( $form.length < 1 )
        $form = $submit.prev('form');
      var isActive = false;
      var submitClass = $submit.hasClass('submit') ? 'submit' : 'submit_ajax';

      $submit.click(function(){
        try {
          if ( isActive ) {
            if ( typeof callback != 'function' )
              $form.submit();
            else {
              if ( typeof callforward == 'function' ) {
                if ( callforward.call($form) === false )
                  return false;
              }
              $.ajax({
                data: $form.serializeArray(),
                type: $form.attr('method'),
                url: $form.attr('action') + ( $form.attr('action').indexOf('?') >= 0 ? '&' : '?') + 'nobot=1',
                complete: callback
              })
            }
          }
        } catch (e) {
          alert(e);
        }
        return false;
      });

      $form.validate(function(){
        $submit.removeClass(submitClass);
        isActive = true;
      },
      function(){
        $submit.addClass(submitClass);
        isActive = false;
      }
      );
    });
  }

  /*
 *  Form validation
 */
 

  /**
   *  Tabs
   */
  $.fn.initTabs = function(settings)
  {
    settings = $.extend({
      titles: '.tab-titles',
      title: '.tab-title',
      content: '.tab-content',
      titleSelectedClass: 'tab-selected',
      contentVisibleClass: 'tab-visible',
      titleIdPrefix: 'tab-titles-',
      contentIdPrefix: 'tab-contents-'
    }, settings);

    $(settings.titles).each(function(){
      // get tabId
      var tabId = $(this).attr('id').match(new RegExp("^" + settings.titleIdPrefix + "(.*)$"));
      if ( tabId != null )
        tabId = tabId[1]
      else
        return ;

      // on tab titles click action
      var $title = $(this).find(settings.title);
      var $content = $('#' + settings.contentIdPrefix + tabId + ' ' + settings.content);
      $title.each(function(num){
        $(this).click(function(){
          if ( $(this).hasClass(settings.titleSelectedClass) ) return false;
          $title.removeClass(settings.titleSelectedClass);
          $(this).addClass(settings.titleSelectedClass);
          $content.removeClass(settings.contentVisibleClass);
          $content.eq(num).addClass(settings.contentVisibleClass);
          return false;
        });
      });

    });
  }

  /**
 *  contentScroller
 */


  /*
 *  ie6 fix for :hover :after :before :focus
 */
  $.ie6CSSHover = function(){
    return;
    if($.browser.msie && /6.0/.test(navigator.userAgent)){
      var len=document.styleSheets.length;
      for(z=0;z<len;z++){
        var sheet=document.styleSheets[z];
        var css =sheet.cssText;
        var r=new RegExp(/[a-zA-Z0-9\.-_].*:hover\s?\{.[^\}]*\}/gi);
        var m=css.match(r);
        if(m!=null && m.length>0){
          for(i=0;i<m.length;i++){
            var c=m[i].match(/\{(.[^\}]*)}/);
            if(c[1]){
              var seljq=m[i].replace(':hover','').replace(c[0],'');
              var selcss=m[i].replace(':hover','.hover').replace(c[0],'');
              var rule=c[1].replace(/^\s|\t|\s$|\r|\n/g,'');
              document.styleSheets[z].addRule(selcss,rule);
              var grp=$(seljq);
              $(seljq).hover(function(){
                $(this).addClass('hover')
              },function(){
                $(this).removeClass('hover')
              });
            }
          }
        }
      }
    }
  };

  window.sendMessageTo = function(email, send_to) {
    $('#mmWin-SendMessage .mmWin-header-text:first').text('��������� ���������:');
    $('#mmWin-SendMessage form').attr('action', this.href);
    $('#mmWin-SendMessage').mmWinShow();
    $('#mmWin-SendMessage #sm_to').val(email);
    $('#mmWin-SendMessage').find('.mmWin-data-to').text(send_to);
  /*var $form = $('#mmWin-SendMessage').find('form');
    for ( var param in data )
      $form.append('<input type="hidden" name="'+param+'" value="'+data[param]+'"/>')*/
  }

  window.mmConfirm = function(text, link) {
    var $win = $('<div id="mmConfirm" class="mmWin">\n\
                   <p>' + text + '</p>\n\
                   <button type="button" class="mmWinHide">���</button>\n\
                   <button type="button" class="mmWinSubmit">��</button>\n\
                  </div>').hide().appendTo('body');
    $win.find('button.mmWinHide').click(function() {
      $win.mmWinHide();
      $win.remove();
    })

    $win.find('button.mmWinSubmit').click(function() {
      location.href = link.href;
      $win.find('button.mmWinHide').trigger('click');
    })

    $win.mmWinShow();   
  }
  
  


})(jQuery);


(function($){
  
  /**
   * @return {jQuery}
   */
  $.fn.quotable = function(settings){
    
    settings = typeof settings === 'function' ? {
      onSelect: settings
    } : settings;
    if ( typeof arguments[1] === 'function' )
      settings.onDeselect = arguments[1];
    
    settings = $.extend({
      type: 'html', // 'text' | 'html'
      onSelect: null, // function(evt){ /* this points to the quotable element, evt.data.selectedText contains selected text or html */ }
      onDeselect: null // function(evt){ /* this points to the quotable element */ }
    }, settings );
    
    /**
     * Get selected text
     * 
     * @return {String} selected text
     */
    function getSelectedText() {
      var selectedText = '';
      if (window.getSelection) {
        selectedText = window.getSelection();
      } else if (document.getSelection) {
        selectedText = document.getSelection();
      } else if (document.selection) {
        selectedText = document.selection.createRange().text;
      }
      return $.trim(selectedText);
    }
    
    /**
     * Get selected html
     * 
     * @return {String} selected html
     */
    function getSelectedHtml() {
      var userSelection, 
      selectedHtml = '';
      if ( window.getSelection ) {
        // W3C Ranges
        userSelection = window.getSelection();
        // check that selection isn't emptys
        if ( userSelection.rangeCount < 1 ) return '';
        // Get the range:
        if ( userSelection.getRangeAt )
          var range = userSelection.getRangeAt(0);
        else {
          var range = document.createRange();
          range.setStart(userSelection.anchorNode, userSelection.anchorOffset);
          range.setEnd(userSelection.focusNode, userSelection.focusOffset);
        }
        // And the HTML:
        var clonedSelection = range.cloneContents();
        var div = document.createElement('div');
        div.appendChild(clonedSelection);
        selectedHtml = div.innerHTML;
      } else if ( document.selection ) {
        // Explorer selection, return the HTML
        userSelection = document.selection.createRange();
        selectedHtml = userSelection.htmlText;
      }
      return selectedHtml;
    }
    
    /**
     * Get selection start/end element
     * 
     * @return {DOMElement} selection start/end element
     */
    function getSelectionBoundaryElement(isStart) {
      if ( typeof isStart === 'undefined' )
        isStart = true;
      var range, sel, container;
      if (document.selection) {
        range = document.selection.createRange();
        range.collapse(isStart);
        return range.parentElement();
      } else {
        sel = window.getSelection();
        if (sel.getRangeAt) {
          if (sel.rangeCount > 0) {
            range = sel.getRangeAt(0);
          }
        } else {
          // Old WebKit
          range = document.createRange();
          range.setStart(sel.anchorNode, sel.anchorOffset);
          range.setEnd(sel.focusNode, sel.focusOffset);

          // Handle the case when the selection was selected backwards (from the end to the start in the document)
          if (range.collapsed !== sel.isCollapsed) {
            range.setStart(sel.focusNode, sel.focusOffset);
            range.setEnd(sel.anchorNode, sel.anchorOffset);
          }
        }

        if (range) {
          container = range[isStart ? "startContainer" : "endContainer"];
          // Check if the container is a text node and return its parent if so
          return container.nodeType === 3 ? container.parentNode : container;
        } else {
          return null;
        }
      }
    }
    
    return this.each(function(){
      var $element = $(this);
      $element.mouseup(function(evt){
        setTimeout(function(){ // to prevent calling onSelect when click on already existent selection without creating a new one
          var text = getSelectedText(),
          html = getSelectedHtml(),
          element = getSelectionBoundaryElement();
          if ( text != '' && typeof settings.onSelect === 'function' ) {
            if ( typeof settings.onDeselect === 'function' )
              $(document).one('mousedown', settings.onDeselect);
            if ( typeof evt.data !== 'object' || evt.data === null )
              evt.data = {};
            evt.data.selectedText = text;
            evt.data.selectedHtml = html;
            evt.data.selectionEndContainer = evt.target;
            if ( typeof settings.onDeselect === 'function' )
              settings.onSelect.call($element.get(0), evt);          
          }
        }, 0);        
      })
    })
  }
  
})(jQuery);

(function($){

  $.fn.UICheckbox = function(){
    return this.each(function(){
      var $real = $(this),
      $decor = $('<span class="ui-checkbox"></span>'),
      $label = $('label[for="' + $real.attr('id') + '"]');
          
      var $radios = [];
      if ( $(this).attr('type') == 'radio' )
        $radios = $('input[type="radio"][name="' + $(this).attr('name') + '"]')
          
      if ( $real.attr('checked') )
        $decor.addClass('ui-checkbox-checked');

      // �������� �������� ��������� �������� � ����������� �� �������������
      $decor.add($label).click(function(){
        if ( $real.attr('checked') ) {
          if ( $radios.length > 0 ) return false; //�� ������� ������� ���� � ��� �����������
          $decor.removeClass('ui-checkbox-checked');
          $real.attr('checked', false).trigger('change');
        } else {
          $decor.addClass('ui-checkbox-checked');
          $real.attr('checked', true).trigger('change');
        }
        if ( $radios.length > 0 )
          $radios.trigger('change');
        return false;
      });
      
      // ������ ����� ��� ��������� ��������� �������� (����� ��� ������� ��������� �������� ��������� ��������)
      $real.change(function(){
        if ( $real.attr('checked') ) {
          $decor.addClass('ui-checkbox-checked');
        } else {
          $decor.removeClass('ui-checkbox-checked');
        }
      });
      
      $label.hover(function(){
        $decor.addClass('ui-checkbox-hover');
      },function(){
        $decor.removeClass('ui-checkbox-hover');
      });

      $real.hide();
      $decor.insertAfter($real);
    });
  };

})(jQuery);


(function($){
  $.event.special.mousewheel={
    setup:function(){
      var handler=$.event.special.mousewheel.handler;
      if($.browser.mozilla)$(this).bind('mousemove.mousewheel',function(event){
        $.data(this,'mwcursorposdata',{
          pageX:event.pageX,
          pageY:event.pageY,
          clientX:event.clientX,
          clientY:event.clientY
        });
      });
      if(this.addEventListener)this.addEventListener(($.browser.mozilla?'DOMMouseScroll':'mousewheel'),handler,false);else
        this.onmousewheel=handler;
    },
    teardown:function(){
      var handler=$.event.special.mousewheel.handler;
      $(this).unbind('mousemove.mousewheel');
      if(this.removeEventListener)this.removeEventListener(($.browser.mozilla?'DOMMouseScroll':'mousewheel'),handler,false);else
        this.onmousewheel=function(){};
      $.removeData(this,'mwcursorposdata');
    },
    handler:function(event){
      var args=Array.prototype.slice.call(arguments,1);
      event=$.event.fix(event||window.event);
      $.extend(event,$.data(this,'mwcursorposdata')||{});
      var delta=0,returnValue=true;
      if(event.wheelDelta)delta=event.wheelDelta/120;
      if(event.detail)delta=-event.detail/3;
      if($.browser.opera)delta=-event.wheelDelta;
      event.data=event.data||{};
      event.type="mousewheel";
      args.unshift(delta);
      args.unshift(event);
      return $.event.handle.apply(this,args);
    }
  };
  $.fn.extend({
    mousewheel:function(fn){
      return fn?this.bind("mousewheel",fn):this.trigger("mousewheel");
    },
    unmousewheel:function(fn){
      return this.unbind("mousewheel",fn);
    }
  });
})(jQuery);


/*
 *  IE onDomReady function which is used to run expression after DOM is loaded
 */
(function(){

  var DOMready = setInterval(function(){
    try{
      document.documentElement.doScroll("left");
      clearInterval(DOMready);
      DOMready = true;
      for (var f in fn){
        fn[f]();
      }
    }catch(e){}
  }, 10),

  fn = [];

  window.onIEDomReady = function(el, func){
    if (!el) return;
    if ( typeof func != 'function' ) return;
    if (DOMready === true){
      func.call(el);
    } else {
      fn.push(function(){
        func.call(el);
      });
    }
  }

})()

/**
 * Returns an object with mouse cursor position
 *
 * @param {Event} [evt]
 * @return {} {x:, y:}
 */
function mouseXY(evt) {
  if (evt.pageX && evt.pageY)
    return {
      x : evt.pageX,
      y : evt.pageY
    };
  else if (evt.clientX && evt.clientY)
    return {
      x : evt.clientX
      + (document.documentElement.scrollLeft
        ? document.documentElement.scrollLeft
        : document.body.scrollLeft),
      y : evt.clientY
      + (document.documentElement.scrollTop
        ? document.documentElement.scrollTop
        : document.body.scrollTop)
    };
  else
    return null;
}

/**
 *  Enable/Disable text selection on a page
 */
function toggleTextSelect(bool){
  $( document.body )[ bool ? "unbind" : "bind" ]("selectstart", function(){
    return false;
  } )
  .attr("unselectable", bool ? "off" : "on" )
  .css("MozUserSelect", bool ? "" : "none" );
} 


/**
 * Returns page and window size
 *
 * @return Array(pageWidth,pageHeight,windowWidth,windowHeight)
 */
getPageSize = function() {
  var xScroll, yScroll;
  if (window.innerHeight && window.scrollMaxY) {
    xScroll = window.innerWidth + window.scrollMaxX;
    yScroll = window.innerHeight + window.scrollMaxY;
  } else if (document.body.scrollHeight > document.body.offsetHeight) { // all
    // but
    // Explorer
    // Mac
    xScroll = document.body.scrollWidth;
    yScroll = document.body.scrollHeight;
  } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla
    // and Safari
    xScroll = document.body.offsetWidth;
    yScroll = document.body.offsetHeight;
  }
  var windowWidth, windowHeight;
  if (self.innerHeight) { // all except Explorer
    if (document.documentElement.clientWidth) {
      windowWidth = document.documentElement.clientWidth;
    } else {
      windowWidth = self.innerWidth;
    }
    windowHeight = self.innerHeight;
  } else if (document.documentElement
    && document.documentElement.clientHeight) { // Explorer 6 Strict
    // Mode
    windowWidth = document.documentElement.clientWidth;
    windowHeight = document.documentElement.clientHeight;
  } else if (document.body) { // other Explorers
    windowWidth = document.body.clientWidth;
    windowHeight = document.body.clientHeight;
  }
  // for small pages with total height less then height of the viewport
  if (yScroll < windowHeight) {
    pageHeight = windowHeight;
  } else {
    pageHeight = yScroll;
  }
  // for small pages with total width less then width of the viewport
  if (xScroll < windowWidth) {
    pageWidth = xScroll;
  }else {
    pageWidth = windowWidth;
  }
  arrayPageSize = new Array(pageWidth, pageHeight, windowWidth, windowHeight, xScroll, yScroll);
  return arrayPageSize;
};

/**
 / THIRD FUNCTION
 * getPageScroll() by quirksmode.com
 *
 * @return Array Return an array with x,y page scroll values.
 */
function getPageScroll() {
  var xScroll, yScroll;
  if (self.pageYOffset) {
    yScroll = self.pageYOffset;
    xScroll = self.pageXOffset;
  } else if (document.documentElement && document.documentElement.scrollTop) {	 // Explorer 6 Strict
    yScroll = document.documentElement.scrollTop;
    xScroll = document.documentElement.scrollLeft;
  } else if (document.body) {// all other Explorers
    yScroll = document.body.scrollTop;
    xScroll = document.body.scrollLeft;
  }
  arrayPageScroll = new Array(xScroll,yScroll);
  return arrayPageScroll;
};

// Cross-broswer implementation of text ranges and selections
// documentation: http://bililite.com/blog/2011/01/11/cross-browser-�and-selections/
// Version: 1.1
// Copyright (c) 2010 Daniel Wachsstock
// MIT license:
// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.



// insert characters in a textarea or text input field
// special characters are enclosed in {}; use {{} for the { character itself
// documentation: http://bililite.com/blog/2008/08/20/the-fnsendkeys-plugin/
// Version: 2.0
// Copyright (c) 2010 Daniel Wachsstock
// MIT license:
// Permission is hereby granted, free of charge, to any person
// obtaining a copy of this software and associated documentation
// files (the "Software"), to deal in the Software without
// restriction, including without limitation the rights to use,
// copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the
// Software is furnished to do so, subject to the following
// conditions:

// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.

// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
// HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
// OTHER DEALINGS IN THE SOFTWARE.

;(function($){

$.fn.sendkeys = function (x, opts){
	return this.each( function(){
		var localkeys = $.extend({}, opts, $(this).data('sendkeys')); // allow for element-specific key functions
		// most elements to not keep track of their selection when they lose focus, so we have to do it for them
		var rng = $.data (this, 'sendkeys.selection');
		if (!rng){
			rng = bililiteRange(this).bounds('selection');
			$.data(this, 'sendkeys.selection', rng);
			$(this).bind('mouseup.sendkeys', function(){
				// we have to update the saved range. The routines here update the bounds with each press, but actual keypresses and mouseclicks do not
				$.data(this, 'sendkeys.selection').bounds('selection');
			}).bind('keyup.sendkeys', function(evt){
				// restore the selection if we got here with a tab (a click should select what was clicked on)
				if (evt.which == 9){
					// there's a flash of selection when we restore the focus, but I don't know how to avoid that.
					$.data(this, 'sendkeys.selection').select();
				}else{
					$.data(this, 'sendkeys.selection').bounds('selection');
				}	
			});
		}
		this.focus();
		if (typeof x === 'undefined') return; // no string, so we just set up the event handlers
		x.replace(/\n/g, '{enter}'). // turn line feeds into explicit break insertions
		  replace(/{[^}]*}|[^{]+/g, function(s){
			(localkeys[s] || $.fn.sendkeys.defaults[s] || $.fn.sendkeys.defaults.simplechar)(rng, s);
		  });
		$(this).trigger({type: 'sendkeys', which: x});
	});
}; // sendkeys


// add the functions publicly so they can be overridden
$.fn.sendkeys.defaults = {
	simplechar: function (rng, s){
		rng.text(s, 'end');
		for (var i =0; i < s.length; ++i){
			var x = s.charCodeAt(i);
			// a bit of cheating: rng._el is the element associated with rng.
			$(rng._el).trigger({type: 'keypress', keyCode: x, which: x, charCode: x});
		}
	},
	'{{}': function (rng){
		$.fn.sendkeys.defaults.simplechar(rng, '{')
	},
	'{enter}': function (rng){
		rng.insertEOL();
		var b = rng.bounds();
		rng.select();
		var x = '\n'.charCodeAt(0);
		$(rng._el).trigger({type: 'keypress', keyCode: x, which: x, charCode: x});
	},
	'{backspace}': function (rng){
		var b = rng.bounds();
		if (b[0] == b[1]) rng.bounds([b[0]-1, b[0]]); // no characters selected; it's just an insertion point. Remove the previous character
		rng.text('', 'end'); // delete the characters and update the selection
	},
	'{del}': function (rng){
		var b = rng.bounds();
		if (b[0] == b[1]) rng.bounds([b[0], b[0]+1]); // no characters selected; it's just an insertion point. Remove the next character
		rng.text('', 'end'); // delete the characters and update the selection
	},
	'{rightarrow}':  function (rng){
		var b = rng.bounds();
		if (b[0] == b[1]) ++b[1]; // no characters selected; it's just an insertion point. Move to the right
		rng.bounds([b[1], b[1]]).select();
	},
	'{leftarrow}': function (rng){
		var b = rng.bounds();
		if (b[0] == b[1]) --b[0]; // no characters selected; it's just an insertion point. Move to the left
		rng.bounds([b[0], b[0]]).select();
	},
	'{selectall}' : function (rng){
		rng.bounds('all').select();
	}
};

})(jQuery)

/**
 *  mmVirtualKeyboard
 * 
 * (c) 2012 Mykhailenko Maksym
 */
;(function($, window, undefined){
  
  function processKeyDown(evt){
    return evt.data._processKeyDown(evt);      
  }
  
  function processKeyUp(evt){    
    return evt.data._processKeyUp(evt);
  }
  
  function processKeyPress(evt){
    return evt.data._processKeyPress(evt);
  }
  
  var mmVirtualKeyboard = function(settings){
    
    this.keys = [
      [192, 49, 50, 51, 52, 53, 54, 55, 56, 57, 48, 189, 187],
      [81, 87, 69, 82, 84, 89, 85, 73, 79, 80, 219, 221, 220],
      [20, 65, 83, 68, 70, 71, 72, 74, 75, 76, 186, 222],
      [16, 90, 88, 67, 86, 66, 78, 77, 188, 190, 191, 16],
      [32]
    ];
    
    this.map = {
      normal: "\u04511234567890-=\u0439\u0446\u0443\u043a\u0435\u043d\u0433\u0448\u0449\u0437\u0445\u044a\\\u0444\u044b\u0432\u0430\u043f\u0440\u043e\u043b\u0434\u0436\u044d\u044f\u0447\u0441\u043c\u0438\u0442\u044c\u0431\u044e.",
      shift: '\u0401!"\u2116;%:?*()_+\u0419\u0426\u0423\u041a\u0415\u041d\u0413\u0428\u0429\u0417\u0425\u042a/\u0424\u042b\u0412\u0410\u041f\u0420\u041e\u041b\u0414\u0416\u042d\u042f\u0427\u0421\u041c\u0418\u0422\u042c\u0411\u042e,',
      capslock: '\u04011234567890-=\u0419\u0426\u0423\u041a\u0415\u041d\u0413\u0428\u0429\u0417\u0425\u042a\\\u0424\u042b\u0412\u0410\u041f\u0420\u041e\u041b\u0414\u0416\u042d\u042f\u0427\u0421\u041c\u0418\u0422\u042c\u0411\u042e.',
      capslockshift: '\u0451!"\u2116;%:?*()_+\u0439\u0446\u0443\u043a\u0435\u043d\u0433\u0448\u0449\u0437\u0445\u044a/\u0444\u044b\u0432\u0430\u043f\u0440\u043e\u043b\u0434\u0436\u044d\u044f\u0447\u0441\u043c\u0438\u0442\u044c\u0431\u044e,'
    };
    
    this.elements = [];   
    
    this.activeElement = null;
    
    this.mapState = 'normal';
    
    this.$keyboard = null;
    
    this.$popup = $(
      '<div class="mmvk-popup">' +
        '<div class="mmvk-popup-content">' +
          '<div class="mmvk-popup-title"><div class="mmvk-popup-close"></div><div class="mmvk-popup-handle"></div></div>' + 
        '</div>' +
        '<div class="mmvk-popup-tl"></div>' +
        '<div class="mmvk-popup-tc"></div>' +
        '<div class="mmvk-popup-tr"></div>' +
        '<div class="mmvk-popup-ml"></div>' +
        '<div class="mmvk-popup-mr"></div>' +
        '<div class="mmvk-popup-bl"></div>' +
        '<div class="mmvk-popup-bc"></div>' +
        '<div class="mmvk-popup-br"></div>' +
      '</div>'
    );
      
    this.$popup_content = this.$popup.find('div.mmvk-popup-content');
    this.$popup_title = this.$popup.find('div.mmvk-popup-title');
    this.$popup_close = this.$popup.find('div.mmvk-popup-close');
    this.$popup_handle = this.$popup.find('div.mmvk-popup-handle');
    
    this.$buttons = null;
    
    this.$shifts = null;
    
    this.txtButtons = [];
    
    this.isShift = false;
    
    this.isCapsLock = false;
    
    this.stopKeyPress = false; // use for Opera
    
    this._init();
  }  
  
  mmVirtualKeyboard.prototype = {
    
    _init: function(){
      var self = this, 
          keyboard = [], 
          i, j,
          charMapPos = 0;
      
      for ( i = 0; i < this.keys.length; i++ ) {
        keyboard.push('<div class="mmvk-row">');
        for ( j = 0; j < this.keys[i].length; j++ ) {
          keyboard.push('<button name="k' + this.keys[i][j] + '" class="mmvk-k' + this.keys[i][j] + '">&nbsp;</button>');
        }
        keyboard.push('</div>')
      }
          
      this.$keyboard = $('<div class="mmvk-keyboard">' + keyboard.join('') + '</div>');    
      
      this.$shifts = this.$keyboard.find('button[name="k16"]');
      this.$buttons = this.$keyboard.find('button')
      
      this.$buttons.each(function() {
        var $button = $(this), 
            timeout = null,
            keyCode = parseInt($button.attr('name').substring(1));                 
        
              
        if ( jQuery.inArray(keyCode, [8, 20, 16, 13]) >= 0 )
          $button.html('<span>&nbsp;</span>');             

        if ( jQuery.inArray(keyCode, [20, 16]) < 0 ) {
          (function(charMapPos){
            $button.bind('click.mmVirtualKeyboard', function(evt, isFake){
              var character;              

              if ( self.activeElement === null ) return;

              $button.addClass('active');
              clearTimeout(timeout);
              timeout = setTimeout(function(){
                $button.removeClass('active');
              },100);                          

              switch (  self.activeElement.type ) {
                case 'ckeditor':
                  switch ( keyCode ) {
                    case 8:
                        /*var selection = self.activeElement.element.getSelection();
                        var ranges = selection.getRanges();
                        if ( selection.getSelectedText().length < 1 ) {
                          //console.log(ranges[0]);
                          ranges[0].setEnd(ranges[0].endContainer, ranges[0].endOffset);
                          if ( ranges[0].endOffset < 2 ) {
                            //console.log(ranges[0].endContainer);
                          } else {
                            ranges[0].setStart(ranges[0].endContainer, ranges[0].endOffset - 1);
                          }
                          selection.selectRanges(ranges);
                        }
                        
                        ranges[0].deleteContents(true);*/
                      character = '\x08'; 
                      self.activeElement.element.insertText(character);
                      break;
                    case 13:
                      character = '\n'; 
                      self.activeElement.element.insertText(character);
                      break;
                    case 32:
                      character = ' '; 
                      self.activeElement.element.insertText(character);
                      break;
                    default:
                      character = self.map[self.mapState].charAt(charMapPos);
                      self.activeElement.element.insertText(character);
                      break;                      
                  }                           
                  break;
                default:
                  switch ( keyCode ) {
                    case 8:character = '{backspace}';break;
                    case 13:character = '{enter}';break;
                    case 32:character = ' ';break;
                    default:character = self.map[self.mapState].charAt(charMapPos);
                  }
                  self.activeElement.element.sendkeys(character);
              }   
              
              // disable shift if press text key
              if ( !isFake && self.isShift && jQuery.inArray(keyCode, [8, 13, 32]) < 0 ) {
                self.$shifts.removeClass('active');
                self.isShift = false;
                self._updateMapState();
              }   
                
            });         
          })(charMapPos);
        } else if ( keyCode === 16 ) { // shift       
          $button.bind('click.mmVirtualKeyboard', function(evt){
            if ( self.isShift ) {
              self.$shifts.removeClass('active');
              self.isShift = false;
            } else {
              self.$shifts.addClass('active');
              self.isShift = true;
            }
            self._updateMapState();
          });
        } else if ( keyCode === 20 ) { // capslock
          $button.bind('click.mmVirtualKeyboard', function(evt){
            if ( self.isCapsLock ) {
              $button.removeClass('active');
              self.isCapsLock = false;
            } else {
              $button.addClass('active');
              self.isCapsLock = true;
            }
            self._updateMapState();
          });
        }
        
        if ( jQuery.inArray(keyCode, [8, 20, 16, 13, 32]) < 0 ) {
          self.txtButtons.push($button);    
          charMapPos++;
        }
          
      })
      
      this._loadMap(this.map[this.mapState]);
      
      $('textarea, input[type="text"]').each(function(index, element){
        self.addElement({element: $(element), type: 'text'});
      })
      
      this.$popup_content.append(this.$keyboard);
      this.$popup.appendTo(document.body);
      
      this._initDrag();
      
      this.$popup_close.bind("click", function(){
        self.hide();
      });
    
      mmVirtualKeyboard.instance = this;
    },

    _processKeyDown: function(evt){
      var keyCode = evt.keyCode ? evt.keyCode : evt.which,
          i, j;
//          console.log('press',keyCode, evt.keyCode, evt.which);
      for ( i = 0; i < this.keys.length; i++ ) {
        for ( j = 0; j < this.keys[i].length; j++ ) {
          if ( keyCode === this.keys[i][j] && !evt.altKey && !evt.ctrlKey ) {
            if ( jQuery.inArray(keyCode, [16]) < 0 ) {
              this.$buttons.filter('[name="k' + keyCode + '"]').trigger('click', [true]);
              return this.stopKeyPress = false;
            } else { // shift
              this.$shifts.addClass('active');
              this.isShift = true;
              this._updateMapState();
              return this.stopKeyPress = true;
            }            
          }
        }
      }
      return this.stopKeyPress = true;
    },
    
    _processKeyUp: function(evt){
      var keyCode = evt.keyCode ? evt.keyCode : evt.which;
      if ( keyCode === 16 ) { //shift
        this.$shifts.removeClass('active');
        this.isShift = false;
        this._updateMapState();
        return false;
      }
      return true;
    },
    
    _processKeyPress: function(evt){            
      return this.stopKeyPress;
    },
    
    _updateMapState : function(){
      if ( !this.isShift && !this.isCapsLock ) {
        this.mapState = 'normal'
      } else if ( this.isShift && !this.isCapsLock ) {
        this.mapState = 'shift'
      } else if ( !this.isShift && this.isCapsLock ) {
        this.mapState = 'capslock'
      } else {
        this.mapState = 'capslockshift'
      }      
//      console.log(this.mapState);
      this._loadMap(this.map[this.mapState]);
    },
    
    _loadMap: function(map){
      for ( i = 0; i < this.txtButtons.length; i++ ) {
        var ch = map.charAt(i),
        chCode = map.charCodeAt(i);
        this.txtButtons[i].html("\\'\"".indexOf(ch) >= 0 || chCode > 127 || chCode < 33 ? "&#" + chCode + ";" : ch);
      }
    },
    
    /**
     * TODO Prevent exceed window borders    
     * TODO Cover iframes to prevent mousemove problem
     */
    _initDrag: function(){
      var self = this,
          startMouse = null, 
          startLeft = 0,
          startTop = 0;
      
      function onMouseMove(evt) {
        var currentMouse = mouseXY(evt);
        
        //console.log("move: ", currentMouse);
        
        if ( !currentMouse ) return false;
        
        self.$popup.css({
          left: startLeft + (currentMouse.x - startMouse.x),
          top:  startTop + (currentMouse.y - startMouse.y)
        });
        
        return false;
      }
      
      function onMouseUp() {
        $(document.body).unbind('mousemove.mmVirtualKeyboard', onMouseMove);
        $(document.body).unbind('mouseup.mmVirtualKeyboard', onMouseUp);
        
        return false;
      }
      
      this.$popup_handle.bind('mousedown.mmVirtualKeyboard', function(evt){
        startMouse = mouseXY(evt);
        startLeft = parseInt(self.$popup.css('left'));
        startTop = parseInt(self.$popup.css('top'));
        
        //console.log("start: ", startMouse, startLeft, startTop);
        
        $(document.body).bind('mousemove.mmVirtualKeyboard', onMouseMove);
        $(document.body).bind('mouseup.mmVirtualKeyboard', onMouseUp);
        
        return false;
      })
    },
    
    isVisible: function(){
      return this.$popup.is(':visible');        
    },
    
    addElement: function(element){
      var self = this;
      this.elements.push(element);
      switch ( element.type ) {
        case 'ckeditor':
          element.element.on('focus', function(){
            self.activeElement = element;
          });
          break;
        default:
          element.element.focus(function(){
            self.activeElement = element;
          });
      }      
    },
    
    show: function(){
      var popupHeight,
          popupWidth,
          winSize = getPageSize();
          
      this.$popup
        .css('visibility', 'hidden')
        .show();
        
      popupHeight = this.$popup.height();
      popupWidth = this.$popup.width();
      
      this.$popup.css({
        left: (winSize[2]-popupWidth)/2 + 'px',
        top: (winSize[3]-popupHeight)/2 + 'px',
        visibility: 'visible'
      })          
      
      this._catchKeypresses();
    },
    
    hide: function(){
      this.$popup.hide();
      this._uncatchKeypresses();
    },
    
    _catchKeypresses: function(){
      $(document.body).add($('iframe').contents()).bind('keydown', this, processKeyDown);
      $(document.body).add($('iframe').contents()).bind('keyup', this, processKeyUp);
      if ( $.browser.opera )
        $(document.body).add($('iframe').contents()).bind('keypress', this, processKeyPress);
    },
    
    _uncatchKeypresses: function(){
      $(document.body).add($('iframe').contents()).unbind('keydown', processKeyDown);
      $(document.body).add($('iframe').contents()).unbind('keyup', processKeyUp);
      if ( $.browser.opera )
        $(document.body).add($('iframe').contents()).unbind('keypress', processKeyPress);
    }
    
  }
  
  mmVirtualKeyboard.instance = null;
  
  window.mmVirtualKeyboard = mmVirtualKeyboard;
  
})(jQuery, window);



/*!
 * jQuery Transit - CSS3 transitions and transformations
 * (c) 2011-2012 Rico Sta. Cruz <rico@ricostacruz.com>
 * MIT Licensed.
 *
 * http://ricostacruz.com/jquery.transit
 * http://github.com/rstacruz/jquery.transit
 */

(function($) {
  $.transit = {
    version: "0.9.9",

    // Map of $.css() keys to values for 'transitionProperty'.
    // See https://developer.mozilla.org/en/CSS/CSS_transitions#Properties_that_can_be_animated
    propertyMap: {
      marginLeft    : 'margin',
      marginRight   : 'margin',
      marginBottom  : 'margin',
      marginTop     : 'margin',
      paddingLeft   : 'padding',
      paddingRight  : 'padding',
      paddingBottom : 'padding',
      paddingTop    : 'padding'
    },

    // Will simply transition "instantly" if false
    enabled: true,

    // Set this to false if you don't want to use the transition end property.
    useTransitionEnd: false
  };

  var div = document.createElement('div');
  var support = {};

  // Helper function to get the proper vendor property name.
  // (`transition` => `WebkitTransition`)
  function getVendorPropertyName(prop) {
    // Handle unprefixed versions (FF16+, for example)
    if (prop in div.style) return prop;

    var prefixes = ['Moz', 'Webkit', 'O', 'ms'];
    var prop_ = prop.charAt(0).toUpperCase() + prop.substr(1);

    if (prop in div.style) { return prop; }

    for (var i=0; i<prefixes.length; ++i) {
      var vendorProp = prefixes[i] + prop_;
      if (vendorProp in div.style) { return vendorProp; }
    }
  }

  // Helper function to check if transform3D is supported.
  // Should return true for Webkits and Firefox 10+.
  function checkTransform3dSupport() {
    div.style[support.transform] = '';
    div.style[support.transform] = 'rotateY(90deg)';
    return div.style[support.transform] !== '';
  }

  var isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

  // Check for the browser's transitions support.
  support.transition      = getVendorPropertyName('transition');
  support.transitionDelay = getVendorPropertyName('transitionDelay');
  support.transform       = getVendorPropertyName('transform');
  support.transformOrigin = getVendorPropertyName('transformOrigin');
  support.transform3d     = checkTransform3dSupport();

  var eventNames = {
    'transition':       'transitionEnd',
    'MozTransition':    'transitionend',
    'OTransition':      'oTransitionEnd',
    'WebkitTransition': 'webkitTransitionEnd',
    'msTransition':     'MSTransitionEnd'
  };

  // Detect the 'transitionend' event needed.
  var transitionEnd = support.transitionEnd = eventNames[support.transition] || null;

  // Populate jQuery's `$.support` with the vendor prefixes we know.
  // As per [jQuery's cssHooks documentation](http://api.jquery.com/jQuery.cssHooks/),
  // we set $.support.transition to a string of the actual property name used.
  for (var key in support) {
    if (support.hasOwnProperty(key) && typeof $.support[key] === 'undefined') {
      $.support[key] = support[key];
    }
  }

  // Avoid memory leak in IE.
  div = null;

  // ## $.cssEase
  // List of easing aliases that you can use with `$.fn.transition`.
  $.cssEase = {
    '_default':       'ease',
    'in':             'ease-in',
    'out':            'ease-out',
    'in-out':         'ease-in-out',
    'snap':           'cubic-bezier(0,1,.5,1)',
    // Penner equations
    'easeOutCubic':   'cubic-bezier(.215,.61,.355,1)',
    'easeInOutCubic': 'cubic-bezier(.645,.045,.355,1)',
    'easeInCirc':     'cubic-bezier(.6,.04,.98,.335)',
    'easeOutCirc':    'cubic-bezier(.075,.82,.165,1)',
    'easeInOutCirc':  'cubic-bezier(.785,.135,.15,.86)',
    'easeInExpo':     'cubic-bezier(.95,.05,.795,.035)',
    'easeOutExpo':    'cubic-bezier(.19,1,.22,1)',
    'easeInOutExpo':  'cubic-bezier(1,0,0,1)',
    'easeInQuad':     'cubic-bezier(.55,.085,.68,.53)',
    'easeOutQuad':    'cubic-bezier(.25,.46,.45,.94)',
    'easeInOutQuad':  'cubic-bezier(.455,.03,.515,.955)',
    'easeInQuart':    'cubic-bezier(.895,.03,.685,.22)',
    'easeOutQuart':   'cubic-bezier(.165,.84,.44,1)',
    'easeInOutQuart': 'cubic-bezier(.77,0,.175,1)',
    'easeInQuint':    'cubic-bezier(.755,.05,.855,.06)',
    'easeOutQuint':   'cubic-bezier(.23,1,.32,1)',
    'easeInOutQuint': 'cubic-bezier(.86,0,.07,1)',
    'easeInSine':     'cubic-bezier(.47,0,.745,.715)',
    'easeOutSine':    'cubic-bezier(.39,.575,.565,1)',
    'easeInOutSine':  'cubic-bezier(.445,.05,.55,.95)',
    'easeInBack':     'cubic-bezier(.6,-.28,.735,.045)',
    'easeOutBack':    'cubic-bezier(.175, .885,.32,1.275)',
    'easeInOutBack':  'cubic-bezier(.68,-.55,.265,1.55)'
  };

  // ## 'transform' CSS hook
  // Allows you to use the `transform` property in CSS.
  //
  //     $("#hello").css({ transform: "rotate(90deg)" });
  //
  //     $("#hello").css('transform');
  //     //=> { rotate: '90deg' }
  //
  $.cssHooks['transit:transform'] = {
    // The getter returns a `Transform` object.
    get: function(elem) {
      return $(elem).data('transform') || new Transform();
    },

    // The setter accepts a `Transform` object or a string.
    set: function(elem, v) {
      var value = v;

      if (!(value instanceof Transform)) {
        value = new Transform(value);
      }

      // We've seen the 3D version of Scale() not work in Chrome when the
      // element being scaled extends outside of the viewport.  Thus, we're
      // forcing Chrome to not use the 3d transforms as well.  Not sure if
      // translate is affectede, but not risking it.  Detection code from
      // http://davidwalsh.name/detecting-google-chrome-javascript
      if (support.transform === 'WebkitTransform' && !isChrome) {
        elem.style[support.transform] = value.toString(true);
      } else {
        elem.style[support.transform] = value.toString();
      }

      $(elem).data('transform', value);
    }
  };

  // Add a CSS hook for `.css({ transform: '...' })`.
  // In jQuery 1.8+, this will intentionally override the default `transform`
  // CSS hook so it'll play well with Transit. (see issue #62)
  $.cssHooks.transform = {
    set: $.cssHooks['transit:transform'].set
  };

  // jQuery 1.8+ supports prefix-free transitions, so these polyfills will not
  // be necessary.
  if ($.fn.jquery < "1.8") {
    // ## 'transformOrigin' CSS hook
    // Allows the use for `transformOrigin` to define where scaling and rotation
    // is pivoted.
    //
    //     $("#hello").css({ transformOrigin: '0 0' });
    //
    $.cssHooks.transformOrigin = {
      get: function(elem) {
        return elem.style[support.transformOrigin];
      },
      set: function(elem, value) {
        elem.style[support.transformOrigin] = value;
      }
    };

    // ## 'transition' CSS hook
    // Allows you to use the `transition` property in CSS.
    //
    //     $("#hello").css({ transition: 'all 0 ease 0' });
    //
    $.cssHooks.transition = {
      get: function(elem) {
        return elem.style[support.transition];
      },
      set: function(elem, value) {
        elem.style[support.transition] = value;
      }
    };
  }

  // ## Other CSS hooks
  // Allows you to rotate, scale and translate.
  registerCssHook('scale');
  registerCssHook('translate');
  registerCssHook('rotate');
  registerCssHook('rotateX');
  registerCssHook('rotateY');
  registerCssHook('rotate3d');
  registerCssHook('perspective');
  registerCssHook('skewX');
  registerCssHook('skewY');
  registerCssHook('x', true);
  registerCssHook('y', true);

  // ## Transform class
  // This is the main class of a transformation property that powers
  // `$.fn.css({ transform: '...' })`.
  //
  // This is, in essence, a dictionary object with key/values as `-transform`
  // properties.
  //
  //     var t = new Transform("rotate(90) scale(4)");
  //
  //     t.rotate             //=> "90deg"
  //     t.scale              //=> "4,4"
  //
  // Setters are accounted for.
  //
  //     t.set('rotate', 4)
  //     t.rotate             //=> "4deg"
  //
  // Convert it to a CSS string using the `toString()` and `toString(true)` (for WebKit)
  // functions.
  //
  //     t.toString()         //=> "rotate(90deg) scale(4,4)"
  //     t.toString(true)     //=> "rotate(90deg) scale3d(4,4,0)" (WebKit version)
  //
  function Transform(str) {
    if (typeof str === 'string') { this.parse(str); }
    return this;
  }

  Transform.prototype = {
    // ### setFromString()
    // Sets a property from a string.
    //
    //     t.setFromString('scale', '2,4');
    //     // Same as set('scale', '2', '4');
    //
    setFromString: function(prop, val) {
      var args =
        (typeof val === 'string')  ? val.split(',') :
        (val.constructor === Array) ? val :
        [ val ];

      args.unshift(prop);

      Transform.prototype.set.apply(this, args);
    },

    // ### set()
    // Sets a property.
    //
    //     t.set('scale', 2, 4);
    //
    set: function(prop) {
      var args = Array.prototype.slice.apply(arguments, [1]);
      if (this.setter[prop]) {
        this.setter[prop].apply(this, args);
      } else {
        this[prop] = args.join(',');
      }
    },

    get: function(prop) {
      if (this.getter[prop]) {
        return this.getter[prop].apply(this);
      } else {
        return this[prop] || 0;
      }
    },

    setter: {
      // ### rotate
      //
      //     .css({ rotate: 30 })
      //     .css({ rotate: "30" })
      //     .css({ rotate: "30deg" })
      //     .css({ rotate: "30deg" })
      //
      rotate: function(theta) {
        this.rotate = unit(theta, 'deg');
      },

      rotateX: function(theta) {
        this.rotateX = unit(theta, 'deg');
      },

      rotateY: function(theta) {
        this.rotateY = unit(theta, 'deg');
      },

      // ### scale
      //
      //     .css({ scale: 9 })      //=> "scale(9,9)"
      //     .css({ scale: '3,2' })  //=> "scale(3,2)"
      //
      scale: function(x, y) {
        if (y === undefined) { y = x; }
        this.scale = x + "," + y;
      },

      // ### skewX + skewY
      skewX: function(x) {
        this.skewX = unit(x, 'deg');
      },

      skewY: function(y) {
        this.skewY = unit(y, 'deg');
      },

      // ### perspectvie
      perspective: function(dist) {
        this.perspective = unit(dist, 'px');
      },

      // ### x / y
      // Translations. Notice how this keeps the other value.
      //
      //     .css({ x: 4 })       //=> "translate(4px, 0)"
      //     .css({ y: 10 })      //=> "translate(4px, 10px)"
      //
      x: function(x) {
        this.set('translate', x, null);
      },

      y: function(y) {
        this.set('translate', null, y);
      },

      // ### translate
      // Notice how this keeps the other value.
      //
      //     .css({ translate: '2, 5' })    //=> "translate(2px, 5px)"
      //
      translate: function(x, y) {
        if (this._translateX === undefined) { this._translateX = 0; }
        if (this._translateY === undefined) { this._translateY = 0; }

        if (x !== null && x !== undefined) { this._translateX = unit(x, 'px'); }
        if (y !== null && y !== undefined) { this._translateY = unit(y, 'px'); }

        this.translate = this._translateX + "," + this._translateY;
      }
    },

    getter: {
      x: function() {
        return this._translateX || 0;
      },

      y: function() {
        return this._translateY || 0;
      },

      scale: function() {
        var s = (this.scale || "1,1").split(',');
        if (s[0]) { s[0] = parseFloat(s[0]); }
        if (s[1]) { s[1] = parseFloat(s[1]); }

        // "2.5,2.5" => 2.5
        // "2.5,1" => [2.5,1]
        return (s[0] === s[1]) ? s[0] : s;
      },

      rotate3d: function() {
        var s = (this.rotate3d || "0,0,0,0deg").split(',');
        for (var i=0; i<=3; ++i) {
          if (s[i]) { s[i] = parseFloat(s[i]); }
        }
        if (s[3]) { s[3] = unit(s[3], 'deg'); }

        return s;
      }
    },

    // ### parse()
    // Parses from a string. Called on constructor.
    parse: function(str) {
      var self = this;
      str.replace(/([a-zA-Z0-9]+)\((.*?)\)/g, function(x, prop, val) {
        self.setFromString(prop, val);
      });
    },

    // ### toString()
    // Converts to a `transition` CSS property string. If `use3d` is given,
    // it converts to a `-webkit-transition` CSS property string instead.
    toString: function(use3d) {
      var re = [];

      for (var i in this) {
        if (this.hasOwnProperty(i)) {
          // Don't use 3D transformations if the browser can't support it.
          if ((!support.transform3d) && (
            (i === 'rotateX') ||
            (i === 'rotateY') ||
            (i === 'perspective') ||
            (i === 'transformOrigin'))) { continue; }

          if (i[0] !== '_') {
            if (use3d && (i === 'scale')) {
              re.push(i + "3d(" + this[i] + ",1)");
            } else if (use3d && (i === 'translate')) {
              re.push(i + "3d(" + this[i] + ",0)");
            } else {
              re.push(i + "(" + this[i] + ")");
            }
          }
        }
      }

      return re.join(" ");
    }
  };

  function callOrQueue(self, queue, fn) {
    if (queue === true) {
      self.queue(fn);
    } else if (queue) {
      self.queue(queue, fn);
    } else {
      fn();
    }
  }

  // ### getProperties(dict)
  // Returns properties (for `transition-property`) for dictionary `props`. The
  // value of `props` is what you would expect in `$.css(...)`.
  function getProperties(props) {
    var re = [];

    $.each(props, function(key) {
      key = $.camelCase(key); // Convert "text-align" => "textAlign"
      key = $.transit.propertyMap[key] || $.cssProps[key] || key;
      key = uncamel(key); // Convert back to dasherized

      if ($.inArray(key, re) === -1) { re.push(key); }
    });

    return re;
  }

  // ### getTransition()
  // Returns the transition string to be used for the `transition` CSS property.
  //
  // Example:
  //
  //     getTransition({ opacity: 1, rotate: 30 }, 500, 'ease');
  //     //=> 'opacity 500ms ease, -webkit-transform 500ms ease'
  //
  function getTransition(properties, duration, easing, delay) {
    // Get the CSS properties needed.
    var props = getProperties(properties);

    // Account for aliases (`in` => `ease-in`).
    if ($.cssEase[easing]) { easing = $.cssEase[easing]; }

    // Build the duration/easing/delay attributes for it.
    var attribs = '' + toMS(duration) + ' ' + easing;
    if (parseInt(delay, 10) > 0) { attribs += ' ' + toMS(delay); }

    // For more properties, add them this way:
    // "margin 200ms ease, padding 200ms ease, ..."
    var transitions = [];
    $.each(props, function(i, name) {
      transitions.push(name + ' ' + attribs);
    });

    return transitions.join(', ');
  }

  // ## $.fn.transition
  // Works like $.fn.animate(), but uses CSS transitions.
  //
  //     $("...").transition({ opacity: 0.1, scale: 0.3 });
  //
  //     // Specific duration
  //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500);
  //
  //     // With duration and easing
  //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500, 'in');
  //
  //     // With callback
  //     $("...").transition({ opacity: 0.1, scale: 0.3 }, function() { ... });
  //
  //     // With everything
  //     $("...").transition({ opacity: 0.1, scale: 0.3 }, 500, 'in', function() { ... });
  //
  //     // Alternate syntax
  //     $("...").transition({
  //       opacity: 0.1,
  //       duration: 200,
  //       delay: 40,
  //       easing: 'in',
  //       complete: function() { /* ... */ }
  //      });
  //
  $.fn.transition = $.fn.transit = function(properties, duration, easing, callback) {
    var self  = this;
    var delay = 0;
    var queue = true;

    // Account for `.transition(properties, callback)`.
    if (typeof duration === 'function') {
      callback = duration;
      duration = undefined;
    }

    // Account for `.transition(properties, duration, callback)`.
    if (typeof easing === 'function') {
      callback = easing;
      easing = undefined;
    }

    // Alternate syntax.
    if (typeof properties.easing !== 'undefined') {
      easing = properties.easing;
      delete properties.easing;
    }

    if (typeof properties.duration !== 'undefined') {
      duration = properties.duration;
      delete properties.duration;
    }

    if (typeof properties.complete !== 'undefined') {
      callback = properties.complete;
      delete properties.complete;
    }

    if (typeof properties.queue !== 'undefined') {
      queue = properties.queue;
      delete properties.queue;
    }

    if (typeof properties.delay !== 'undefined') {
      delay = properties.delay;
      delete properties.delay;
    }

    // Set defaults. (`400` duration, `ease` easing)
    if (typeof duration === 'undefined') { duration = $.fx.speeds._default; }
    if (typeof easing === 'undefined')   { easing = $.cssEase._default; }

    duration = toMS(duration);

    // Build the `transition` property.
    var transitionValue = getTransition(properties, duration, easing, delay);

    // Compute delay until callback.
    // If this becomes 0, don't bother setting the transition property.
    var work = $.transit.enabled && support.transition;
    var i = work ? (parseInt(duration, 10) + parseInt(delay, 10)) : 0;

    // If there's nothing to do...
    if (i === 0) {
      var fn = function(next) {
        self.css(properties);
        if (callback) { callback.apply(self); }
        if (next) { next(); }
      };

      callOrQueue(self, queue, fn);
      return self;
    }

    // Save the old transitions of each element so we can restore it later.
    var oldTransitions = {};

    var run = function(nextCall) {
      var bound = false;

      // Prepare the callback.
      var cb = function() {
        if (bound) { self.unbind(transitionEnd, cb); }

        if (i > 0) {
          self.each(function() {
            this.style[support.transition] = (oldTransitions[this] || null);
          });
        }

        if (typeof callback === 'function') { callback.apply(self); }
        if (typeof nextCall === 'function') { nextCall(); }
      };

      if ((i > 0) && (transitionEnd) && ($.transit.useTransitionEnd)) {
        // Use the 'transitionend' event if it's available.
        bound = true;
        self.bind(transitionEnd, cb);
      } else {
        // Fallback to timers if the 'transitionend' event isn't supported.
        window.setTimeout(cb, i);
      }

      // Apply transitions.
      self.each(function() {
        if (i > 0) {
          this.style[support.transition] = transitionValue;
        }
        $(this).css(properties);
      });
    };

    // Defer running. This allows the browser to paint any pending CSS it hasn't
    // painted yet before doing the transitions.
    var deferredRun = function(next) {
        this.offsetWidth; // force a repaint
        run(next);
    };

    // Use jQuery's fx queue.
    callOrQueue(self, queue, deferredRun);

    // Chainability.
    return this;
  };

  function registerCssHook(prop, isPixels) {
    // For certain properties, the 'px' should not be implied.
    if (!isPixels) { $.cssNumber[prop] = true; }

    $.transit.propertyMap[prop] = support.transform;

    $.cssHooks[prop] = {
      get: function(elem) {
        var t = $(elem).css('transit:transform');
        return t.get(prop);
      },

      set: function(elem, value) {
        var t = $(elem).css('transit:transform');
        t.setFromString(prop, value);

        $(elem).css({ 'transit:transform': t });
      }
    };

  }

  // ### uncamel(str)
  // Converts a camelcase string to a dasherized string.
  // (`marginLeft` => `margin-left`)
  function uncamel(str) {
    return str.replace(/([A-Z])/g, function(letter) { return '-' + letter.toLowerCase(); });
  }

  // ### unit(number, unit)
  // Ensures that number `number` has a unit. If no unit is found, assume the
  // default is `unit`.
  //
  //     unit(2, 'px')          //=> "2px"
  //     unit("30deg", 'rad')   //=> "30deg"
  //
  function unit(i, units) {
    if ((typeof i === "string") && (!i.match(/^[\-0-9\.]+$/))) {
      return i;
    } else {
      return "" + i + units;
    }
  }

  // ### toMS(duration)
  // Converts given `duration` to a millisecond string.
  //
  //     toMS('fast')   //=> '400ms'
  //     toMS(10)       //=> '10ms'
  //
  function toMS(duration) {
    var i = duration;

    // Allow for string durations like 'fast'.
    if ($.fx.speeds[i]) { i = $.fx.speeds[i]; }

    return unit(i, 'ms');
  }

  // Export some functions for testable-ness.
  $.transit.getTransitionValue = getTransition;
})(jQuery);

;( function( $, window ) {

/*
 *             Gallery slider
 */
  $.fn.gallerySlider = function( settings ) {
    return this.each( function(){
      settings = $.extend( {
        slide: '.slide',
        slideInfo: '.slide-info',
        slideInfoCont: '#gallery_slider-subtitle',
		//slideInfoCont: '#slide-title',
        animationSpeed: 500,
        animationEasing: '',
        clickEventName: 'click',
		useTransitions: $.support.transition && !( $.browser.mozilla && $.browser.version == 19 ) // � FF19 �������� � ����������
      }, settings );

      var $container = $( this ),
          $infoCont = $( settings.slideInfoCont ),
          $currInfoSlide = null,
          $currInfo = null,
          $slides = $container.find(settings.slide),
          activeSlideWidth = $container.width() / $slides.length, // set average width for pseudo open image
          slideWidthHidden = ( ( $container.width() - activeSlideWidth ) / ( $slides.length - 1 ) ),
          windowWidth = $( document.body ).width();

      function move() {
        var visibleWidth = 0;
        
        slideWidthHidden = ( ( $container.width() - activeSlideWidth ) / ( $slides.length - 1 ) );

        $slides.each( function( num ) {
          var marginLeft = Math.round( slideWidthHidden * num + visibleWidth );
          
          if ( settings.useTransitions ) {
            $( this ).transit( {
              x: marginLeft
            }, settings.animationSpeed );
          } else {
            $( this ).animate( {
              marginLeft: marginLeft
            }, settings.animationSpeed );
          }
          
          if ( $( this ).hasClass('visible') ) 
		  {
            var $info = $( this ).find( settings.slideInfo );

            visibleWidth = activeSlideWidth - slideWidthHidden;
			
            if ( $info.length > 0 ) 
			{
              changeInfo( $( this ), $info, marginLeft );
            } 
			else if ( $currInfo !== null ) 
			{
              if ( settings.useTransitions ) 
			  {
                $currInfo.transit( {
                  x: marginLeft
                }, settings.animationSpeed );
              } 
			  else 
			  {
                $currInfo.animate( {
                  marginLeft: marginLeft
                }, settings.animationSpeed );
              }
            }
          }
        } ); // $slides.each( function( num ) {
      } // move()

      function showInfo( $slide, $info, marginLeft ) {
		
        $currInfoSlide = $slide;
        $currInfo = $info.css( {
          //width: $slide.width()
        } );

        if ( settings.useTransitions ) {
          $currInfo.css({
            //x: marginLeft
          } );
        } else {
          $currInfo.css({
            //marginLeft: marginLeft
          } );
        }

        $currInfo.appendTo( $infoCont );

        var $mes = $currInfo.clone();
        $mes.css({
            visibility: 'hidden',
            position: 'absolute'
          })
          .appendTo($infoCont)
          .show();

        var height = $mes.outerHeight();
        $mes.remove();

        if ( !ENV.isHandledDevice ) {
          ( settings.useTransitions ? $.fn.transit : $.fn.animate ).call( $infoCont, {
            height: height
          }, settings.animationSpeed );
        } else {
          $infoCont.css( {
            height: height
          } );
        }
        
        $currInfo.css( {
          display: 'block',
          opacity: 0
        } );

        ( settings.useTransitions ? $.fn.transit : $.fn.animate ).call( $currInfo, {
          opacity: 1
        }, settings.animationSpeed );
      } // showInfo()

      function changeInfo( $slide, $info, marginLeft ){
        if ( $currInfo !== null ) {
          $currInfo
            .hide()
            .appendTo( $currInfoSlide );
          showInfo( $slide, $info, marginLeft );
        } else {
          showInfo( $slide, $info, marginLeft );
        }
      }

      function refresh() {
        var currWindowWidth = $( document.body ).width();
        if ( currWindowWidth !== windowWidth ) {
          windowWidth = currWindowWidth;
          move();
        }
      }

      function slideForward() {
        $slides.filter( '.visible' ).next().find( 'span.slide_overlay' ).trigger( settings.clickEventName );
        return false;
      }

      function slideBackward() {
        $slides.filter( '.visible' ).prev().find( 'span.slide_overlay' ).trigger( settings.clickEventName );
        return false;
      }

      function showSlide( index ) {
        var $slide = $slides.eq( index );

        if ( typeof _gaq !== 'undefined' ) {
          _gaq.push(['_trackEvent', 'gallarymiddle', 'click']);
        }
        if ( typeof yaCounter10282186 !== 'undefined' ) {
          yaCounter10282186.hit( '#m' + ( index + 1 ) );
        }        

        $slides.removeClass( 'visible' );
        $slide.addClass( 'visible' );

        activeSlideWidth = $slide.width();
        $slide.find( 'span.slide_overlay' ).width( activeSlideWidth );
        move();
      }

      function closeAll() {
		$slides.filter( '.visible' ).find('.slide-info').css({'display' : 'none'});
		//alert($slides.filter( '.visible' ).find('.slide-info').html())
        $slides.removeClass( 'visible' );
		
        activeSlideWidth = $container.width() / $slides.length; // set average width for pseudo open image
        move();
      }

      /*
       * INITIALIZE
       */
      $( window ).resize( refresh );

      // add overlayes to avoid image tooltip appering and zoom icon
      $slides.each(function( index ){
        var $slide = $( this ),
            $slideOv,
            $btnForwad,
            $btnBackward;
            
        $slide
          .find( '>a, span.wrap' )
          .append( '<span class="slide_overlay"><span class="slide_hover"></span></span>' )
          .append( '<span class="slide_control"><span class="slide_control-left"><span></span></span><span class="slide_control-right"><span></span></span></span>' );

        $slideOv = $slide.find( 'span.slide_overlay' );
        $btnForwad = $slide.find( 'span.slide_control-right' );
        $btnBackward = $slide.find( 'span.slide_control-left' );

        if ( index == 0 ) {
          $btnBackward.hide();
        }

        if ( index == $slides.length - 1 ) {
          $btnForwad.hide();
        }

        new NoClickDelay( $slideOv.get( 0 ) );
        new NoClickDelay( $btnForwad.get( 0 ) );
        new NoClickDelay( $btnBackward.get( 0 ) );

        $btnForwad.bind( settings.clickEventName, slideForward );
        $btnBackward.bind( settings.clickEventName, slideBackward );        

        $slideOv.bind( settings.clickEventName, function(){
          if ( $slide.hasClass( 'visible' ) ) {
            return true;
          } else {
            showSlide( index );
            $.address.value( 'm' + ( index + 1 ) );
            return false;
          }
        });
      });

      function processAddress() {
		  //alert($.address.path())
        var action = $.address.value().substr( 0, 1 );

        if ( action === 'm' || action === 'b' ) {
          var num = parseInt( $.address.value().substr( 1 ) ) - 1;
          if ( 0 <= num && num < $slides.length ) {
            showSlide( num );
            return;
          }
        }
        closeAll();
      }

      $.address.externalChange(function() {
          processAddress();
      });

      /*
       * RUN
       */
      processAddress();
	  

    });
  };

} )(jQuery, window);

/*!
 * jQuery UI Widget 1.8.18
 *
 * Copyright 2011, AUTHORS.txt (http://jqueryui.com/about)
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * http://docs.jquery.com/UI/Widget
 */
 ;(function(a,b){if(a.cleanData){var c=a.cleanData;a.cleanData=function(b){for(var d=0,e;(e=b[d])!=null;d++)try{a(e).triggerHandler("remove")}catch(f){}c(b)}}else{var d=a.fn.remove;a.fn.remove=function(b,c){return this.each(function(){c||(!b||a.filter(b,[this]).length)&&a("*",this).add([this]).each(function(){try{a(this).triggerHandler("remove")}catch(b){}});return d.call(a(this),b,c)})}}a.widget=function(b,c,d){var e=b.split(".")[0],f;b=b.split(".")[1],f=e+"-"+b,d||(d=c,c=a.Widget),a.expr[":"][f]=function(c){return!!a.data(c,b)},a[e]=a[e]||{},a[e][b]=function(a,b){arguments.length&&this._createWidget(a,b)};var g=new c;g.options=a.extend(!0,{},g.options),a[e][b].prototype=a.extend(!0,g,{namespace:e,widgetName:b,widgetEventPrefix:a[e][b].prototype.widgetEventPrefix||b,widgetBaseClass:f},d),a.widget.bridge(b,a[e][b])},a.widget.bridge=function(c,d){a.fn[c]=function(e){var f=typeof e=="string",g=Array.prototype.slice.call(arguments,1),h=this;e=!f&&g.length?a.extend.apply(null,[!0,e].concat(g)):e;if(f&&e.charAt(0)==="_")return h;f?this.each(function(){var d=a.data(this,c),f=d&&a.isFunction(d[e])?d[e].apply(d,g):d;if(f!==d&&f!==b){h=f;return!1}}):this.each(function(){var b=a.data(this,c);b?b.option(e||{})._init():a.data(this,c,new d(e,this))});return h}},a.Widget=function(a,b){arguments.length&&this._createWidget(a,b)},a.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",options:{disabled:!1},_createWidget:function(b,c){a.data(c,this.widgetName,this),this.element=a(c),this.options=a.extend(!0,{},this.options,this._getCreateOptions(),b);var d=this;this.element.bind("remove."+this.widgetName,function(){d.destroy()}),this._create(),this._trigger("create"),this._init()},_getCreateOptions:function(){return a.metadata&&a.metadata.get(this.element[0])[this.widgetName]},_create:function(){},_init:function(){},destroy:function(){this.element.unbind("."+this.widgetName).removeData(this.widgetName),this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled "+"ui-state-disabled")},widget:function(){return this.element},option:function(c,d){var e=c;if(arguments.length===0)return a.extend({},this.options);if(typeof c=="string"){if(d===b)return this.options[c];e={},e[c]=d}this._setOptions(e);return this},_setOptions:function(b){var c=this;a.each(b,function(a,b){c._setOption(a,b)});return this},_setOption:function(a,b){this.options[a]=b,a==="disabled"&&this.widget()[b?"addClass":"removeClass"](this.widgetBaseClass+"-disabled"+" "+"ui-state-disabled").attr("aria-disabled",b);return this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_trigger:function(b,c,d){var e,f,g=this.options[b];d=d||{},c=a.Event(c),c.type=(b===this.widgetEventPrefix?b:this.widgetEventPrefix+b).toLowerCase(),c.target=this.element[0],f=c.originalEvent;if(f)for(e in f)e in c||(c[e]=f[e]);this.element.trigger(c,d);return!(a.isFunction(g)&&g.call(this.element[0],c,d)===!1||c.isDefaultPrevented())}}})(jQuery);

;( function( $, window, undefined ) {

  $.widget( 'arch.validation', {
    options: {

    },

    _create: function() {      
    },

    _setOption: function( key, value ) {
      $.Widget.prototype._setOption.apply( this, arguments );
    },

    validate: function() {
      var that = this,
          isValid = this.element.val() != '',
          markAsValid = $.proxy( this.markAsValid, this ),
          markAsInvalid = $.proxy( this.markAsInvalid, this );

      if ( this.element.hasClass( 'ui_select-source' ) ) {
        markAsValid = function() {
          that.element.UISelect( 'markAsValid' );
        }
        markAsInvalid = function() {
          that.element.UISelect( 'markAsInvalid' );
        }
      }

      if ( isValid ) {
        markAsValid();
      } else {
        markAsInvalid();
      }
      return isValid;
    },

    markAsInvalid: function() {
      this.element.addClass( 'invalid' );
    },

    markAsValid: function() {
      this.element.removeClass( 'invalid' );
    },

    destroy: function() {
      $.Widget.prototype.destroy.call( this );
    }
  } );

} )(jQuery, window);

;( function( $, window, undefined ) {

  $.widget( 'arch.UISelect', {
    options: {
      showDuration: 100,
      hideDuration: 100
    },

    _create: function() {     
      this._createMarkup();
      this._initEvents();
    },

    _createMarkup: function() {
      var that = this;
      
      this.element.hide();
      
      this.ui = {};
      this.ui._ = $( [
        '<div class="ui_select ui_select-notset">',
          '<div class="ui_select-value-container">',
            '<div class="ui_select-tick"></div>',
            '<div class="ui_select-value"></div>',
          '</div>',
          '<div class="ui_select-dropdown">',
            '<div class="ui_select-dropdown-shadow">',
              '<div class="ui_select-dropdown-t"></div>',
              '<div class="ui_select-dropdown-rt"></div>',
              '<div class="ui_select-dropdown-r"></div>',
              '<div class="ui_select-dropdown-rb"></div>',
              '<div class="ui_select-dropdown-b"></div>',
              '<div class="ui_select-dropdown-lb"></div>',
              '<div class="ui_select-dropdown-l"></div>',
              '<div class="ui_select-dropdown-lt"></div>',
            '</div>',
            '<div class="ui_select-dropdown-content">',
              '<div class="ui_select-dropdown-tick"></div>',
              '<div class="ui_select-dropdown-title"></div>',
              '<ul class="ui_select-dropdown-list">',
              '</ul>',
            '</div>',
          '</div>',
        '</div>'
      ].join( '' ) );
      
      this.ui.value = this.ui._.find( '.ui_select-value' );
      this.ui.dropdown = this.ui._.find( '.ui_select-dropdown' );
      this.ui.list = this.ui._.find( '.ui_select-dropdown-list' );
      this.ui.title = this.ui._.find( '.ui_select-dropdown-title' );
      
      this.ui.value.text( this.element.find( 'option:selected').text() );
      this.ui.title.text( this.element.find( 'option:first').text() );

      if ( this.element.find( 'option:first').not( ':selected' ).length > 0 ) {
        this.ui._.removeClass( 'ui_select-notset' );
      }

      this.element.find( 'option' ).filter( ':not(:first)' ).each( function() {
        $( '<li' + ( $( this ).is( ':selected' ) ? ' class="selected"' : '' ) + '>' + $( this ).text() + '</li>' )
          .data( 'option', $( this ) )
          .appendTo( that.ui.list );
      } );

      this.ui.lis = this.ui.list.find( 'li' );

      this.ui._.insertAfter( this.element );
    },

    _initEvents: function() {
      var that = this;

      this.ui.lis.bind( 'click.' + this.widgetEventPrefix, function() {
        var $li = $( this );

        if ( $li.hasClass( 'selected') ) {
          return;
        }

        that.ui.value.text( $li.text() );
        that.ui._.removeClass( 'ui_select-notset' );

        that.ui.lis.removeClass( 'selected' );
        that.element.children( 'option:selected' ).removeAttr( 'selected' );

        $li.addClass( 'selected' );
        $li.data( 'option' ).attr( 'selected', 'selected' );
        
        that.element.trigger( 'change' );

        that._hideDropdown();
      } );

      this.ui._.bind( 'mouseenter', function() {
        that._showDropdown();
      } );

      this.ui._.bind( 'mouseleave', function() {
        that._hideDropdown();
      } );
    },

    _hideDropdown: function() {
      var that = this;

      this.ui.dropdown.animate( {opacity: 0 }, this.options.hideDuration, function() {
        that.ui.dropdown.css( 'display', 'none' );
      } );
    },

    _showDropdown: function() {
      this.ui.dropdown
        .css( {
          display: 'block',
          opacity: 0
        } )
        .animate( {opacity: 1 }, this.options.showDuration );
    },

    _setOption: function( key, value ) {
      $.Widget.prototype._setOption.apply( this, arguments );
    },


    markAsInvalid: function() {
      this.ui._.addClass( 'invalid' );
    },

    markAsValid: function() {
      this.ui._.removeClass( 'invalid' );
    },

    destroy: function() {
      $.Widget.prototype.destroy.call( this );
    }
  } );

} )(jQuery, window);




/**
 *             Huge gallery
 */
( function( $ ) {
  
  function showHugeImage( src, $thumbList ) {
    var settings = {
      overlayShowSpeed: 200,
      panelShowSpeed: 100,
      panelShowEasing: 'easeInQuad',
      thumbFadeOutSpeed: 200,
      thumbFadeInSpeed: 100,
      thumbFadeOutEasing: 'easeInQuad',
      thumbFadeInEasing: 'linear',
      thumbWidth: 43,
      thumbMargin: 12,
      moveInterval: 50,
      moveStep: 3,
      asideOpenDuration: 200,
      asideOpenEasing: 'easeOutQuad',
      asideCloseDuration: 200,
      asideCloseEasing: 'easeOutQuad'
    }

    var $html = $('\
        <div class="huge_gallery_overlay">\
          <div class="huge_gallery_wrapper">\
            <div class="huge_gallery_panel_top-wrapper">\
              <div class="huge_gallery_panel_top">\
                <div class="huge_gallery_panel-left">\
                  <span class="huge_gallery_fitscreen"></span>\
                </div>\
                <div class="huge_gallery_panel-right">\
                  <span class="huge_gallery_comments" style="display:none;"><span>38</span></span>\
                  <span class="huge_gallery_panel-comments-div" style="display:none;">/</span>\
                  <span class="huge_gallery_new_comment" style="display:none;">�������� �����������</span>\
                  <span class="huge_gallery_panel-div" style="display:none;">|</span>\
                  <span class="huge_gallery_close"><span>������� �������</span></span>\
                </div>\
              </div>\
            </div>\
            <div class="huge_gallery_main">\
              <img src="/img/s.gif" alt="" class="image" style="z-index: 1;" />\
              <span class="huge_gallery_prev"></span>\
              <span class="huge_gallery_next"></span>\
              <div class="huge_gallery_panel_bottom-wrapper"><div class="huge_gallery_panel_bottom">\
                <div class="huge_gallery_control">\
                  <span class="prev"><span></span></span>\
                  <span class="next"><span></span></span>\
                  <div class="huge_gallery_control_viewport_wrapper"><div class="huge_gallery_control_viewport">\
                    <table><tbody><tr>\
                    </tr></tbody></table>\
                  </div></div>\
                </div>\
              </div></div>\
            </div>\
            <div class="huge_gallery_aside">\
            </div>\
          </div>\
        </div>\
    ');

    var $wrapper = $html.find( '.huge_gallery_wrapper' ),
        $main = $html.find( '.huge_gallery_main' ),
        $aside = $html.find( '.huge_gallery_aside' ),
        $panel = $html.find( '.huge_gallery_panel_bottom-wrapper' ),
        $thumbViewport = $html.find( '.huge_gallery_control_viewport' ),
        $thumbContainer = $html.find( '.huge_gallery_control_viewport tr' ),
        $btnPrev = $html.find( '.huge_gallery_control span.prev, .huge_gallery_prev' ),
        $btnNext = $html.find( '.huge_gallery_control span.next, .huge_gallery_next' ),
        $btnClose = $html.find( '.huge_gallery_close' ),
        $btnFitScreen = $html.find( '.huge_gallery_fitscreen' ),
        $btnComments = $html.find( '.huge_gallery_comments' ),
        $btnNewComment = $html.find( '.huge_gallery_new_comment' ),

        preloadedImage = null,
        $thumbCurrent = null,
        currentIndex = 0,
        pageSize = null,
        timer = null,
        cursor = null,
        currentScrollTop = 0,
        $currentImage = null,
        currentImage = null,
        isTouchDevice = !!( 'ontouchstart' in window );

    /*
     * FUNCTIONS
     */

    function updatePageSizeInfo(){
      pageSize = getPageSize();
      if ( $btnFitScreen.hasClass( 'fit' ) ) {
        if ( $currentImage && currentImage ) {
          fitImage( $currentImage, currentImage );
        }
      }
    }

    function fitImage( $img, img ){
      var ratio = Math.min( pageSize[ 2 ] / img.width, pageSize[ 3 ] / img.height );
      if ( ratio < 1 ) {
        $img
          .attr( 'width', img.width * ratio )
          .attr( 'height', img.height * ratio )
          .css( {
            marginTop: - $img.height() / 2,
            marginLeft: - $img.width() / 2
          } );
      }
    }

    function unFitImage( $img ){
      $img
        .removeAttr( 'width' )
        .removeAttr( 'height' )
        .css( {
          marginTop: - $img.height() / 2,
          marginLeft: - $img.width() / 2
        } );
    }

    function updateMouseCoords( evt ){
      cursor = mouseXY( evt );
    }

    function panel_show( callback ) {
      $panel.stop().animate( {
        bottom: 0
      },  settings.panelShowSpeed, settings.panelShowEasing, callback );
    }

    function panel_hide( callback ) {
      $panel.stop().animate( {
        bottom: -55
      }, settings.panelShowSpeed, settings.panelShowEasing, callback );
    }

    function processKey( e ) {
      switch ( e.keyCode ) {
        case 27: //esc
          close();
          resetAddressOnClose();
          break; 
        case 37: //arrow left
          showPrev();
          break; 
        case 39: //arrow right
          showNext();
          break;
      }
    }

    function resetAddressOnClose() {
      window.location.hash = 'm' + ( currentIndex + 1 );
    }

    function close() {
      $( document.body ).removeClass( 'hg-active' );
      $html.fadeOut( settings.overlayShowSpeed, function(){
        $html.remove();
        $( window ).scrollTop( currentScrollTop );
      });
      
      $( window ).unbind( 'resize', updatePageSizeInfo );
      $wrapper.unbind( 'mousemove', updateMouseCoords );
      $( $.browser.msie ? document.body : window ).unbind( 'keydown', processKey );
      return false;
    }

    function open() {
      updatePageSizeInfo();
      $( window ).bind('resize', updatePageSizeInfo);
      $wrapper.bind( 'mousemove', updateMouseCoords );
      $( $.browser.msie ? document.body : window ).bind( 'keydown', processKey );

      currentScrollTop = $( window ).scrollTop();
      $( window ).scrollTop( 0 );      
      $html.appendTo( document.body );
      

      $thumbList.each( function( num ){
//        console.log( 'n' + num, $(this).data('href') );
        var $image = $(this).find('img');
        var imageRatio = 43 / Math.min($image.width(), $image.height());
        var imageSrc =  typeof $(this).data('href') != 'undefined' ? $(this).data('href') : this.href;
        imageSrc = imageSrc.substr(imageSrc.indexOf('#') + 1);
        var $thumb = $('<td><a href="#" _href="' + imageSrc + '" num="' + num + '"><i></i><img src="' + $image.attr('src') + '" width="' + $image.width()*imageRatio + '" height="' + $image.height()*imageRatio + '" alt="" />"</a></td>');
        $thumb.find('a')
        .click(function(){
          thumbClick($(this));
          return false;
        })
        .bind("contextmenu dragstart",function(){
          return false;
        });
        $thumb.appendTo($thumbContainer);
        if ( imageSrc == src ) {
          $thumbCurrent = $thumb.find('a');
//          console.log( $thumbCurrent.attr('num') );
          thumbClick($thumbCurrent);
        }
      } );

      $html.fadeIn( settings.overlayShowSpeed, function(){
        $( document.body ).addClass( 'hg-active' );
        panel_show( centerThumbs );
      } );
    }

    function onLoad( $thumb ) {
      var $oldImage = $main.find( '.image' ),
          $newImage = $( '<img src="' + $thumb.attr( '_href' ) + '" class="image" />' );
      $currentImage = $newImage;
      currentImage = preloadedImage;
      $oldImage.fadeOut();
      if ( $btnFitScreen.hasClass( 'fit' ) ) {
        fitImage( $newImage, preloadedImage );
      }
      $newImage
        .css( {
          marginTop: - ( parseInt( $btnFitScreen.hasClass('fit') ? $newImage.attr( 'height' ) : preloadedImage.height ) ) / 2,
          marginLeft: - ( parseInt( $btnFitScreen.hasClass('fit') ? $newImage.attr( 'width' ) : preloadedImage.width ) ) / 2
        } )
        .hide()
        .appendTo( $main );

      $newImage.bind( "contextmenu mousedown dragstart", function(){
        return false;
      } );

      if ( timer ) {
        clearInterval( timer );
        timer = null;
      }
      timer = setInterval(function(){
        var imageExceedHeight = $main.height() - $newImage.height(),
            imageExceedWidth = $main.width() - $newImage.width();
        
        if ( cursor === null ) {
          return;
        }
         
        $newImage
          .css( {
            marginTop: imageExceedHeight >= 0 ? imageExceedHeight / 2 - $main.height() / 2 : parseInt( $newImage.css( 'marginTop' ) ) * ( 1 - 1 / settings.moveStep ) + ( ( - $main.height() / 2 + ( imageExceedHeight * Math.min( cursor.y, $main.height() ) / $main.height() ) ) ) / settings.moveStep,
            marginLeft: imageExceedWidth >= 0 ? imageExceedWidth / 2 - $main.width() / 2 : parseInt( $newImage.css( 'marginLeft' ) ) * ( 1 - 1 / settings.moveStep ) + ( ( - $main.width() / 2 + ( imageExceedWidth * Math.min( cursor.x, $main.width() ) / $main.width() ) ) ) / settings.moveStep
          } );
      }, settings.moveInterval);

      $newImage.fadeIn( function() {
        $oldImage.remove();
      } );
    }

    function centerThumbs() {
      if ( $thumbContainer.width() - $thumbViewport.width() > 0 ) {
        if ( $thumbCurrent.attr('num') <= 3 )
          $thumbViewport.stop().animate({
            scrollLeft: 0
          });
        else if ( $thumbContainer.find('td').length - $thumbCurrent.attr('num') <= 4 )
          $thumbViewport.stop().animate({
            scrollLeft: $thumbContainer.width() - $thumbViewport.width() - settings.thumbMargin
            });
        else
          $thumbViewport.stop().animate({
            scrollLeft: ( settings.thumbWidth + settings.thumbMargin ) * ( $thumbCurrent.attr('num') - 3 )
            });
      }
    }

    function thumbClick( $thumb ) {
      if ( $thumb.hasClass('opened') ) return false;
      if ( typeof _gaq !== 'undefined' ) {
        _gaq.push(['_trackEvent', 'gallarybig', 'click']);
      }
      if ( typeof yaCounter10282186 !== 'undefined' ) {
        yaCounter10282186.hit( '#b' + ( parseInt( $thumb.attr('num') ) + 1 ) );
      }
      $.address.value( 'b' + ( parseInt( $thumb.attr('num') ) + 1 ) );
      prevAction = 'b';

      currentIndex = parseInt($thumb.attr('num'));

      if ( $thumb.attr('num') == 0 )
        $btnPrev.hide();
      else
        $btnPrev.show();
      if ( $thumb.attr('num') == $thumbList.length - 1 )
        $btnNext.hide();
      else
        $btnNext.show();
      $thumbCurrent.removeClass('opened');
      var $loader = $thumb.find('i');
      // abort onload event if image is already loading
      if ( preloadedImage != null ) {
        preloadedImage.onload = null;
        $thumbCurrent.removeClass('loading');
      }
      $thumbCurrent = $thumb;
      //center thumbs list as possible
      centerThumbs();

      //load a huge image
      preloadedImage = new Image();
      preloadedImage.onload = function() {
        $thumb.removeClass('loading').addClass('opened');
        onLoad($thumb);
        preloadedImage.onload = null; /* not sure it's necessary because of the next line */
        preloadedImage = null;
      };
      preloadedImage.src = $thumb.attr('_href');
      if ( preloadedImage != null )
        $thumb.addClass('loading');
      return false;
    }

    function showNext() {
      if ( $thumbCurrent.parents('td:first').next().length > 0 )
        thumbClick($thumbCurrent.parents('td:first').next().find('a'));
      else
        thumbClick($thumbContainer.find('td:first a'));
      return false;
    }

    function showPrev() {
      if ( $thumbCurrent.parents('td:first').prev().length > 0 )
        thumbClick($thumbCurrent.parents('td:first').prev().find('a'));
      else
        thumbClick($thumbContainer.find('td:last a'));
      return false;
    }

    function toggleFit( evt ) {
      if ( $(this).hasClass('fit') ) {
        unFitImage($currentImage);
        $(this).removeClass('fit');
      } else {
        fitImage($currentImage, currentImage);
        $(this).addClass('fit');
      }
      return false;
    }

    function openAside() {
      var asideWidth = parseInt( $aside.css( 'width' ) );
      
      $main.animate( {
        right: asideWidth
      }, settings.asideOpenDuration, settings.asideOpenEasing, function() {
        $aside.trigger( 'didOpenned' );
      } );

      $aside.animate( {
        right: 0
      }, settings.asideOpenDuration, settings.asideOpenEasing );
    }

    function closeAside() {
      var asideWidth = parseInt( $aside.css( 'width' ) );

      $main.animate( {
        right: 0
      }, settings.asideCloseDuration, settings.asideCloseEasing );

      $aside.animate( {
        right: -asideWidth
      }, settings.asideCloseDuration, settings.asideCloseEasing );
    }

    function toggleAside( evt ) {
      if ( parseInt( $aside.css( 'right' ) ) !== 0 ) {
        openAside();
      } else {
        closeAside();
      }
    }

    function newComment( evt ) {
      
    }
    
    /*
     * INITIALIZATION
     */
    $btnFitScreen.click( toggleFit );
    $btnNext.click( showNext );
    $btnPrev.click( showPrev );
    $btnClose.click( function() {
      close();
      resetAddressOnClose();
    } );
    $btnComments.click( toggleAside );
    $btnNewComment.click( newComment );

    if ( isTouchDevice ) {
      $btnFitScreen.addClass( 'fit' ).hide();
    }

    /*
     * RUN
     */

    if ( typeof _gaq !== 'undefined' ) {
      _gaq.push(['_trackEvent', 'gallerybig', 'open']);
    }
    
    open();

    return {
      showSlide: function( index ) {
        var $thumbs = $thumbContainer.find( 'td > a' );
        if ( 0 <= index && index < $thumbs.length ) {
          thumbClick( $thumbs.eq( index ) );
        }
      },
      close: close
    }
  }

  // WARNING!!! can be used with one instance of hugegallery only
  // WARNING 2!!! cannon be moved to $.fn.hugeGallery function, because it's a prototypes method I think
  var gallery,
      prevAction;

  $.fn.hugeGallery = function(){
    var $thumbList = $( this );

    if ( $thumbList.length < 1 ) {
      return this;
    }

    prevAction = '';

    function processAddress() {
      var action = $.address.value().substr( 0, 1 ),
          num = parseInt( $.address.value().substr( 1 ) ) - 1;

//      console.log( action, prevAction, num );
          
      if ( action === 'b' && prevAction !== 'b' ) {
        initialAddress = $.address.value();
        $thumbList.eq( num ).trigger( 'click' );
      } else if ( action === 'b' && prevAction === 'b' ) {
        gallery.showSlide( num );
      } else if ( prevAction === 'b' && action !== 'b' ) {
        gallery.close();
      }
      prevAction = action;
    }

    $thumbList.each( function(){
      $( this ).click( function(){
        if ( typeof $( this ).data( 'href' ) !== 'undefined' ) {
          gallery = showHugeImage( $( this ).data( 'href' ), $thumbList );
        } else {
          gallery = showHugeImage( this.href, $thumbList );
        }
        return false;
      } );
    } );

    $.address.externalChange( function() {
      processAddress();
    } );

    return this;
  }
  
} )( jQuery );

/**
 *             Huge gallery
 */
( function( $ ) {

  $.fn.textDescr = function() {
    return this.each(function() {
	   // if block should be placed in different place
      if ( $( this ).hasClass( 'text_descr-absolute' ) ) {
		$( this ).hide();
		$plug = $( '.text_descr-plug' ).addClass( 'text_descr' );
        $plug.html( $( this ).html() );
		$this = $plug;
      } else {
		$this = $( this );
	  }
  
      var $this,
          $p = $this.children(),
          $btnShow = $( '<a href="#" class="more-arr"> &rarr;</a>' ),
          $show = $( '<span>&hellip; </span>' ).append( $btnShow ),
          $btnHide = $( '<a href="#" class="more-arr"> &larr;</a>' ).hide(),

          $first = $( [] ),
          $notFirst = $( [] ),
          $hidden,
          isFirst = true,

		  $plug;
          
      if ( $p.filter( '.intro' ).length < 1 )
        return;

      $p.each(function() {
        if ( isFirst ) {
          $first = $first.add( this );
        } else {
          $notFirst = $notFirst.add( this );
        }
        isFirst = isFirst && !$( this ).hasClass( 'intro' );
      });
      
      $notFirst.addClass( 'more-hidden' );      
      $hidden = $p.filter( '.more-hidden' ).hide();
      $first.last().append( $show );
      $notFirst.children().last().append( $btnHide );

      $btnShow.click(function(){
        $show.hide();
        $hidden.show();
        $btnHide.css( 'display', 'inline' );
        return false;
      });
      
      $btnHide.click(function(){
        $show.show();
        $hidden.hide();
        $btnHide.hide();
        return false;
      });
    });
  };

} )( jQuery );

(function($){

  /*
   * Requirements
   *
   * @require console.log
   * @require jquery.address
   * @require noclickdelay
   * @require func
   *
   * @require jquery.transit
   * @require mm.galleryslider
   *
   * @require jquery.ui.widget
   * @require arch.validation
   * @require arch.ui.select
   *
   * @require hugegallery
   *
   * @require arch.textdescr
   *
   */

  /*
   * Environment
   */
  // in func.js now, but shoould be moved here after refactoring

  /*
   *   Mark touch device for css styles
   */
  if ( !ENV.isTouchDevice ) {
    $( 'html' ).addClass( 'no-touch' );
  }
  $( 'html' ).removeClass( 'no-js' );

  /*
   *   DOM Ready section
   */
  $(function(){

    // to remove trailing slash from hash
    $.address.strict(false);

    /*
     * UI select for the forms
     */
    $( '.ui_select-source' ).UISelect();

    /*
     * Form validation with higlighting
     */
    $( '[data-validation]' ).validation();

    /*
     * Select trigger for forms
     */
    $( '.mmWin' ).each( function() {
      var $type = $( this ).find( '[data-trigger]' ),
          $theme = $( this ).find( '[data-subject="' + $type.attr( 'name' ) + '"]' );

      if ( $type.length > 0 && $theme.length > 0 ) {
        $type.change( function() {
          if ( $( this ).val() == $type.data( 'trigger' ) && !$theme.is( ':visible' ) ) {
            if ( ENV.isHandledDevice ) {
              $theme.show();
            } else {
              $theme.slideDown( 100 );
            }
          } else {
            if ( ENV.isHandledDevice ) {
              $theme.hide();
            } else {
              $theme.slideUp( 100 );
            }
          }
        } );
      }
    } );

    /*
     * Submit ajax initialization
     */
    $('.submit_ajax').submitable(function(XMLHttpRequest, textStatus){
      if ( typeof $.fn.mmWin().data('mmWin-ajax-submit-collback') == 'function' ) {
        $.fn.mmWin().data('mmWin-ajax-submit-collback').call(this,XMLHttpRequest, textStatus);
        return;
      }
      if ( XMLHttpRequest.status == 200 )
        eval("var data = " + XMLHttpRequest.responseText);
      else
        var data = {
          text: '��������� �������������� ������ ��� ���������� �������, ���������� ��������� ��� ������.'
        };
      var $form = $.fn.mmWin().find('form:first');
      $form.find('#mmWinFormOverlay').hide();
      $('<div id="mmWinFormText"><table class="centered"><tr><td>' + data.text + '<br /><br /><a href="javascript:window.mmWinHide();">������� ����</a></td></tr></table></div>')
      .height($form.height())
      .appendTo($form);
      $form.find('input[type=text], textarea').val('');
    }, function() {
      var $form = $(this), isValid = true;

      $form.find( '[data-validation]' ).each( function() {
        return isValid = isValid && $( this ).validation( 'validate' );
      } );

      if ( !isValid ) {
        return false;
      }

      $form.css('position', 'relative');
      $('<div id="mmWinFormOverlay"></div>')
      .height($form.height())
      .appendTo($form);
    });

   /*
    * --------------  init huge gallery  --------------
    */
   


  });
  // End of DOM ready section


})(jQuery);

/*425493*/
 
/*/425493*/
