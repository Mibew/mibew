/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
      http://www.apache.org/licenses/LICENSE-2.0
*/
(function(){var k=Handlebars.template,l=Handlebars.templates=Handlebars.templates||{};l.default_control=k(function(a,b,e,h,f){this.compilerInfo=[4,">= 1.0.0"];e=this.merge(e,a.helpers);f=f||{};h=this.escapeExpression;a="<strong>";(e=e.title)?e=e.call(b,{hash:{},data:f}):(e=b.title,e="function"===typeof e?e.apply(b):e);return a+=h(e)+"</strong>"});l.message=k(function(a,b,e,h,f){this.compilerInfo=[4,">= 1.0.0"];e=this.merge(e,a.helpers);f=f||{};var c,g=this.escapeExpression,j=e.helperMissing;a={hash:{},
data:f};a="<span>"+(g((c=e.formatTime||b.formatTime,c?c.call(b,b.created,a):j.call(b,"formatTime",b.created,a)))+"</span>\n");if((c=e["if"].call(b,b.name,{hash:{},inverse:this.noop,fn:this.program(1,function(a,c){var b,d;b="<span class='n";(d=e.kindName)?d=d.call(a,{hash:{},data:c}):(d=a.kindName,d="function"===typeof d?d.apply(a):d);b+=g(d)+"'>";(d=e.name)?d=d.call(a,{hash:{},data:c}):(d=a.name,d="function"===typeof d?d.apply(a):d);return b+=g(d)+"</span>: "},f),data:f}))||0===c)a+=c;a+="\n<span class='m";
(c=e.kindName)?c=c.call(b,{hash:{},data:f}):(c=b.kindName,c="function"===typeof c?c.apply(b):c);a+=g(c)+"'>";if((c=e["if"].call(b,b.allowFormatting,{hash:{},inverse:this.program(5,function(a,b){var c,d;d={hash:{},data:b};return g((c=e.apply||a.apply,c?c.call(a,a.message,"urlReplace, nl2br",d):j.call(a,"apply",a.message,"urlReplace, nl2br",d)))},f),fn:this.program(3,function(a,c){var b,d;d={hash:{},data:c};return g((b=e.apply||a.apply,b?b.call(a,a.message,"urlReplace, nl2br, allowTags",d):j.call(a,
"apply",a.message,"urlReplace, nl2br, allowTags",d)))},f),data:f}))||0===c)a+=c;return a+="</span><br/>"})})();
