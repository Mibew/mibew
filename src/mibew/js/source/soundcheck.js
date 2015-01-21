/**
 * @preserve This file is a part of Mibew Messenger.
 * http://mibew.org
 * 
 * Copyright (c) 2005-2015 Mibew Messenger Community
 * License: http://mibew.org/license.php
 */

Behaviour.register({
	'a#check-nv' : function(el) {
		el.onclick = function() {
			playSound(wroot + '/sounds/new_user.wav');
		};
	},
	'a#check-nm' : function(el) {
		el.onclick = function() {
			playSound(wroot + '/sounds/new_message.wav')
		};
	}
});
