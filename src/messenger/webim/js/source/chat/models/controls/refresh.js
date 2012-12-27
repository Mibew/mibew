/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

    /**
     * @class Represent Refresh control model
     */
    Mibew.Models.RefreshControl = Mibew.Models.Control.extend(
        /** @lends Mibew.Models.RefreshControl.prototype */
        {
            /**
             * Returns model type
             * @returns {String} Model type
             */
            getModelType: function() {
                return 'RefreshControl';
            },

            /**
             * Refresh message window
             */
            refresh: function() {
                Mibew.Objects.server.restartUpdater();
            }
        }
    );

})(Mibew);