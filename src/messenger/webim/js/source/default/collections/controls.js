/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone){

    /**
     * @class Represents collection of chat controls
     */
    Mibew.Collections.Controls = Backbone.Collection.extend(
        /** @lends Mibew.Collections.Controls.prototype */
        {
            /**
             * Use for sort controls in collection
             * @param {Backbone.Model} model Control model
             */
            comparator: function(model) {
                return model.get('weight');
            }
        }
    );

})(Mibew, Backbone);