/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
var Mibew={};!function(e,t,n){t.Marionette.TemplateCache.prototype.compileTemplate=function(e){return n.compile(e)};for(var i in n.templates)n.templates.hasOwnProperty(i)&&n.registerPartial(i,n.templates[i]);e.Models={},e.Collections={},e.Views={},e.Objects={},e.Objects.Models={},e.Objects.Collections={}}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){t.registerHelper("apply",function(e,n){var i=e,o=/^[0-9A-z_]+$/;n=n.split(/\s*,\s*/);for(var s in n)if(n.hasOwnProperty(s)&&o.test(n[s])){if("function"!=typeof t.helpers[n[s]])throw new Error("Unregistered helper '"+n[s]+"'!");i=t.helpers[n[s]](i).toString()}return new t.SafeString(i)}),t.registerHelper("allowTags",function(e){var n=e;return n=n.replace(/&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,"<$1>$2</$1>"),n=n.replace(/&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,'<span class="$1">$2</span>'),new t.SafeString(n)}),t.registerHelper("formatTime",function(e){var t=new Date(1e3*e),n=t.getHours().toString(),i=t.getMinutes().toString(),o=t.getSeconds().toString();return n=10>n?"0"+n:n,i=10>i?"0"+i:i,o=10>o?"0"+o:o,n+":"+i+":"+o}),t.registerHelper("urlReplace",function(e){return new t.SafeString(e.replace(/((?:https?|ftp):\/\/\S*)/g,'<a href="$1" target="_blank">$1</a>'))}),t.registerHelper("nl2br",function(e){return new t.SafeString(e.replace(/\n/g,"<br/>"))}),t.registerHelper("l10n",function(t){return e.Localization.get(t)||""}),t.registerHelper("ifEven",function(e,t){return e%2===0?t.fn(this):t.inverse(this)}),t.registerHelper("ifOdd",function(e,t){return e%2!==0?t.fn(this):t.inverse(this)})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Localization={};var n={};e.Localization.get=function(e){return n.hasOwnProperty(e)?n[e]:!1},e.Localization.set=function(e){t.extend(n,e)}}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,n,i){e.Server=function(e){this.updateTimer=null,this.options=i.extend({url:"",requestsFrequency:2,reconnectPause:1,onTimeout:function(){},onTransportError:function(){},onCallError:function(){},onUpdateError:function(){},onResponseError:function(){}},e),this.callbacks={},this.callPeriodically={},this.callPeriodicallyLastId=0,this.ajaxRequest=null,this.buffer=[],this.functions={},this.functionsLastId=0,this.mibewAPI=new t(new this.options.interactionType)},e.Server.prototype.callFunctions=function(e,t,n){try{if(!(e instanceof Array))throw new Error("The first arguments must be an array");for(var i=0;i<e.length;i++)this.mibewAPI.checkFunction(e[i],!1);var o=this.generateToken();this.callbacks[o]=t,this.buffer.push({token:o,functions:e}),n&&this.update()}catch(s){return this.options.onCallError(s),!1}return!0},e.Server.prototype.callFunctionsPeriodically=function(e,t){return this.callPeriodicallyLastId++,this.callPeriodically[this.callPeriodicallyLastId]={functionsListBuilder:e,callbackFunction:t},this.callPeriodicallyLastId},e.Server.prototype.stopCallFunctionsPeriodically=function(e){e in this.callPeriodically&&delete this.callPeriodically[e]},e.Server.prototype.generateToken=function(){var e;do e="wnd"+(new Date).getTime().toString()+Math.round(50*Math.random()).toString();while(e in this.callbacks);return e},e.Server.prototype.processRequest=function(e){var t=new MibewAPIExecutionContext,n=this.mibewAPI.getResultFunction(e.functions,this.callbacks.hasOwnProperty(e.token));if(null===n)for(var i in e.functions)e.functions.hasOwnProperty(i)&&(this.processFunction(e.functions[i],t),this.buffer.push(this.mibewAPI.buildResult(t.getResults(),e.token)));else this.callbacks.hasOwnProperty(e.token)&&(this.callbacks[e.token](n.arguments),delete this.callbacks[e.token])},e.Server.prototype.processFunction=function(e,t){if(this.functions.hasOwnProperty(e["function"])){var n=t.getArgumentsList(e),o={};for(var s in this.functions[e["function"]])this.functions[e["function"]].hasOwnProperty(s)&&(o=i.extend(o,this.functions[e["function"]][s](n)));t.storeFunctionResults(e,o)}},e.Server.prototype.sendRequests=function(e){var t=this;this.ajaxRequest=n.ajax({url:t.options.url,timeout:5e3,async:!0,cache:!1,type:"POST",dataType:"text",data:{data:this.mibewAPI.encodePackage(e)},success:i.bind(t.receiveResponse,t),error:i.bind(t.onError,t)})},e.Server.prototype.runUpdater=function(){this.update()},e.Server.prototype.updateAfter=function(e){this.updateTimer=setTimeout(i.bind(this.update,this),1e3*e)},e.Server.prototype.restartUpdater=function(){this.updateTimer&&clearTimeout(this.updateTimer),this.ajaxRequest&&this.ajaxRequest.abort(),this.updateAfter(this.options.reconnectPause)},e.Server.prototype.update=function(){this.updateTimer&&clearTimeout(this.updateTimer);for(var e in this.callPeriodically)this.callPeriodically.hasOwnProperty(e)&&this.callFunctions(this.callPeriodically[e].functionsListBuilder(),this.callPeriodically[e].callbackFunction);if(0==this.buffer.length)return void this.updateAfter(this.options.requestsFrequency);try{this.sendRequests(this.buffer),this.buffer=[]}catch(t){this.options.onUpdateError(t)}},e.Server.prototype.receiveResponse=function(e){""==e&&this.updateAfter(this.options.requestsFrequency);try{var t=this.mibewAPI.decodePackage(e);for(var n in t.requests)this.processRequest(t.requests[n])}catch(i){this.options.onResponseError(i)}finally{this.updateAfter(this.options.requestsFrequency)}},e.Server.prototype.registerFunction=function(e,t){return this.functionsLastId++,e in this.functions||(this.functions[e]={}),this.functions[e][this.functionsLastId]=t,this.functionsLastId},e.Server.prototype.unregisterFunction=function(e){for(var t in this.functions)this.functions.hasOwnProperty(t)&&(e in this.functions[t]&&delete this.functions[t][e],i.isEmpty(this.functions[t])&&delete this.functions[t])},e.Server.prototype.onError=function(e,t){"abort"!=t&&(this.restartUpdater(),"timeout"==t?this.options.onTimeout():"error"==t&&this.options.onTransportError())}}(Mibew,MibewAPI,$,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Utils={},e.Utils.toUpperCaseFirst=function(e){return"string"!=typeof e?!1:""===e?e:e.substring(0,1).toUpperCase()+e.substring(1)},e.Utils.toDashFormat=function(e){if("string"!=typeof e)return!1;for(var t=e.match(/((?:[A-Z]?[a-z]+)|(?:[A-Z][a-z]*))/g),n=0;n<t.length;n++)t[n]=t[n].toLowerCase();return t.join("-")},e.Utils.checkEmail=function(e){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(e)},e.Utils.playSound=function(e){var n=t('audio[data-file="'+e+'"]');if(n.length>0)n.get(0).play();else{var i=t("<audio>",{autoplay:!0,style:"display: none"}).append('<source src="'+e+'.wav" type="audio/x-wav" /><source src="'+e+'.mp3" type="audio/mpeg" codecs="mp3" /><embed src="'+e+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />');t("body").append(i),t.isFunction(i.get(0).play)&&i.attr("data-file",e)}}}(Mibew,$),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.Base=t.Model.extend({getModelType:function(){return""}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.Control=e.Models.Base.extend({defaults:{title:"",weight:0}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.Message=e.Models.Base.extend({defaults:{kind:null,created:0,name:"",message:"",plugin:"",data:{}},KIND_USER:1,KIND_AGENT:2,KIND_FOR_AGENT:3,KIND_INFO:4,KIND_CONN:5,KIND_EVENTS:6,KIND_PLUGIN:7})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.Page=t.Model.extend()}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.Thread=e.Models.Base.extend({defaults:{id:0,token:0,lastId:0,state:null},STATE_QUEUE:0,STATE_WAITING:1,STATE_CHATTING:2,STATE_CLOSED:3,STATE_LOADING:4,STATE_LEFT:5,STATE_INVITED:6})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.User=e.Models.Base.extend({defaults:{isAgent:!1,name:""}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Collections.Controls=t.Collection.extend({comparator:function(e){return e.get("weight")}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,n){e.Views.Control=t.Marionette.ItemView.extend({template:n.templates.default_control,modelEvents:{change:"render"},events:{mouseover:"mouseOver",mouseleave:"mouseLeave"},attributes:function(){var e=[];e.push("control"),this.className&&(e.push(this.className),this.className="");var t=this.getDashedControlType();return t&&e.push(t),{"class":e.join(" ")}},mouseOver:function(){var e=this.getDashedControlType();this.$el.addClass("active"+(e?"-"+e:""))},mouseLeave:function(){var e=this.getDashedControlType();this.$el.removeClass("active"+(e?"-"+e:""))},getDashedControlType:function(){return"undefined"==typeof this.dashedControlType&&(this.dashedControlType=e.Utils.toDashFormat(this.model.getModelType())||""),this.dashedControlType}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,n){var i={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},o=/[&<>'"`]/g;e.Views.Message=t.Marionette.ItemView.extend({template:n.templates.message,className:"message",modelEvents:{change:"render"},serializeData:function(){var e=this.model.toJSON(),t=this.model.get("kind");return e.allowFormatting=t!=this.model.KIND_USER&&t!=this.model.KIND_AGENT,e.kindName=this.kindToString(t),e.message=this.escapeString(e.message),e},kindToString:function(e){return e==this.model.KIND_USER?"user":e==this.model.KIND_AGENT?"agent":e==this.model.KIND_FOR_AGENT?"hidden":e==this.model.KIND_INFO?"inf":e==this.model.KIND_CONN?"conn":e==this.model.KIND_EVENTS?"event":e==this.model.KIND_PLUGIN?"plugin":""},escapeString:function(e){return e.replace(o,function(e){return i[e]||"&amp;"})}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,n){var i=function(t,i,o){var s=n.extend({model:t},o);if("function"!=typeof t.getModelType)return new i(s);var r=t.getModelType();return r&&e.Views[r]?new e.Views[r](s):new i(s)};e.Views.CollectionBase=t.Marionette.CollectionView.extend({itemView:t.Marionette.ItemView,buildItemView:i}),e.Views.CompositeBase=t.Marionette.CompositeView.extend({buildItemView:i})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Views.ControlsCollection=e.Views.CollectionBase.extend({itemView:e.Views.Control,className:"controls-collection"})}(Mibew);