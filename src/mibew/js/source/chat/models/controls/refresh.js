/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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