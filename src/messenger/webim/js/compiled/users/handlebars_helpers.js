/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(e){e.registerHelper("formatTimeSince",function(b){var a=Math.round((new Date).getTime()/1E3)-b;b=a%60;var d=Math.floor(a/60)%60,a=Math.floor(a/3600),c=[];0<a&&c.push(a);c.push(10>d?"0"+d:d);c.push(10>b?"0"+b:b);return c.join(":")})})(Handlebars);
