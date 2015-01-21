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

(function(Mibew, _){

    // Create shortcut for base model
    var BaseModel = Mibew.Models.BaseSurveyForm;


    /**
     * @class Represents leave message form model
     */
    Mibew.Models.LeaveMessageForm = BaseModel.extend(
        /** @lends Mibew.Models.LeaveMessageForm.prototype */
        {
            /**
             * A list of default model values.
             * The model inherits default values from
             * {@link Mibew.Models.BaseSurveyForm.prototype.defaults}
             * @type Object
             */
            defaults : _.extend(
                {},
                BaseModel.prototype.defaults,
                {
                    /**
                     * Indicate if captcha should be shown
                     * @type Boolean
                     */
                    showCaptcha: false,

                    /**
                     * Value of captcha field
                     * @type String
                     */
                    captcha: ''
                }
            ),

            /**
             * Check attributes before set
             * @param Object attributes Attributes hash for test
             */
            validate: function(attributes) {
                // Create some shortcuts
                var l = Mibew.Localization;

                // Check email
                if (typeof attributes.email != 'undefined') {
                    if (! attributes.email) {
                        return l.trans('Please fill "{0}".', l.trans('Your email'));
                    }
                    if(! Mibew.Utils.checkEmail(attributes.email)) {
                        return l.trans('Please fill "{0}" correctly.', l.trans('Your email'));
                    }
                }

                // Check name
                if (typeof attributes.name != 'undefined') {
                    if (! attributes.name) {
                        return l.trans('Please fill "{0}".', l.trans('Your name'));
                    }
                }

                // Check message
                if (typeof attributes.message != 'undefined') {
                    if (! attributes.message) {
                        return l.trans('Please fill "{0}".', l.trans('Message'));
                    }
                }

                // Check captcha
                if (this.get('showCaptcha')) {
                    if (typeof attributes.captcha != 'undefined') {
                        if (! attributes.captcha) {
                            return l.trans('The letters you typed don\'t match the letters that were shown in the picture.');
                        }
                    }
                }
            },

            /**
             * Send form information to server
             */
            submit: function() {
                if (! this.validate(this.attributes)) {
                    var self = this;
                    Mibew.Objects.server.callFunctions(
                        [{
                            "function": "processLeaveMessage",
                            "arguments": {
                                "references": {},
                                "return": {},
                                "groupId": self.get('groupId'),
                                "name": self.get('name'),
                                "info": self.get('info'),
                                "email": self.get('email'),
                                "message": self.get('message'),
                                "referrer": self.get('referrer'),
                                "captcha": self.get('captcha'),
                                // There is no initialized thread yet
                                "threadId": null,
                                "token": null
                            }
                        }],
                        function(args){
                            if (args.errorCode == 0) {
                                self.trigger('submit:complete', self);
                            } else {
                                self.trigger(
                                    'submit:error',
                                    self,
                                    {
                                        code: args.errorCode,
                                        message: args.errorMessage || ''
                                    }
                                );
                            }
                        },
                        true
                    );
                }
            },

            /** Error codes */

            /**
             * User enter wrong captcha value.
             * Correspond ThreadProcessorException::ERROR_WRONG_CAPTCHA at
             * server side.
             */
            ERROR_WRONG_CAPTCHA: 10

            /** End of error codes */
        }
    );

})(Mibew, _);