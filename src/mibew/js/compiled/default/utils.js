/*
 Copyright 2005-2014 the original author or authors.

 Licensed under the Apache License, Version 2.0 (the "License").
 You may obtain a copy of the License at
     http://www.apache.org/licenses/LICENSE-2.0
*/
(function(c,d){c.Utils={};c.Utils.toUpperCaseFirst=function(a){return"string"!=typeof a?!1:""===a?a:a.substring(0,1).toUpperCase()+a.substring(1)};c.Utils.toDashFormat=function(a){if("string"!=typeof a)return!1;a=a.match(/((?:[A-Z]?[a-z]+)|(?:[A-Z][a-z]*))/g);for(var b=0;b<a.length;b++)a[b]=a[b].toLowerCase();return a.join("-")};c.Utils.checkEmail=function(a){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(a)};
c.Utils.playSound=function(a){var b=d('audio[data-file="'+a+'"]');0<b.length?b.get(0).play():(b=d("<audio>",{autoplay:!0,style:"display: none"}).append('<source src="'+a+'.wav" type="audio/x-wav" /><source src="'+a+'.mp3" type="audio/mpeg" codecs="mp3" /><embed src="'+a+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />'),d("body").append(b),d.isFunction(b.get(0).play)&&b.attr("data-file",a))}})(Mibew,$);
