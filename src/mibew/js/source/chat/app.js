/*!
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2015 the original author or authors.
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

(function (Mibew, _) {

    // Create shortcut for application
    var app = Mibew.Application;

    // Define regions
    app.addRegions({
        mainRegion: '#main-region'
    });

    // Initialize application
    app.addInitializer(function(options){
        // Initialize Server
        Mibew.Objects.server = new Mibew.Server(_.extend(
            {'interactionType': MibewAPIChatInteraction},
            options.server
        ));

        // Initialize Page
        Mibew.Objects.Models.page = new Mibew.Models.Page(options.page);

        switch (options.startFrom) {
            case 'chat':
                app.Chat.start(options.chatOptions);
                break;
            case 'survey':
                app.Survey.start(options.surveyOptions);
                break;
            case 'leaveMessage':
                app.LeaveMessage.start(options.leaveMessageOptions);
                break;
            case 'invitation':
                app.Invitation.start(options.invitationOptions);
                break;
            default:
                throw new Error('Dont know how to start!');
                break;
        }
    });

    app.on('start', function() {
        // Run Server updater
        Mibew.Objects.server.runUpdater();
    });

})(Mibew, _);