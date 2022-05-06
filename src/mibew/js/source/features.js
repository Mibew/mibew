/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2022 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

(function($) {
    function updateSendEmail() {
        if ($("#user-can-send-email").is(":checked")) {
            $(".under-user-can-send-email").show();
        } else {
            $(".under-user-can-send-email").hide();
        }
    }

    function updateSurvey() {
        if ($("#enable-presurvey").is(":checked")) {
            $(".under-survey").show();
        } else {
            $(".under-survey").hide();
        }
    }

    function updateSSL() {
        if ($("#enable-ssl").is(":checked")) {
            $(".under-ssl").show();
        } else {
            $(".under-ssl").hide();
        }
    }

    function updateGroups() {
        if ($("#enable-groups").is(":checked")) {
            $(".under-groups").show();
        } else {
            $(".under-groups").hide();
        }
    }

    function updateTracking() {
        if ($("#enable-tracking").is(":checked")) {
            $(".under-tracking").show();
        } else {
            $(".under-tracking").hide();
        }
    }

    function updatePrivacyPolicy() {
        if ($("#enable-privacy-policy").is(":checked")) {
            $(".under-privacy-policy").show();
        } else {
            $(".under-privacy-policy").hide();
        }
    }

    $(function() {
        $("#user-can-send-email").change(function() {
            updateSendEmail();
        });
        $("#enable-presurvey").change(function() {
            updateSurvey();
        });
        $("#enable-ssl").change(function() {
            updateSSL();
        });
        $("#enable-groups").change(function() {
            updateGroups();
        });
        $("#enable-tracking").change(function() {
            updateTracking();
        });
        $("#enable-privacy-policy").change(function() {
            updatePrivacyPolicy();
        });
        updateSendEmail();
        updateSurvey();
        updateSSL();
        updateGroups();
        updateTracking();
        updatePrivacyPolicy();
    });
})(jQuery);
