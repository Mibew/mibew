/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(a){var d=a.Application,b=d.module("Survey",{startWithParent:!1});b.addInitializer(function(b){var c=a.Objects,e=a.Objects.Models;c.surveyLayout=new a.Layouts.Survey;d.mainRegion.show(c.surveyLayout);e.surveyForm=new a.Models.SurveyForm(b.surveyForm);c.surveyLayout.surveyFormRegion.show(new a.Views.SurveyForm({model:e.surveyForm}))});b.addFinalizer(function(){a.Objects.surveyLayout.close();delete a.Objects.Models.surveyForm})})(Mibew);
