/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone) {

    /**
     * @class Used for repesent sound notice
     */
    Mibew.Models.Sound = Backbone.Model.extend(
        /** @lends Mibew.Models.Sound.prototype */
        {
            /**
             * Play sound file
             *
             * @param {String} filePath Path to file that should be played
             */
            play: function(filePath) {
                this.set({file: filePath});
                this.trigger('sound:play', this);
            }
        }
    );

})(Mibew, Backbone);