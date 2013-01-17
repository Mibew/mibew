/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.Sound=b.Marionette.ItemView.extend({template:c.templates.sound,className:"sound-player",modelEvents:{"sound:play":"render"}})})(Mibew,Backbone,Handlebars);
