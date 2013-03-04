/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,d,e){var c=b.Views.BaseSurveyForm;b.Views.SurveyForm=c.extend({template:d.templates.survey_form,events:e.extend({},c.prototype.events,{"click #submit-survey":"submitForm"}),submitForm:function(){this.showAjaxLoader();var a={};this.model.get("groups")&&(a.groupId=this.ui.groupSelect.val());this.model.get("canChangeName")&&(a.name=this.ui.name.val()||"");this.model.get("showEmail")&&(a.email=this.ui.email.val()||"");this.model.get("showMessage")&&(a.message=this.ui.message.val()||"");this.model.set(a,
{validate:!0});this.model.submit()}})})(Mibew,Handlebars,_);
