/*
 Copyright 2005-2014 the original author or authors.
 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
      http://www.apache.org/licenses/LICENSE-2.0
*/
(function(){var h=Handlebars.template,k=Handlebars.templates=Handlebars.templates||{};k.default_control=h({compiler:[5,">= 2.0.0"],main:function(a,c,d,e){var b;d=this.escapeExpression;return"<strong>"+d((b=c.title||a&&a.title,"function"===typeof b?b.call(a,{name:"title",hash:{},data:e}):b))+"</strong>"},useData:!0});k.message=h({1:function(a,c,d,e){var b;d=this.escapeExpression;return"<span class='n"+d((b=c.kindName||a&&a.kindName,"function"===typeof b?b.call(a,{name:"kindName",hash:{},data:e}):b))+
"'>"+d((b=c.name||a&&a.name,"function"===typeof b?b.call(a,{name:"name",hash:{},data:e}):b))+"</span>: "},3:function(a,c,d,e){var b;d=c.helperMissing;var f=this.escapeExpression;return f((b=c.apply||a&&a.apply||d,b.call(a,a&&a.message,"urlReplace, nl2br, allowTags",{name:"apply",hash:{},data:e})))},5:function(a,c,d,e){var b;d=c.helperMissing;var f=this.escapeExpression;return f((b=c.apply||a&&a.apply||d,b.call(a,a&&a.message,"urlReplace, nl2br",{name:"apply",hash:{},data:e})))},compiler:[5,">= 2.0.0"],
main:function(a,c,d,e){var b,f;b=c.helperMissing;d=this.escapeExpression;var g="<span>"+d((f=c.formatTime||a&&a.formatTime||b,f.call(a,a&&a.created,{name:"formatTime",hash:{},data:e})))+"</span>\n";if((b=c["if"].call(a,a&&a.name,{name:"if",hash:{},fn:this.program(1,e),inverse:this.noop,data:e}))||0===b)g+=b;g+="\n<span class='m"+d((f=c.kindName||a&&a.kindName,"function"===typeof f?f.call(a,{name:"kindName",hash:{},data:e}):f))+"'>";if((b=c["if"].call(a,a&&a.allowFormatting,{name:"if",hash:{},fn:this.program(3,
e),inverse:this.program(5,e),data:e}))||0===b)g+=b;return g+"</span><br/>"},useData:!0})})();
