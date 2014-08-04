/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,i,t){var n=function(i,n,o){var w=t.extend({model:i},o);if("function"!=typeof i.getModelType)return new n(w);var r=i.getModelType();return r&&e.Views[r]?new e.Views[r](w):new n(w)};e.Views.CollectionBase=i.Marionette.CollectionView.extend({itemView:i.Marionette.ItemView,buildItemView:n}),e.Views.CompositeBase=i.Marionette.CompositeView.extend({buildItemView:n})}(Mibew,Backbone,_);