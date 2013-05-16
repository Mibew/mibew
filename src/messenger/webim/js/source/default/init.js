/**
 * @preserve Copyright 2005-2013 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
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