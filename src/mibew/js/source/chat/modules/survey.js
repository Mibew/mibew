/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew){

    // Create shortcut for Application object
    var app = Mibew.Application;

    // Create an Survey module instance
    var survey = app.module('Survey', {startWithParent: false});

    // Add module initializer
    survey.addInitializer(function(options) {

        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;

        // Create instance of the survey layout
        objs.surveyLayout = new Mibew.Layouts.Survey();

        // Show layout at page
        app.mainRegion.show(objs.surveyLayout);

        // Create an instance of the survey form
        models.surveyForm = new Mibew.Models.SurveyForm(
            options.surveyForm
        );

        objs.surveyLayout.surveyFormRegion.show(new Mibew.Views.SurveyForm({
            model: models.surveyForm
        }));
    });

    // Add module finalizer
    survey.addFinalizer(function() {
        // Close layout
        Mibew.Objects.surveyLayout.close();

        // Remove instance of survey form model
        delete Mibew.Objects.Models.surveyForm;
    });

})(Mibew);