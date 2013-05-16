/*
 Copyright 2005-2013 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(d,b,f){var e=function(a,b,c){c=f.extend({model:a},c);return"function"!=typeof a.getModelType?new b(c):(a=a.getModelType())&&d.Views[a]?new d.Views[a](c):new b(c)};d.Views.CollectionBase=b.Marionette.CollectionView.extend({itemView:b.Marionette.ItemView,buildItemView:e});d.Views.CompositeBase=b.Marionette.CompositeView.extend({buildItemView:e,renderCollection:function(){var a=Array.prototype.slice.apply(arguments);b.Marionette.CollectionView.prototype.render.apply(this,a)}})})(Mibew,Backbone,
_);
