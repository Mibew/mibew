/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
      http://www.apache.org/licenses/LICENSE-2.0
*/
(function(){var m=Handlebars.template,n=Handlebars.templates=Handlebars.templates||{};n.default_control=m(function(c,a,e,h,f){this.compilerInfo=[4,">= 1.0.0"];e=this.merge(e,c.helpers);f=f||{};h=this.escapeExpression;c="<strong>";(e=e.title)?a=e.call(a,{hash:{},data:f}):(e=a&&a.title,a="function"===typeof e?e.call(a,{hash:{},data:f}):e);return c+=h(a)+"</strong>"});n.message=m(function(c,a,e,h,f){this.compilerInfo=[4,">= 1.0.0"];e=this.merge(e,c.helpers);f=f||{};var b,k,g=this.escapeExpression,l=
e.helperMissing;c=""+("<span>"+g((b=e.formatTime||a&&a.formatTime,k={hash:{},data:f},b?b.call(a,a&&a.created,k):l.call(a,"formatTime",a&&a.created,k)))+"</span>\n");if((b=e["if"].call(a,a&&a.name,{hash:{},inverse:this.noop,fn:this.program(1,function(a,b){var c,d;c="<span class='n";(d=e.kindName)?d=d.call(a,{hash:{},data:b}):(d=a&&a.kindName,d="function"===typeof d?d.call(a,{hash:{},data:b}):d);c+=g(d)+"'>";(d=e.name)?d=d.call(a,{hash:{},data:b}):(d=a&&a.name,d="function"===typeof d?d.call(a,{hash:{},
data:b}):d);return c+=g(d)+"</span>: "},f),data:f}))||0===b)c+=b;c+="\n<span class='m";(b=e.kindName)?b=b.call(a,{hash:{},data:f}):(b=a&&a.kindName,b="function"===typeof b?b.call(a,{hash:{},data:f}):b);c+=g(b)+"'>";if((b=e["if"].call(a,a&&a.allowFormatting,{hash:{},inverse:this.program(5,function(a,b){var c,d;return g((c=e.apply||a&&a.apply,d={hash:{},data:b},c?c.call(a,a&&a.message,"urlReplace, nl2br",d):l.call(a,"apply",a&&a.message,"urlReplace, nl2br",d)))},f),fn:this.program(3,function(a,c){var b,
d;return g((b=e.apply||a&&a.apply,d={hash:{},data:c},b?b.call(a,a&&a.message,"urlReplace, nl2br, allowTags",d):l.call(a,"apply",a&&a.message,"urlReplace, nl2br, allowTags",d)))},f),data:f}))||0===b)c+=b;return c+"</span><br/>"})})();
