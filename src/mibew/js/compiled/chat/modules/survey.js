/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e){var o=e.Application,r=o.module("Survey",{startWithParent:!1});r.addInitializer(function(r){var s=e.Objects,u=e.Objects.Models;s.surveyLayout=new e.Layouts.Survey,o.mainRegion.show(s.surveyLayout),u.surveyForm=new e.Models.SurveyForm(r.surveyForm),s.surveyLayout.surveyFormRegion.show(new e.Views.SurveyForm({model:u.surveyForm}))}),r.addFinalizer(function(){e.Objects.surveyLayout.close(),delete e.Objects.Models.surveyForm})}(Mibew);