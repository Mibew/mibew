/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew) {

    /**
     * @class Represents controls bar
     */
    Mibew.Views.ControlsCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.ControlsCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Control,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'controls-collection'
        }
    );

})(Mibew);