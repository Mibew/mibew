/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(d,a,f){var e=function(b,a,c){c=f.extend({model:b},c);return"function"!=typeof b.getModelType?new a(c):(b=b.getModelType())&&d.Views[b]?new d.Views[b](c):new a(c)};d.Views.CollectionBase=a.Marionette.CollectionView.extend({itemView:a.Marionette.ItemView,buildItemView:e});d.Views.CompositeBase=a.Marionette.CompositeView.extend({buildItemView:e})})(Mibew,Backbone,_);
