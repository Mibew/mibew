/**
 * @preserve This file is part of Mibew Messenger project.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2011 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

(function(Mibew){

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
     * @param {String} id Id of new window
     * @param {String} params Window params passed to window.open method
     */
    Mibew.Popup.open = function(link, id, params) {
        var newWindow = window.open(link, id, params);
        newWindow.focus();
        newWindow.opener = window;
    }

})(Mibew);