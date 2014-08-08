/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */
function loadNews(){if("undefined"!=typeof window.mibewNews&&"undefined"!=typeof window.mibewNews.length){for(var e="<div>",w=0;w<window.mibewNews.length;w++)e+='<div class="newstitle"><a href="'+window.mibewNews[w].link+'">'+window.mibewNews[w].title+'</a>, <span class="small">'+window.mibewNews[w].date+"</span></div>",e+='<div class="newstext">'+window.mibewNews[w].message+"</div>";$("#news").html(e+"</div>")}}function loadVersion(){if("undefined"!=typeof window.mibewLatest&&"undefined"!=typeof window.mibewLatest.version){var e=$("#cver").html();e!=window.mibewLatest.version?(e<window.mibewLatest.version&&$("#cver").css("color","red"),$("#lver").html(window.mibewLatest.version+', Download <a href="'+window.mibewLatest.download+'">'+window.mibewLatest.title+"</a>")):($("#cver").css("color","green"),$("#lver").html(window.mibewLatest.version))}}$(function(){loadNews(),loadVersion()});