/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone){

    /**
     * @class Represents Messages region
     */
    Mibew.Regions.Messages = Backbone.Marionette.Region.extend(
        /** @lends Mibew.Regions.Message */
        {
            /**
             * Show view event handler. Register handler to view's
             * 'after:item:added' event.
             * @param {Backbone.Marionette.ItemView} view View to show in region
             */
            onShow: function(view) {
                view.on('after:item:added', this.scrollToBottom, this);
            },

            /**
             * Scroll region to bottom.
             */
            scrollToBottom: function() {
                this.$el.scrollTop(this.$el.prop('scrollHeight'));
            }

        }
    );

})(Mibew, Backbone);