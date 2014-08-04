/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){e.Regions={},e.Layouts={},e.Application=new Backbone.Marionette.Application}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
MibewAPIChatInteraction=function(){this.mandatoryArguments=function(){return{"*":{threadId:null,token:null,"return":{},references:{}},result:{errorCode:0}}},this.getReservedFunctionsNames=function(){return["result"]}},MibewAPIChatInteraction.prototype=new MibewAPIInteraction,/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.BaseSoundManager=t.Model.extend({defaults:{enabled:!0},play:function(t){this.get("enabled")&&e.Utils.playSound(t)}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.Status=e.Models.Base.extend({defaults:{visible:!0,weight:0,hideTimeout:4e3,title:""},initialize:function(){this.hideTimer=null},autoHide:function(e){var s=e||this.get("hideTimeout");this.hideTimer&&clearTimeout(this.hideTimer),this.hideTimer=setTimeout(t.bind(function(){this.set({visible:!1})},this),s)}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.BaseSurveyForm=e.Models.Base.extend({defaults:{name:"",email:"",message:"",info:"",referrer:"",groupId:null,groups:null}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.Avatar=e.Models.Base.extend({defaults:{imageLink:!1},initialize:function(){this.registeredFunctions=[],this.registeredFunctions.push(e.Objects.server.registerFunction("setupAvatar",t.bind(this.apiSetupAvatar,this)))},finalize:function(){for(var t=0;t<this.registeredFunctions.length;t++)e.Objects.server.unregisterFunction(this.registeredFunctions[t])},apiSetupAvatar:function(e){e.imageLink&&this.set({imageLink:e.imageLink})}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.ChatUser=e.Models.User.extend({defaults:t.extend({},e.Models.User.prototype.defaults,{canPost:!0,typing:!1,canChangeName:!1,dafaultName:!0})})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.CloseControl=e.Models.Control.extend({getModelType:function(){return"CloseControl"},closeThread:function(){var t=e.Objects.Models.thread,s=e.Objects.Models.user;e.Objects.server.callFunctions([{"function":"close",arguments:{references:{},"return":{closed:"closed"},threadId:t.get("id"),token:t.get("token"),lastId:t.get("lastId"),user:!s.get("isAgent")}}],function(t){t.closed?window.close():e.Objects.Models.Status.message.setMessage(t.errorMessage||"Cannot close")},!0)}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.HistoryControl=e.Models.Control.extend({defaults:t.extend({},e.Models.Control.prototype.defaults,{link:!1,windowParams:""}),getModelType:function(){return"HistoryControl"}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.RedirectControl=e.Models.Control.extend({defaults:t.extend({},e.Models.Control.prototype.defaults,{link:!1}),getModelType:function(){return"RedirectControl"}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.RefreshControl=e.Models.Control.extend({getModelType:function(){return"RefreshControl"},refresh:function(){e.Objects.server.restartUpdater()}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.SecureModeControl=e.Models.Control.extend({defaults:t.extend({},e.Models.Control.prototype.defaults,{link:!1}),getModelType:function(){return"SecureModeControl"}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.SendMailControl=e.Models.Control.extend({defaults:t.extend({},e.Models.Control.prototype.defaults,{link:!1,windowParams:""}),getModelType:function(){return"SendMailControl"}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.SoundControl=e.Models.Control.extend({defaults:t.extend({},e.Models.Control.prototype.defaults,{enabled:!0}),toggle:function(){var t=!this.get("enabled");e.Objects.Models.soundManager.set({enabled:t}),this.set({enabled:t})},getModelType:function(){return"SoundControl"}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.UserNameControl=e.Models.Control.extend({getModelType:function(){return"UserNameControl"},changeName:function(t){var s=e.Objects.Models.user,o=e.Objects.Models.thread,n=s.get("name");t&&n!=t&&(e.Objects.server.callFunctions([{"function":"rename",arguments:{references:{},"return":{},threadId:o.get("id"),token:o.get("token"),name:t}}],function(t){t.errorCode&&(e.Objects.Models.Status.message.setMessage(t.errorMessage||"Cannot rename"),s.set({name:n}))},!0),s.set({name:t}))}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){var s=e.Models.BaseSurveyForm;e.Models.LeaveMessageForm=s.extend({defaults:t.extend({},s.prototype.defaults,{showCaptcha:!1,captcha:""}),validate:function(t){var s=e.Localization;if("undefined"!=typeof t.email){if(!t.email)return s.get("leavemessage.error.email.required");if(!e.Utils.checkEmail(t.email))return s.get("leavemessage.error.wrong.email")}return"undefined"==typeof t.name||t.name?"undefined"==typeof t.message||t.message?this.get("showCaptcha")&&"undefined"!=typeof t.captcha&&!t.captcha?s.get("The letters you typed don't match the letters that were shown in the picture."):void 0:s.get("leavemessage.error.message.required"):s.get("leavemessage.error.name.required")},submit:function(){if(!this.validate(this.attributes)){var t=this;e.Objects.server.callFunctions([{"function":"processLeaveMessage",arguments:{references:{},"return":{},groupId:t.get("groupId"),name:t.get("name"),info:t.get("info"),email:t.get("email"),message:t.get("message"),referrer:t.get("referrer"),captcha:t.get("captcha"),threadId:null,token:null}}],function(e){0==e.errorCode?t.trigger("submit:complete",t):t.trigger("submit:error",t,{code:e.errorCode,message:e.errorMessage||""})},!0)}},ERROR_WRONG_CAPTCHA:10})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Models.MessageForm=e.Models.Base.extend({defaults:{predefinedAnswers:[],ignoreCtrl:!1},postMessage:function(t){var s=e.Objects.Models.thread,o=e.Objects.Models.user;if(o.get("canPost")){this.trigger("before:post",this);var n=this;e.Objects.server.callFunctions([{"function":"post",arguments:{references:{},"return":{},message:t,threadId:s.get("id"),token:s.get("token"),user:!o.get("isAgent")}}],function(){n.trigger("after:post",n)},!0)}}})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.ChatSoundManager=e.Models.BaseSoundManager.extend({defaults:t.extend({},e.Models.BaseSoundManager.prototype.defaults,{skipNextMessageSound:!1}),initialize:function(){var t=e.Objects,s=this;t.Collections.messages.on("multiple:add",this.playNewMessageSound,this),t.Models.messageForm.on("before:post",function(){s.set({skipNextMessageSound:!0})})},playNewMessageSound:function(){if(!this.get("skipNextMessageSound")){var t=e.Objects.Models.page.get("mibewRoot");"undefined"!=typeof t&&(t+="/sounds/new_message",this.play(t))}this.set({skipNextMessageSound:!1})}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.StatusMessage=e.Models.Status.extend({defaults:t.extend({},e.Models.Status.prototype.defaults,{message:"",visible:!1}),getModelType:function(){return"StatusMessage"},setMessage:function(e){this.set({message:e,visible:!0}),this.autoHide()}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Models.StatusTyping=e.Models.Status.extend({defaults:t.extend({},e.Models.Status.prototype.defaults,{visible:!1,hideTimeout:2e3}),getModelType:function(){return"StatusTyping"},show:function(){this.set({visible:!0}),this.autoHide()}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){var s=e.Models.BaseSurveyForm;e.Models.SurveyForm=s.extend({defaults:t.extend({},s.prototype.defaults,{showEmail:!1,showMessage:!1,canChangeName:!1}),validate:function(t){return this.get("showEmail")&&"undefined"!=typeof t.email&&!e.Utils.checkEmail(t.email)?e.Localization.get("Wrong email address."):void 0},submit:function(){if(!this.validate(this.attributes)){var t=this;e.Objects.server.callFunctions([{"function":"processSurvey",arguments:{references:{},"return":{next:"next",options:"options"},groupId:t.get("groupId"),name:t.get("name"),info:t.get("info"),email:t.get("email"),message:t.get("message"),referrer:t.get("referrer"),threadId:null,token:null}}],function(s){if(0==s.errorCode)switch(t.trigger("submit:complete",t),e.Application.Survey.stop(),s.next){case"chat":e.Application.Chat.start(s.options);break;case"leaveMessage":e.Application.LeaveMessage.start(s.options);break;default:throw new Error("Do not know how to continue!")}else t.trigger("submit:error",t,{code:s.errorCode,message:s.errorMessage||""})},!0)}}})}(Mibew,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Collections.Messages=t.Collection.extend({model:e.Models.Message,initialize:function(){this.periodicallyCalled=[],this.periodicallyCalled.push(e.Objects.server.callFunctionsPeriodically(s.bind(this.updateMessagesFunctionBuilder,this),s.bind(this.updateMessages,this)))},finalize:function(){for(var t=0;t<this.periodicallyCalled.length;t++)e.Objects.server.stopCallFunctionsPeriodically(this.periodicallyCalled[t])},updateMessages:function(t){t.lastId&&e.Objects.Models.thread.set({lastId:t.lastId});for(var s,o,n,a,i=e.Models.Message.prototype.KIND_PLUGIN,r=[],l=0,d=t.messages.length;d>l;l++)s=t.messages[l],s.kind==i?"object"==typeof s.data&&null!==s.data&&(o=s.plugin||!1,n="process:"+(o!==!1?o+":":"")+"plugin:message",a={messageData:s,model:!1},this.trigger(n,a),a.model&&(a.model.get("id")||a.model.set({id:s.id}),r.push(a.model))):r.push(new e.Models.Message(s));r.length>0&&this.add(r)},updateMessagesFunctionBuilder:function(){var t=e.Objects.Models.thread,s=e.Objects.Models.user;return[{"function":"updateMessages",arguments:{"return":{messages:"messages",lastId:"lastId"},references:{},threadId:t.get("id"),token:t.get("token"),lastId:t.get("lastId"),user:!s.get("isAgent")}}]},add:function(){var e=Array.prototype.slice.apply(arguments),s=t.Collection.prototype.add.apply(this,e);return this.trigger("multiple:add"),s}})}(Mibew,Backbone,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Collections.Status=t.Collection.extend({comparator:function(e){return e.get("weight")}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.Status=t.Marionette.ItemView.extend({template:s.templates["chat/status/base"],className:"status",modelEvents:{change:"render"},onBeforeRender:function(){this.model.get("visible")?this.$el.show():this.$el.hide()}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.BaseSurveyForm=t.Marionette.ItemView.extend({events:{'change select[name="group"]':"changeGroupDescription","submit form":"preventSubmit"},ui:{groupSelect:'select[name="group"]',groupDescription:"#groupDescription",name:'input[name="name"]',email:'input[name="email"]',message:'textarea[name="message"]',errors:".errors",ajaxLoader:"#ajax-loader"},modelEvents:{invalid:"hideAjaxLoader showError","submit:error":"hideAjaxLoader showError"},preventSubmit:function(e){e.preventDefault()},changeGroupDescription:function(){var e=this.ui.groupSelect.prop("selectedIndex"),t=this.model.get("groups")[e].description||"";this.ui.groupDescription.text(t)},showError:function(e,t){var s;s="string"==typeof t?t:t.message,this.ui.errors.html(s)},serializeData:function(){var t=this.model.toJSON();return t.page=e.Objects.Models.page.toJSON(),t},showAjaxLoader:function(){this.ui.ajaxLoader.show()},hideAjaxLoader:function(){this.ui.ajaxLoader.hide()}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.Avatar=t.Marionette.ItemView.extend({template:s.templates["chat/avatar"],className:"avatar",modelEvents:{change:"render"}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.CloseControl=e.Views.Control.extend({template:t.templates["chat/controls/close"],events:s.extend({},e.Views.Control.prototype.events,{click:"closeThread"}),closeThread:function(){var t=e.Localization.get("Are you sure want to leave chat?");(t===!1||confirm(t))&&this.model.closeThread()}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.HistoryControl=e.Views.Control.extend({template:t.templates["chat/controls/history"],events:s.extend({},e.Views.Control.prototype.events,{click:"showHistory"}),showHistory:function(){var t=e.Objects.Models.user,s=this.model.get("link");if(t.get("isAgent")&&s){var o=this.model.get("windowParams");s=s.replace("&amp;","&","g");var n=window.open(s,"UserHistory",o);null!==n&&(n.focus(),n.opener=window)}}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.RedirectControl=e.Views.Control.extend({template:t.templates["chat/controls/redirect"],events:s.extend({},e.Views.Control.prototype.events,{click:"redirect"}),initialize:function(){e.Objects.Models.user.on("change",this.render,this)},serializeData:function(){var t=this.model.toJSON();return t.user=e.Objects.Models.user.toJSON(),t},redirect:function(){var t=e.Objects.Models.user;if(t.get("isAgent")&&t.get("canPost")){var s=this.model.get("link");if(s){var o=e.Objects.Models.page.get("style"),n="";o&&(n=(-1===s.indexOf("?")?"?":"&")+"style="+o),window.location.href=s.replace(/\&amp\;/g,"&")+n}}}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.RefreshControl=e.Views.Control.extend({template:t.templates["chat/controls/refresh"],events:s.extend({},e.Views.Control.prototype.events,{click:"refresh"}),refresh:function(){this.model.refresh()}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.SecureModeControl=e.Views.Control.extend({template:t.templates["chat/controls/secure_mode"],events:s.extend({},e.Views.Control.prototype.events,{click:"secure"}),secure:function(){var t=this.model.get("link");if(t){var s=e.Objects.Models.page.get("style");window.location.href=t.replace(/\&amp\;/g,"&")+(s?"&style="+s:"")}}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.SendMailControl=e.Views.Control.extend({template:t.templates["chat/controls/send_mail"],events:s.extend({},e.Views.Control.prototype.events,{click:"sendMail"}),sendMail:function(){var t=this.model.get("link"),s=e.Objects.Models.page;if(t){var o=this.model.get("windowParams"),n=s.get("style"),a="";n&&(a=(-1===t.indexOf("?")?"?":"&")+"style="+n),t=t.replace(/\&amp\;/g,"&")+a;var i=window.open(t,"ForwardMail",o);null!==i&&(i.focus(),i.opener=window)}}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.SoundControl=e.Views.Control.extend({template:t.templates["chat/controls/sound"],events:s.extend({},e.Views.Control.prototype.events,{click:"toggle"}),toggle:function(){this.model.toggle()}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.UserNameControl=e.Views.Control.extend({template:t.templates["chat/controls/user_name"],events:s.extend({},e.Views.Control.prototype.events,{"click .user-name-control-set":"changeName","click .user-name-control-change":"showNameInput","keydown #user-name-control-input":"inputKeyDown"}),ui:{nameInput:"#user-name-control-input"},initialize:function(){e.Objects.Models.user.on("change:name",this.hideNameInput,this),this.nameInput=e.Objects.Models.user.get("defaultName")},serializeData:function(){var t=this.model.toJSON();return t.user=e.Objects.Models.user.toJSON(),t.nameInput=this.nameInput,t},inputKeyDown:function(e){var t=e.which;(13==t||10==t)&&this.changeName()},hideNameInput:function(){this.nameInput=!1,this.render()},showNameInput:function(){this.nameInput=!0,this.render()},changeName:function(){var e=this.ui.nameInput.val();this.model.changeName(e)}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.LeaveMessageDescription=t.Marionette.ItemView.extend({template:s.templates["leave_message/description"],serializeData:function(){return{page:e.Objects.Models.page.toJSON()}}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){var o=e.Views.BaseSurveyForm;e.Views.LeaveMessageForm=o.extend({template:t.templates["leave_message/form"],events:s.extend({},o.prototype.events,{"click #send-message":"submitForm"}),ui:s.extend({},o.prototype.ui,{captcha:'input[name="captcha"]',captchaImg:"#captcha-img"}),modelEvents:s.extend({},o.prototype.modelEvents,{"submit:error":"hideAjaxLoader showError submitError"}),submitForm:function(){this.showAjaxLoader();var e={};this.model.get("groups")&&(e.groupId=this.ui.groupSelect.val()),e.name=this.ui.name.val()||"",e.email=this.ui.email.val()||"",e.message=this.ui.message.val()||"",this.model.get("showCaptcha")&&(e.captcha=this.ui.captcha.val()||""),this.model.set(e,{validate:!0}),this.model.submit()},submitError:function(e,t){if(t.code==e.ERROR_WRONG_CAPTCHA&&e.get("showCaptcha")){var s=this.ui.captchaImg.attr("src");s=s.replace(/\?d\=[0-9]+/,""),this.ui.captchaImg.attr("src",s+"?d="+(new Date).getTime())}}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.LeaveMessageSentDescription=t.Marionette.ItemView.extend({template:s.templates["leave_message/sent_description"],serializeData:function(){return{page:e.Objects.Models.page.toJSON()}}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.Message=e.Views.Message.extend({template:t.templates["chat/message"]})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){e.Views.MessageForm=t.Marionette.ItemView.extend({template:s.templates["chat/message_form"],events:{"click #send-message":"postMessage","keydown #message-input":"messageKeyDown","keyup #message-input":"checkUserTyping","change #message-input":"checkUserTyping","change #predefined":"selectPredefinedAnswer","focus #message-input":"setFocus","blur #message-input":"dropFocus"},modelEvents:{change:"render"},ui:{message:"#message-input",send:"#send-message",predefinedAnswer:"#predefined"},initialize:function(){e.Objects.Models.user.on("change:canPost",this.render,this)},serializeData:function(){var t=this.model.toJSON();return t.user=e.Objects.Models.user.toJSON(),t},postMessage:function(){var t=this.ui.message.val();""!=t&&(this.disableInput(),this.model.postMessage(t)),e.Objects.Collections.messages.on("multiple:add",this.postMessageComplete,this)},messageKeyDown:function(e){var t=e.which,s=e.ctrlKey;(13==t&&(s||this.model.get("ignoreCtrl"))||10==t)&&this.postMessage()},enableInput:function(){this.ui.message.removeAttr("disabled")},disableInput:function(){this.ui.message.attr("disabled","disabled")},clearInput:function(){this.ui.message.val("").change()},postMessageComplete:function(){this.clearInput(),this.enableInput(),this.focused&&this.ui.focus(),e.Objects.Collections.messages.off("multiple:add",this.postMessageComplete,this)},selectPredefinedAnswer:function(){var e=this.ui.message,t=this.ui.predefinedAnswer,s=t.get(0).selectedIndex;s&&(e.val(this.model.get("predefinedAnswers")[s-1].full).change(),e.focus(),t.get(0).selectedIndex=0)},checkUserTyping:function(){var t=e.Objects.Models.user,s=""!=this.ui.message.val();s!=t.get("typing")&&t.set({typing:s})},setFocus:function(){this.focused=!0},dropFocus:function(){this.focused=!1}})}(Mibew,Backbone,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.StatusMessage=e.Views.Status.extend({template:t.templates["chat/status/message"]})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Views.StatusTyping=e.Views.Status.extend({template:t.templates["chat/status/typing"]})}(Mibew,Handlebars),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t,s){var o=e.Views.BaseSurveyForm;e.Views.SurveyForm=o.extend({template:t.templates["survey/form"],events:s.extend({},o.prototype.events,{"click #submit-survey":"submitForm"}),submitForm:function(){this.showAjaxLoader();var e={};this.model.get("groups")&&(e.groupId=this.ui.groupSelect.val()),this.model.get("canChangeName")&&(e.name=this.ui.name.val()||""),this.model.get("showEmail")&&(e.email=this.ui.email.val()||""),this.model.get("showMessage")&&(e.message=this.ui.message.val()||""),this.model.set(e,{validate:!0}),this.model.submit()}})}(Mibew,Handlebars,_),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Views.MessagesCollection=e.Views.CollectionBase.extend({itemView:e.Views.Message,className:"messages-collection"})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Views.StatusCollection=e.Views.CollectionBase.extend({itemView:e.Views.Status,className:"status-collection"})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Regions.Messages=t.Marionette.Region.extend({onShow:function(e){e.on("after:item:added",this.scrollToBottom,this)},scrollToBottom:function(){this.$el.scrollTop(this.$el.prop("scrollHeight"))}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Layouts.Chat=t.Marionette.Layout.extend({template:Handlebars.templates["chat/layout"],regions:{controlsRegion:"#controls-region",avatarRegion:"#avatar-region",messagesRegion:{selector:"#messages-region",regionType:e.Regions.Messages},statusRegion:"#status-region",messageFormRegion:"#message-form-region"},serializeData:function(){var t=e.Objects.Models;return{page:t.page.toJSON(),user:t.user.toJSON()}}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Layouts.Invitation=t.Marionette.Layout.extend({template:Handlebars.templates["invitation/layout"],regions:{messagesRegion:{selector:"#invitation-messages-region",regionType:e.Regions.Messages}}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Layouts.LeaveMessage=t.Marionette.Layout.extend({template:Handlebars.templates["leave_message/layout"],regions:{leaveMessageFormRegion:"#content-wrapper",descriptionRegion:"#description-region"},serializeData:function(){return{page:e.Objects.Models.page.toJSON()}}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){e.Layouts.Survey=t.Marionette.Layout.extend({template:Handlebars.templates["survey/layout"],regions:{surveyFormRegion:"#content-wrapper"},serializeData:function(){return{page:e.Objects.Models.page.toJSON()}}})}(Mibew,Backbone),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){e.Objects.Models.Controls={},e.Objects.Models.Status={};var t=[],s=e.Application,o=s.module("Chat",{startWithParent:!1});o.addInitializer(function(o){var n=e.Objects,a=e.Objects.Models,i=e.Objects.Models.Controls,r=e.Objects.Models.Status;o.page&&a.page.set(o.page),a.thread=new e.Models.Thread(o.thread),a.user=new e.Models.ChatUser(o.user);var l=new e.Layouts.Chat;n.chatLayout=l,s.mainRegion.show(l);var d=new e.Collections.Controls;a.user.get("isAgent")||(i.userName=new e.Models.UserNameControl({weight:220}),d.add(i.userName),i.sendMail=new e.Models.SendMailControl({weight:200,link:o.links.mail,windowParams:o.windowsParams.mail}),d.add(i.sendMail)),a.user.get("isAgent")&&(i.redirect=new e.Models.RedirectControl({weight:200,link:o.links.redirect}),d.add(i.redirect),i.history=new e.Models.HistoryControl({weight:180,link:o.links.history,windowParams:o.windowsParams.history}),d.add(i.history)),i.sound=new e.Models.SoundControl({weight:160}),d.add(i.sound),i.refresh=new e.Models.RefreshControl({weight:140}),d.add(i.refresh),o.links.ssl&&(i.secureMode=new e.Models.SecureModeControl({weight:120,link:o.links.ssl}),d.add(i.secureMode)),i.close=new e.Models.CloseControl({weight:100}),d.add(i.close),n.Collections.controls=d,l.controlsRegion.show(new e.Views.ControlsCollection({collection:d})),r.message=new e.Models.StatusMessage({hideTimeout:5e3}),r.typing=new e.Models.StatusTyping({hideTimeout:5e3}),n.Collections.status=new e.Collections.Status([r.message,r.typing]),l.statusRegion.show(new e.Views.StatusCollection({collection:n.Collections.status})),a.user.get("isAgent")||(a.avatar=new e.Models.Avatar,l.avatarRegion.show(new e.Views.Avatar({model:a.avatar}))),n.Collections.messages=new e.Collections.Messages,a.messageForm=new e.Models.MessageForm(o.messageForm),l.messageFormRegion.show(new e.Views.MessageForm({model:a.messageForm})),l.messagesRegion.show(new e.Views.MessagesCollection({collection:n.Collections.messages})),a.soundManager=new e.Models.ChatSoundManager,t.push(n.server.callFunctionsPeriodically(function(){var t=e.Objects.Models.thread,s=e.Objects.Models.user;return[{"function":"update",arguments:{"return":{typing:"typing",canPost:"canPost"},references:{},threadId:t.get("id"),token:t.get("token"),lastId:t.get("lastId"),typed:s.get("typing"),user:!s.get("isAgent")}}]},function(t){return t.errorCode?void e.Objects.Models.Status.message.setMessage(t.errorMessage||"refresh failed"):(t.typing&&e.Objects.Models.Status.typing.show(),void e.Objects.Models.user.set({canPost:t.canPost||!1}))}))}),o.on("start",function(){e.Objects.server.restartUpdater()}),o.addFinalizer(function(){e.Objects.chatLayout.close();for(var s=0;s<t.length;s++)e.Objects.server.stopCallFunctionsPeriodically(t[s]);"undefined"!=typeof e.Objects.Models.avatar&&e.Objects.Models.avatar.finalize(),e.Objects.Collections.messages.finalize(),delete e.Objects.chatLayout,delete e.Objects.Models.thread,delete e.Objects.Models.user,delete e.Objects.Models.page,delete e.Objects.Models.avatar,delete e.Objects.Models.messageForm,delete e.Objects.Models.Controls,delete e.Objects.Models.Status,delete e.Objects.Collections.messages,delete e.Objects.Collections.controls,delete e.Objects.Collections.status})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){var t=[],s=e.Application,o=s.module("Invitation",{startWithParent:!1});o.addInitializer(function(o){var n=e.Objects,a=e.Objects.Models;a.thread=new e.Models.Thread(o.thread),a.user=new e.Models.ChatUser(o.user),n.invitationLayout=new e.Layouts.Invitation,s.mainRegion.show(n.invitationLayout),n.Collections.messages=new e.Collections.Messages,n.invitationLayout.messagesRegion.show(new e.Views.MessagesCollection({collection:n.Collections.messages})),t.push(n.server.callFunctionsPeriodically(function(){var t=e.Objects.Models.thread;return[{"function":"update",arguments:{"return":{},references:{},threadId:t.get("id"),token:t.get("token"),lastId:t.get("lastId"),typed:!1,user:!0}}]},function(){}))}),o.on("start",function(){e.Objects.server.restartUpdater()}),o.addFinalizer(function(){e.Objects.invitationLayout.close();for(var s=0;s<t.length;s++)e.Objects.server.stopCallFunctionsPeriodically(t[s]);e.Objects.Collections.messages.finalize(),delete e.Objects.invitationLayout,delete e.Objects.Models.thread,delete e.Objects.Models.user,delete e.Objects.Collections.messages})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){var t=e.Application,s=t.module("LeaveMessage",{startWithParent:!1});s.addInitializer(function(s){var o=e.Objects,n=e.Objects.Models;s.page&&n.page.set(s.page),o.leaveMessageLayout=new e.Layouts.LeaveMessage,t.mainRegion.show(o.leaveMessageLayout),n.leaveMessageForm=new e.Models.LeaveMessageForm(s.leaveMessageForm),o.leaveMessageLayout.leaveMessageFormRegion.show(new e.Views.LeaveMessageForm({model:n.leaveMessageForm})),o.leaveMessageLayout.descriptionRegion.show(new e.Views.LeaveMessageDescription),n.leaveMessageForm.on("submit:complete",function(){o.leaveMessageLayout.leaveMessageFormRegion.close(),o.leaveMessageLayout.descriptionRegion.close(),o.leaveMessageLayout.descriptionRegion.show(new e.Views.LeaveMessageSentDescription)})}),s.addFinalizer(function(){e.Objects.leaveMessageLayout.close(),delete e.Objects.Models.leaveMessageForm})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e){var t=e.Application,s=t.module("Survey",{startWithParent:!1});s.addInitializer(function(s){var o=e.Objects,n=e.Objects.Models;o.surveyLayout=new e.Layouts.Survey,t.mainRegion.show(o.surveyLayout),n.surveyForm=new e.Models.SurveyForm(s.surveyForm),o.surveyLayout.surveyFormRegion.show(new e.Views.SurveyForm({model:n.surveyForm}))}),s.addFinalizer(function(){e.Objects.surveyLayout.close(),delete e.Objects.Models.surveyForm})}(Mibew),/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function(e,t){var s=e.Application;s.addRegions({mainRegion:"#main-region"}),s.addInitializer(function(o){switch(e.PluginOptions=o.plugins||{},e.Objects.server=new e.Server(t.extend({interactionType:MibewAPIChatInteraction},o.server)),e.Objects.Models.page=new e.Models.Page(o.page),o.startFrom){case"chat":s.Chat.start(o.chatOptions);break;case"survey":s.Survey.start(o.surveyOptions);break;case"leaveMessage":s.LeaveMessage.start(o.leaveMessageOptions);break;case"invitation":s.Invitation.start(o.invitationOptions);break;default:throw new Error("Dont know how to start!")}}),s.on("start",function(){e.Objects.server.runUpdater()})}(Mibew,_);