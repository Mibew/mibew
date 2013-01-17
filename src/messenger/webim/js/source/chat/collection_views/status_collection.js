/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew) {

    /**
     * @class Represents Status bar view
     */
    Mibew.Views.StatusCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.StatusCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Status,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'status-collection'
        }
    );

})(Mibew);