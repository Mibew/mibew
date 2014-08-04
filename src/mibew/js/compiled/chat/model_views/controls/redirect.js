/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,i){e.Views.RedirectControl=e.Views.Control.extend({template:t.templates["chat/controls/redirect"],events:i.extend({},e.Views.Control.prototype.events,{click:"redirect"}),initialize:function(){e.Objects.Models.user.on("change",this.render,this)},serializeData:function(){var t=this.model.toJSON();return t.user=e.Objects.Models.user.toJSON(),t},redirect:function(){var t=e.Objects.Models.user;if(t.get("isAgent")&&t.get("canPost")){var i=this.model.get("link");if(i){var s=e.Objects.Models.page.get("style"),n="";s&&(n=(-1===i.indexOf("?")?"?":"&")+"style="+s),window.location.href=i.replace(/\&amp\;/g,"&")+n}}}})}(Mibew,Handlebars,_);