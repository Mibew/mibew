/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(c,d){c.Views.BaseSurveyForm=d.Marionette.ItemView.extend({events:{'change select[name="group"]':"changeGroupDescription","submit form":"preventSubmit"},ui:{groupSelect:'select[name="group"]',groupDescription:"#groupDescription",name:'input[name="name"]',email:'input[name="email"]',message:'textarea[name="message"]',errors:".errors",ajaxLoader:"#ajax-loader"},modelEvents:{invalid:"hideAjaxLoader showError","submit:error":"hideAjaxLoader showError"},preventSubmit:function(a){a.preventDefault()},
changeGroupDescription:function(){var a=this.ui.groupSelect.prop("selectedIndex"),a=this.model.get("groups")[a].description||"";this.ui.groupDescription.text(a)},showError:function(a,b){this.ui.errors.html("string"==typeof b?b:b.message)},serializeData:function(){var a=this.model.toJSON();a.page=c.Objects.Models.page.toJSON();return a},showAjaxLoader:function(){this.ui.ajaxLoader.show()},hideAjaxLoader:function(){this.ui.ajaxLoader.hide()}})})(Mibew,Backbone);
