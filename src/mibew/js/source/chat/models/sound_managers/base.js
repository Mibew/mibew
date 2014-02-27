/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, Backbone) {

    /**
     * @class Represents Base helper model which manage all sounds
     */
    Mibew.Models.BaseSoundManager = Backbone.Model.extend(
        /** @lends Mibew.Models.BaseSoundManager.prototype */
        {
            /**
             * A list of default model values.
             * @type Object
             */
            defaults: {
                /**
                 * Indicates if sound enabled or not
                 */
                enabled: true
            },

            /**
             * Play sound file if sound enabled.
             * @param {String} file Sound file to play
             */
            play: function(file) {
                if (this.get('enabled')) {
                    Mibew.Utils.playSound(file);
                }
            }
        }
    );

})(Mibew, Backbone);