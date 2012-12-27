/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Models.Sound=b.Model.extend({play:function(a){this.set({file:a});this.trigger("sound:play",this)}})})(Mibew,Backbone);
