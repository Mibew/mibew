/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,e,b){var c=d.Views.BaseSurveyForm;d.Views.LeaveMessageForm=c.extend({template:e.templates.leave_message_form,events:b.extend({},c.prototype.events,{"click #send-message":"submitForm"}),ui:b.extend({},c.prototype.ui,{captcha:'input[name="captcha"]',captchaImg:"#captcha-img"}),modelEvents:b.extend({},c.prototype.modelEvents,{"submit:error":"hideAjaxLoader showError submitError"}),submitForm:function(){this.showAjaxLoader();var a={};this.model.get("groups")&&(a.groupId=this.ui.groupSelect.val());
a.name=this.ui.name.val()||"";a.email=this.ui.email.val()||"";a.message=this.ui.message.val()||"";this.model.get("showCaptcha")&&(a.captcha=this.ui.captcha.val()||"");this.model.set(a,{validate:!0});this.model.submit()},submitError:function(a,c){if(c.code==a.ERROR_WRONG_CAPTCHA&&a.get("showCaptcha")){var b=this.ui.captchaImg.attr("src"),b=b.replace(/\?d\=[0-9]+/,"");this.ui.captchaImg.attr("src",b+"?d="+(new Date).getTime())}}})})(Mibew,Handlebars,_);
