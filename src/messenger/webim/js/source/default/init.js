/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

/**
 * @namespace Application namespace
 */
var Mibew = {};

(function(Mibew, Backbone){

    // Use Backbone.Marionette with handlebars.js
    Backbone.Marionette.TemplateCache.prototype.compileTemplate = function(rawTemplate) {
        return Handlebars.compile(rawTemplate);
    }

    /**
     * @namespace Holds Backbone Models constructors
     */
    Mibew.Models = {};

    /**
     * @namespace Holds Backbone Collections constructors
     */
    Mibew.Collections = {};

    /**
     * @namespace Holds Backbone Views constructors
     */
    Mibew.Views = {};

    /**
     * @namespace Holds objects instances
     */
    Mibew.Objects = {};

    /**
     * @namespace Holds Models instances
     */
    Mibew.Objects.Models = {};

    /**
     * @namespace Holds Collections instances
     */
    Mibew.Objects.Collections = {};

})(Mibew, Backbone);