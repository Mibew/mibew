/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,n,t){e.Views.SendMailControl=e.Views.Control.extend({template:n.templates["chat/controls/send_mail"],events:t.extend({},e.Views.Control.prototype.events,{click:"sendMail"}),sendMail:function(){var n=this.model.get("link"),t=e.Objects.Models.page;if(n){var l=this.model.get("windowParams"),o=t.get("style"),i="";o&&(i=(-1===n.indexOf("?")?"?":"&")+"style="+o),n=n.replace(/\&amp\;/g,"&")+i;var a=window.open(n,"ForwardMail",l);null!==a&&(a.focus(),a.opener=window)}}})}(Mibew,Handlebars,_);