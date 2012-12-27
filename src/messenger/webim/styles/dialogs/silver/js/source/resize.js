(function($){

    var $msgRegion = null;
    var $avatarRegion = null;
    var t;

    var getHeight = function() {
        var elementsHeight = 0;
        $('body > *').not('#chat').each(function () {
            elementsHeight += $(this).outerHeight(true);
        });

        elementsHeight += ($('#chat').outerHeight(true)
            - $('#messages-region').innerHeight());

        return ($(window).height() - elementsHeight);
    }

    var updateHeight = function() {
        if ($msgRegion == null) {
            $msgRegion = $('#messages-region');
            $avatarRegion = $('#avatar-region');
        }
        if ($msgRegion.size() == 0) {
            return;
        }
        var newHeight = getHeight();
        var minHeight = parseInt($msgRegion.css('minHeight'));

        if (minHeight >= newHeight) {
            newHeight = minHeight;
        }
        $msgRegion.innerHeight(newHeight);
        $avatarRegion.innerHeight(newHeight);
    }

    var updateHeightWrapper = function() {
        if (t) {
            clearTimeout(t);
        }
        t = setTimeout(updateHeight, 0);
    }

    $(document).ready(updateHeight);
    $(window)
        .load(function() {
            updateHeight();
            // Scroll messages region to bottom
            $('#messages-region').scrollTop(
                $('#messages-region').prop('scrollHeight')
            );
        })
        .resize(updateHeightWrapper);

})($);