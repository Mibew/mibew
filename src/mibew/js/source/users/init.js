/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, $){

    /**
     * @namespace Holds application region constructors
     */
    Mibew.Regions = {};

    /**
     * @namespace Holds popup windows control
     */
    Mibew.Popup = {};

    /**
     * Open new window
     * @param {String} link URL address of page to open
     * @param {String} id ID of new window. Value of the ID can contain only
     * alphanumeric characters and underscore sign. Any other characters will
     * be stripped. It helps to avoid problems with popup windows in IE7-9.
     * @param {String} params Window params passed to window.open method
     */
    Mibew.Popup.open = function(link, id, params) {
        // Filter window ID to avoid problems in IE7-9
        id = id.replace(/[^A-z0-9_]+/g, '');
        var newWindow = window.open(link, id, params);

        newWindow.focus();
        newWindow.opener = window;
    }

    /**
     * Update time in timers
     * @param {Object} $el jQuery DOM object
     * @param {String} selector Selector string
     */
    Mibew.Utils.updateTimers = function($el, selector) {
        $el.find(selector).each(function(){
            // Get timestamp
            var timestamp = $(this).data('timestamp');
            if (! timestamp) {
                return;
            }
            // Get time diff
            var diff = Math.round((new Date()).getTime() / 1000) - timestamp;
            // Get time parts
            var seconds = diff % 60;
            var minutes = Math.floor(diff / 60) % 60;
            var hours = Math.floor(diff / (60 * 60));
            // Get result parts
            var result = [];
            if (hours > 0) {
                result.push(hours);
            }
            result.push(minutes < 10 ? '0' + minutes : minutes);
            result.push(seconds < 10 ? '0' + seconds : seconds);
            // Build result string
            $(this).html(result.join(':'));
        });
    }

})(Mibew, jQuery);