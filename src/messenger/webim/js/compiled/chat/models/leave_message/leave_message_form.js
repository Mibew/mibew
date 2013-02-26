/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,e){var d=c.Models.BaseSurveyForm;c.Models.LeaveMessageForm=d.extend({defaults:e.extend({},d.prototype.defaults,{showCaptcha:!1,captcha:""}),validate:function(a){var b=c.Localization;if("undefined"!=typeof a.email){if(!a.email)return b.get("leavemessage.error.email.required");if(!c.Utils.checkEmail(a.email))return b.get("leavemessage.error.wrong.email")}if("undefined"!=typeof a.name&&!a.name)return b.get("leavemessage.error.name.required");if("undefined"!=typeof a.message&&!a.message)return b.get("leavemessage.error.message.required");
if(this.get("showCaptcha")&&"undefined"!=typeof a.captcha&&!a.captcha)return b.get("errors.captcha")},submit:function(){if(!this.validate(this.attributes)){var a=this;c.Objects.server.callFunctions([{"function":"processLeaveMessage",arguments:{references:{},"return":{},groupId:a.get("groupId"),name:a.get("name"),info:a.get("info"),email:a.get("email"),message:a.get("message"),referrer:a.get("referrer"),captcha:a.get("captcha"),threadId:null,token:null}}],function(b){0==b.errorCode?a.trigger("submit:complete",
a):a.trigger("submit:error",a,{code:b.errorCode,message:b.errorMessage||""})},!0)}},ERROR_WRONG_CAPTCHA:10})})(Mibew,_);
