/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function(Mibew, $){

    /**
     * Total height of elements exclude #messages-region or false if it is not
     * calculated yet
     * @type Number|Boolean
     */
    var delta = false;

    /**
     * Timer id
     * @type Number
     */
    var t;

    /**
     * Stretch #messages-region to fill the window
     */
    var updateHeight = function() {
        if ($('#messages-region').size() == 0) {
            return;
        }
        // Create shortcut for #messages-region
        var $msgs = $('#messages-region');
        var $ava = $('#avatar-region');

        // Calculate delta
        if (delta === false) {
            var max = 0;
            $('body > *').each(function() {
                var $el = $(this);
                var pos = $el.offset();
                var height = $el.height();
                if (max < (pos.top + height)) {
                    max = pos.top + height
                }
            });
            delta = max - $msgs.height();
        }

        // Check new height
        var newHeight = $(window).height() - delta;

        if (newHeight < parseInt($msgs.css('minHeight'))) {
            return;
        }

        // Update messages region height
        $msgs.height(newHeight);

        // Update avatar region height
        if ($ava.size() > 0) {
            $ava.height($msgs.innerHeight());
        }
    }

    /**
     * Fix bug with window resize event
     */
    var updateHeightWrapper = function() {
        if (t) {
            clearTimeout(t);
        }
        t = setTimeout(updateHeight, 0);
    }

    // Stretch messages region after chat page initialize
    Mibew.Application.Chat.addInitializer(function() {
        /**
         * Contains total count of images on the page
         * @type Number
         */
        var totalImagesCount = $('img').size();

        /**
         * Contains count of loaded images
         * @type Number
         */
        var imagesLoaded = 0;

        /**
         * Run then image loaded. If all images loaded run resizer.
         * @type Function
         */
        var imageLoadCallback = function() {
            imagesLoaded++;
            if (totalImagesCount == imagesLoaded) {
                updateHeight();
                // Scroll messages region to bottom
                $('#messages-region').scrollTop(
                    $('#messages-region').prop('scrollHeight')
                );
                // Stretch messages region on resize
                $(window).resize(updateHeightWrapper);
            }
        }

        // Change size of message region only after all images will be loaded
        $('img').each(function(){
            // Shortcut for current image element
            var $el = $(this);
            // Check if image already loaded and cached.
            // Cached image have height and do not triggers load event.
            if ($el.height() > 0) {
                imageLoadCallback();
            } else {
                $el.load(imageLoadCallback);
            }
        });
    });

})(Mibew, jQuery);