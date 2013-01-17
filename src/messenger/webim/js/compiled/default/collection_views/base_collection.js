/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(d,b,e){d.Views.CollectionBase=b.Marionette.CollectionView.extend({itemView:b.Marionette.ItemView,buildItemView:function(a,b,c){c=e.extend({model:a},c);return(a=a.getModelType())&&d.Views[a]?new d.Views[a](c):new b(c)}})})(Mibew,Backbone,_);
