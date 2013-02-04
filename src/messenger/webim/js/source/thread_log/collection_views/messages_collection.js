/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew) {

    /**
     * @class Represents messages list view
     */
    Mibew.Views.MessagesCollection = Mibew.Views.CollectionBase.extend(
        /** @lends Mibew.Views.MessagesCollection.prototype */
        {
            /**
             * Default item view constructor.
             * @type Function
             */
            itemView: Mibew.Views.Message,

            /**
             * Class name for view's DOM element
             * @type String
             */
            className: 'messages-collection'
        }
    );

})(Mibew);