/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b,c){a.Views.RefreshControl=a.Views.Control.extend({template:b.templates.refresh_control,events:c.extend({},a.Views.Control.prototype.events,{click:"refresh"}),refresh:function(){this.model.refresh()}})})(Mibew,Handlebars,_);
