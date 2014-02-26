/**
 * @preserve Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may obtain a copy of the License at
 *     http://www.apache.org/licenses/LICENSE-2.0
 */

(function($) {
    function updateParentGroup() {
        if ($("#parentgroup").val() == '') {
            $("#extrafields").show();
        } else {
            $("#extrafields").hide();
        }
    }

    $(function() {
        $("#parentgroup").change(function() {
            updateParentGroup();
        });
        updateParentGroup();
    });
})(jQuery);
