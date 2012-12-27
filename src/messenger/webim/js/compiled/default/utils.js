/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(c){c.Utils={};c.Utils.toUpperCaseFirst=function(a){return"string"!=typeof a?!1:""===a?a:a.substring(0,1).toUpperCase()+a.substring(1)};c.Utils.toDashFormat=function(a){if("string"!=typeof a)return!1;a=a.match(/((?:[A-Z]?[a-z]+)|(?:[A-Z][a-z]*))/g);for(var b=0;b<a.length;b++)a[b]=a[b].toLowerCase();return a.join("-")}})(Mibew);
