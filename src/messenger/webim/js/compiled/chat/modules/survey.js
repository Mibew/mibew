/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a){var d=a.Application,b=d.module("Survey",{startWithParent:!1});b.addInitializer(function(b){var c=a.Objects,e=a.Objects.Models;c.surveyLayout=new a.Layouts.Survey;d.mainRegion.show(c.surveyLayout);e.surveyForm=new a.Models.SurveyForm(b.surveyForm);c.surveyLayout.surveyFormRegion.show(new a.Views.SurveyForm({model:e.surveyForm}))});b.addFinalizer(function(){a.Objects.surveyLayout.close();delete a.Objects.Models.surveyForm})})(Mibew);
