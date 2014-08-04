/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
!function(t,e){t.Utils={},t.Utils.toUpperCaseFirst=function(t){return"string"!=typeof t?!1:""===t?t:t.substring(0,1).toUpperCase()+t.substring(1)},t.Utils.toDashFormat=function(t){if("string"!=typeof t)return!1;for(var e=t.match(/((?:[A-Z]?[a-z]+)|(?:[A-Z][a-z]*))/g),a=0;a<e.length;a++)e[a]=e[a].toLowerCase();return e.join("-")},t.Utils.checkEmail=function(t){return/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(t)},t.Utils.playSound=function(t){var a=e('audio[data-file="'+t+'"]');if(a.length>0)a.get(0).play();else{var i=e("<audio>",{autoplay:!0,style:"display: none"}).append('<source src="'+t+'.wav" type="audio/x-wav" /><source src="'+t+'.mp3" type="audio/mpeg" codecs="mp3" /><embed src="'+t+'.wav" type="audio/x-wav" hidden="true" autostart="true" loop="false" />');e("body").append(i),e.isFunction(i.get(0).play)&&i.attr("data-file",t)}}}(Mibew,$);