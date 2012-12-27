/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.SoundControl=a.Views.Control.extend({template:b.templates.sound_control,events:c.extend({},a.Views.Control.prototype.events,{click:"toggle"}),toggle:function(){this.model.set({enabled:!this.model.get("enabled")})}})})(Mibew,Handlebars,_);
