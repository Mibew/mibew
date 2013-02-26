(function(Mibew, $) {
    $(document).ready(function() {
        $('#check-nv').click(function(){
            Mibew.Utils.playSound('../sounds/new_user.wav');
        });

        $('#check-nm').click(function() {
            Mibew.Utils.playSound('../sounds/new_message.wav');
        });
    });
})(Mibew, $);
