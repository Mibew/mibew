/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c){b.Models.Avatar=b.Models.Base.extend({defaults:{imageLink:!1},initialize:function(){this.registeredFunctions=[];this.registeredFunctions.push(b.Objects.server.registerFunction("setupAvatar",c.bind(this.apiSetupAvatar,this)))},finalize:function(){for(var a=0;a<this.registeredFunctions.length;a++)b.Objects.server.unregisterFunction(this.registeredFunctions[a])},apiSetupAvatar:function(a){a.imageLink&&this.set({imageLink:a.imageLink})}})})(Mibew,_);
