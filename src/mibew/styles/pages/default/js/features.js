/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function($) {
    function updateSurvey() {
        if ($("#enablepresurvey").is(":checked")) {
            $(".undersurvey").show();
        } else {
            $(".undersurvey").hide();
        }
    }

    function updateSSL() {
        if ($("#enablessl").is(":checked")) {
            $(".underssl").show();
        } else {
            $(".underssl").hide();
        }
    }

    function updateGroups() {
        if ($("#enablegroups").is(":checked")) {
            $(".undergroups").show();
        } else {
            $(".undergroups").hide();
        }
    }

    $(function() {
        $("#enablepresurvey").change(function() {
            updateSurvey();
        });
        $("#enablessl").change(function() {
            updateSSL();
        });
        $("#enablegroups").change(function() {
            updateGroups();
        });
        updateSurvey();
        updateSSL();
        updateGroups();
    });
})(jQuery);