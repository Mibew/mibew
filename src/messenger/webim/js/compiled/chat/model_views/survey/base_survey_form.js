/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c,d){c.Views.BaseSurveyForm=d.Marionette.ItemView.extend({events:{'change select[name="group"]':"changeGroupDescription","submit form":"preventSubmit"},ui:{groupSelect:'select[name="group"]',groupDescription:"#groupDescription",name:'input[name="name"]',email:'input[name="email"]',message:'textarea[name="message"]',errors:".errors"},modelEvents:{invalid:"showError","submit:error":"showError"},preventSubmit:function(b){b.preventDefault()},changeGroupDescription:function(){var b=this.ui.groupSelect.prop("selectedIndex"),
a=this.model.get("groups").descriptions||[];this.ui.groupDescription.text(a[b]||"")},showError:function(b,a){this.ui.errors.html("string"==typeof a?a:a.message)}})})(Mibew,Backbone);
