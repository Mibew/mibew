/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var SurveyForm=Class.create();Class.inherit(SurveyForm,ClientForm,{checkFields:function(){return null==this.form.email||"hidden"==this.form.email.getAttribute("type")?null:!this.emailIsValid(this.form.email)?this.localizedStrings.wrongEmail||"":null}});EventHelper.register(window,"onload",function(){Survey=new SurveyForm(document.surveyForm);Survey.localize(localizedStrings)});