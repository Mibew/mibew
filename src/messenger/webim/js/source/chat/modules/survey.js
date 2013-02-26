/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
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