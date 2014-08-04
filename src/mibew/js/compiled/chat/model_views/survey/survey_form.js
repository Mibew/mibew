/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,s,t){var i=e.Views.BaseSurveyForm;e.Views.SurveyForm=i.extend({template:s.templates["survey/form"],events:t.extend({},i.prototype.events,{"click #submit-survey":"submitForm"}),submitForm:function(){this.showAjaxLoader();var e={};this.model.get("groups")&&(e.groupId=this.ui.groupSelect.val()),this.model.get("canChangeName")&&(e.name=this.ui.name.val()||""),this.model.get("showEmail")&&(e.email=this.ui.email.val()||""),this.model.get("showMessage")&&(e.message=this.ui.message.val()||""),this.model.set(e,{validate:!0}),this.model.submit()}})}(Mibew,Handlebars,_);