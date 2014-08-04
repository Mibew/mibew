/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
var Mibew={};!function(e,t,o){t.Marionette.TemplateCache.prototype.compileTemplate=function(e){return o.compile(e)};for(var a in o.templates)o.templates.hasOwnProperty(a)&&o.registerPartial(a,o.templates[a]);e.Models={},e.Collections={},e.Views={},e.Objects={},e.Objects.Models={},e.Objects.Collections={}}(Mibew,Backbone,Handlebars);