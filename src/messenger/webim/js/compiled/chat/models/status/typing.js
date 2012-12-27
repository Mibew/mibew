/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,b){a.Models.StatusTyping=a.Models.Status.extend({defaults:b.extend({},a.Models.Status.prototype.defaults,{visible:!1,hideTimeout:2E3}),getModelType:function(){return"StatusTyping"},show:function(){this.set({visible:!0});this.autoHide()}})})(Mibew,_);
