<?php

	 $to = $_POST['email'];
	 $subj = "Forgotten Password Retrival";
	$password = $_POST['password'];
	$username= $_POST['username'];
	$message = "This message has been sent to you because a form was filled out saying you've forgotten your password.
	Your username:$username 
	Your new password:$password 
	click here to confirm the changes."
# mail($to, $subj, $message);
	?>