/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2023 the original author or authors.
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

(function(Mibew, $) {
    $(document).ready(function() {
        var basePath = window.location.href.replace(/install\.php\/install\/done$/, 'install/done');
        $('#check-new-visitor').click(function(){
            Mibew.Utils.playSound(basePath + '/../../sounds/new_user');
        });

        $('#check-new-message').click(function() {
            Mibew.Utils.playSound(basePath + '/../../sounds/new_message');
        });

        $('#check-invitation').click(function() {
            Mibew.Utils.playSound(basePath + '/../../sounds/invite');
        });
    });
})(Mibew, jQuery);
