/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(e,t,o){e.Views.HistoryControl=e.Views.Control.extend({template:t.templates["chat/controls/history"],events:o.extend({},e.Views.Control.prototype.events,{click:"showHistory"}),showHistory:function(){var t=e.Objects.Models.user,o=this.model.get("link");if(t.get("isAgent")&&o){var s=this.model.get("windowParams");o=o.replace("&amp;","&","g");var n=window.open(o,"UserHistory",s);null!==n&&(n.focus(),n.opener=window)}}})}(Mibew,Handlebars,_);