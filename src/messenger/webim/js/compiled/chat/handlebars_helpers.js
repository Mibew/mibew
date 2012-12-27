/*
 This file is part of Mibew Messenger project.
 http://mibew.org

 Copyright (c) 2005-2011 Mibew Messenger Community
 License: http://mibew.org/license.php
*/
(function(b){b.registerHelper("allowTags",function(a){a=a.replace(/&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,"<$1>$2</$1>");a=a.replace(/&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,'<span class="$1">$2</span>');return new b.SafeString(a)})})(Handlebars);
