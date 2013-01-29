/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,a,f){var e=function(b,a,c){c=f.extend({model:b},c);return"function"!=typeof b.getModelType?new a(c):(b=b.getModelType())&&d.Views[b]?new d.Views[b](c):new a(c)};d.Views.CollectionBase=a.Marionette.CollectionView.extend({itemView:a.Marionette.ItemView,buildItemView:e});d.Views.CompositeBase=a.Marionette.CompositeView.extend({buildItemView:e})})(Mibew,Backbone,_);
