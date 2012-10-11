/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
var MessageView=function(){var b={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#x27;","`":"&#x60;"},c=/[&<>'"`]/g;this.kindToString=function(a){return a==this.KIND_USER?"user":a==this.KIND_AGENT?"agent":a==this.KIND_FOR_AGENT?"hidden":a==this.KIND_INFO?"inf":a==this.KIND_CONN?"conn":a==this.KIND_EVENTS?"event":""};this.escapeString=function(a){return a.replace(c,function(a){return b[a]||"&amp;"})};this.themeMessage=function(a){if(!Handlebars.templates.message)throw Error("There is no template for message loaded!");
if(a.kind==this.KIND_AVATAR)throw Error("KIND_AVATAR message kind is deprecated at window!");a.allowFormating=a.kind!=this.KIND_USER&&a.kind!=this.KIND_AGENT;a.kindName=this.kindToString(a.kind);a.message=this.escapeString(a.message);return Handlebars.templates.message(a)}};MessageView.prototype.KIND_USER=1;MessageView.prototype.KIND_AGENT=2;MessageView.prototype.KIND_FOR_AGENT=3;MessageView.prototype.KIND_INFO=4;MessageView.prototype.KIND_CONN=5;MessageView.prototype.KIND_EVENTS=6;
MessageView.prototype.KIND_AVATAR=7;Handlebars.registerHelper("allowTags",function(b){b=b.replace(/&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,"<$1>$2</$1>");b=b.replace(/&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,'<span class="$1">$2</span>');return new Handlebars.SafeString(b)});