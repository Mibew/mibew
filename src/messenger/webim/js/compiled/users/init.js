/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(a,e){a.Regions={};a.Popup={};a.Popup.open=function(b,a,g){b=window.open(b,a,g);b.focus();b.opener=window};a.Utils.updateTimers=function(a,f){a.find(f).each(function(){var a=e(this).data("timestamp");if(a){var c=Math.round((new Date).getTime()/1E3)-a,a=c%60,b=Math.floor(c/60)%60,c=Math.floor(c/3600),d=[];0<c&&d.push(c);d.push(10>b?"0"+b:b);d.push(10>a?"0"+a:a);e(this).html(d.join(":"))}})}})(Mibew,jQuery);
