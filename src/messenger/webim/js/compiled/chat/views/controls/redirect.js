/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,d,e){a.Views.RedirectControl=a.Views.Control.extend({template:d.templates.redirect_control,events:e.extend({},a.Views.Control.prototype.events,{click:"redirect"}),initialize:function(){a.Objects.Models.user.on("change",this.render,this)},serializeData:function(){var b=this.model.toJSON();b.user=a.Objects.Models.user.toJSON();return b},redirect:function(){var b=a.Objects.Models.user;if(b.get("isAgent")&&b.get("canPost")&&(b=this.model.get("link"))){var c=a.Objects.Models.page.get("style");
window.location.href=b.replace(/\&amp\;/g,"&")+(c?"&style="+c:"")}}})})(Mibew,Handlebars,_);
