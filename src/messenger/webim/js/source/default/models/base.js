/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew, Backbone) {

    /**
     * @class Base model class
     */
    Mibew.Models.Base = Backbone.Model.extend(
        /** @lends Mibew.Models.Base.prototype */
        {
            /**
             * Returns the model type. Used for dynamic binding models and
             * views and for some other purposes
             * @returns {String} Model type name
             */
            getModelType: function() {
                return '';
            }
        }
    );

})(Mibew, Backbone);