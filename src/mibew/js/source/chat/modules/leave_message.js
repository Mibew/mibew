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

(function(Mibew){

    // Create shortcut for Application object
    var app = Mibew.Application;

    // Create LeaveMessage module
    var leaveMessage = app.module('LeaveMessage', {startWithParent: false});

    // Add module initializer
    leaveMessage.addInitializer(function(options) {
        // Create some shortcuts
        var objs = Mibew.Objects;
        var models = Mibew.Objects.Models;

        // Update page options to change logo block
        if (options.page) {
            models.page.set(options.page);
        }

        // Create instance of the leave message layout
        objs.leaveMessageLayout = new Mibew.Layouts.LeaveMessage();

        // Show layout at page
        app.mainRegion.show(objs.leaveMessageLayout);

        // Create an instance of the leave message form
        models.leaveMessageForm = new Mibew.Models.LeaveMessageForm(
            options.leaveMessageForm
        );

        objs.leaveMessageLayout.leaveMessageFormRegion.show(
            new Mibew.Views.LeaveMessageForm({
                model: models.leaveMessageForm
            })
        );
        objs.leaveMessageLayout.descriptionRegion.show(
            new Mibew.Views.LeaveMessageDescription()
        );

        // When message sent form should be hide and description should be
        // changed
        models.leaveMessageForm.on('submit:complete', function() {
            objs.leaveMessageLayout.leaveMessageFormRegion.empty();
            objs.leaveMessageLayout.descriptionRegion.empty();

            objs.leaveMessageLayout.descriptionRegion.show(
                new Mibew.Views.LeaveMessageSentDescription()
            );
        });

    });

    // Add module finalizer
    leaveMessage.addFinalizer(function() {
        // Close layout
        Mibew.Objects.leaveMessageLayout.destroy();

        // Remove instance of leaveMessage form model
        delete Mibew.Objects.Models.leaveMessageForm;
    });

})(Mibew);