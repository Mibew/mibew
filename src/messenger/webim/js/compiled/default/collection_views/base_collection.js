/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,b,f){var e=function(a,b,c){c=f.extend({model:a},c);return"function"!=typeof a.getModelType?new b(c):(a=a.getModelType())&&d.Views[a]?new d.Views[a](c):new b(c)};d.Views.CollectionBase=b.Marionette.CollectionView.extend({itemView:b.Marionette.ItemView,buildItemView:e});d.Views.CompositeBase=b.Marionette.CompositeView.extend({buildItemView:e,renderCollection:function(){var a=Array.prototype.slice.apply(arguments);b.Marionette.CollectionView.prototype.render.apply(this,a)}})})(Mibew,Backbone,
_);
