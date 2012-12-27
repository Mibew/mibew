/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Models.Avatar=a.Models.Base.extend({defaults:{imageLink:!1},initialize:function(){a.Objects.server.registerFunction("setupAvatar",b.bind(this.apiSetupAvatar,this))},apiSetupAvatar:function(a){a.imageLink&&this.set({imageLink:a.imageLink})}})})(Mibew,_);
