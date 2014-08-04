/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,n){var i={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},s=/[&<>'"`]/g;e.Views.Message=t.Marionette.ItemView.extend({template:n.templates.message,className:"message",modelEvents:{change:"render"},serializeData:function(){var e=this.model.toJSON(),t=this.model.get("kind");return e.allowFormatting=t!=this.model.KIND_USER&&t!=this.model.KIND_AGENT,e.kindName=this.kindToString(t),e.message=this.escapeString(e.message),e},kindToString:function(e){return e==this.model.KIND_USER?"user":e==this.model.KIND_AGENT?"agent":e==this.model.KIND_FOR_AGENT?"hidden":e==this.model.KIND_INFO?"inf":e==this.model.KIND_CONN?"conn":e==this.model.KIND_EVENTS?"event":e==this.model.KIND_PLUGIN?"plugin":""},escapeString:function(e){return e.replace(s,function(e){return i[e]||"&amp;"})}})}(Mibew,Backbone,Handlebars);