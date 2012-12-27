/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Handlebars){

   /**
    * Register 'allowTags' Handlebars helper.
    *
    * This helper unescape HTML entities for allowed (span and strong) tags.
    */
    Handlebars.registerHelper('allowTags', function(text) {
        var result = text;
        result = result.replace(
            /&lt;(span|strong)&gt;(.*?)&lt;\/\1&gt;/g,
            '<$1>$2</$1>'
        );
        result = result.replace(
            /&lt;span class=&quot;(.*?)&quot;&gt;(.*?)&lt;\/span&gt;/g,
            '<span class="$1">$2</span>'
        );
        return new Handlebars.SafeString(result);
    });

})(Handlebars);