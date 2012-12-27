/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b,c){b.Models.Status=b.Models.Base.extend({defaults:{visible:!0,weight:0,hideTimeout:4E3,title:""},initialize:function(){this.hideTimer=null},autoHide:function(a){a=a||this.get("hideTimeout");this.hideTimer&&clearTimeout(this.hideTimer);this.hideTimer=setTimeout(c.bind(function(){this.set({visible:!1})},this),a)}})})(Mibew,_);
