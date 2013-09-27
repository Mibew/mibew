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
