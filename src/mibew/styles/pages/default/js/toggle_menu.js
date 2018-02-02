/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
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

(function (Mibew, $) {
    $(function() {
        $('#toggle-menu a').on('click', function(e) {
            e.preventDefault();

            // A special variable is used here only for readability.
            var isMenuShown = $('#content').hasClass('content-inner');
            if (isMenuShown) {
                $('#sidebar').addClass('sidebar-hidden');
                $('#content').removeClass('content-inner');
                $('#content').addClass('content-no-menu');
                $(this).html(Mibew.Localization.trans('Show menu'));
            } else {
                $('#sidebar').removeClass('sidebar-hidden');
                $('#content').removeClass('content-no-menu');
                $('#content').addClass('content-inner');
                $(this).html(Mibew.Localization.trans('Hide menu'));
            }
        });
    });
})(Mibew, jQuery);
