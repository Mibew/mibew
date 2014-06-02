/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(b,e,f){b.Views.RedirectControl=b.Views.Control.extend({template:e.templates.chat_controls_redirect,events:f.extend({},b.Views.Control.prototype.events,{click:"redirect"}),initialize:function(){b.Objects.Models.user.on("change",this.render,this)},serializeData:function(){var a=this.model.toJSON();a.user=b.Objects.Models.user.toJSON();return a},redirect:function(){var a=b.Objects.Models.user;if(a.get("isAgent")&&a.get("canPost")&&(a=this.model.get("link"))){var c=b.Objects.Models.page.get("style"),
d="";c&&(d=(-1===a.indexOf("?")?"?":"&")+"style="+c);window.location.href=a.replace(/\&amp\;/g,"&")+d}}})})(Mibew,Handlebars,_);
