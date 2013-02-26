/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,e){var d=b.Models.BaseSurveyForm;b.Models.SurveyForm=d.extend({defaults:e.extend({},d.prototype.defaults,{showEmail:!1,showMessage:!1,canChangeName:!1}),validate:function(a){if(this.get("showEmail")&&"undefined"!=typeof a.email&&!b.Utils.checkEmail(a.email))return b.Localization.get("presurvey.error.wrong_email")},submit:function(){if(!this.validate(this.attributes)){var a=this;b.Objects.server.callFunctions([{"function":"processSurvey",arguments:{references:{},"return":{next:"next",options:"options"},
groupId:a.get("groupId"),name:a.get("name"),info:a.get("info"),email:a.get("email"),message:a.get("message"),referrer:a.get("referrer"),threadId:null,token:null}}],function(c){if(0==c.errorCode)switch(a.trigger("submit:complete",a),b.Application.Survey.stop(),c.next){case "chat":b.Application.Chat.start(c.options);break;case "leaveMessage":b.Application.LeaveMessage.start(c.options);break;default:throw Error("Do not know how to continue!");}else a.trigger("submit:error",a,{code:c.errorCode,message:c.errorMessage||
""})},!0)}}})})(Mibew,_);
