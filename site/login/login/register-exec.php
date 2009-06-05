<?php
	//Start session
	session_start();
	
	//Include database connection details
	require_once('libs/config.php');
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Validation error flag
	$errflag = false;
	
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link) {
		die('Failed to connect to server: ' . mysql_error());
	}
	
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db) {
		die("Unable to select database");
	}
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		if(get_magic_quotes_gpc()) {
			$str = stripslashes($str);
		}
		return mysql_real_escape_string($str);
	}
	
	//Sanitize the POST values
	$fname = clean($_POST['fname']);
	$lname = clean($_POST['lname']);
	$city = clean($_POST['city']);
    $state = clean($_POST['state']);
    $country = clean($_POST['country']);
    $phone = clean($_POST['phone']);
    $email = clean($_POST['email']);
	$login = clean($_POST['login']);
	$password = clean($_POST['password']);
	$cpassword = clean($_POST['cpassword']);
	
	//Input Validations
	if($fname == '') {
		$errmsg_arr[] = 'First name is missing';
		$errflag = true;
	}
	if($lname == '') {
		$errmsg_arr[] = 'Last name is missing';
		$errflag = true;
	}
    if($city == '') {
		$errmsg_arr[] = 'City is missing';
		$errflag = true;
	}
    if($state == '') {
		$errmsg_arr[] = 'State is missing';
		$errflag = true;
	}
    if($country == '') {
		$errmsg_arr[] = 'Country is missing';
		$errflag = true;
	}
    if($phone == '') {
		$errmsg_arr[] = 'Phone is missing';
		$errflag = true;
	}
     if($phone == '000-000-0000') {
		$errmsg_arr[] = 'Phone is invalid';
		$errflag = true;
	}
		if($email == '') {
		$errmsg_arr[] = 'Email is missing';
		$errflag = true;
	}
	if($login == '') {
		$errmsg_arr[] = 'Login ID is missing';
		$errflag = true;
	}
	
	if($password == '') {
		$errmsg_arr[] = 'Password is missing';
		$errflag = true;
	}
	if($cpassword == '') {
		$errmsg_arr[] = 'Confirm password is missing';
		$errflag = true;
	}
	if( strcmp($password, $cpassword) != 0 ) {
		$errmsg_arr[] = 'Passwords do not match';
		$errflag = true;
	}
	
	//Check for duplicate login ID
	if($login != '') {
		$qry = "SELECT * FROM members WHERE login='$login'";
		$result = mysql_query($qry);
		if($result) {
			if(mysql_num_rows($result) > 0) {
				$errmsg_arr[] = 'Login ID already in use';
				$errflag = true;
			}
			@mysql_free_result($result);
		}
		else {
			die("Query failed");
		}
	}


	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		session_write_close();
		header("location: register-form.php");
		exit();
	}

	//Create INSERT query
	$qry = "INSERT INTO members(firstname, lastname, city, state, country, phone, email, login, passwd) VALUES('$fname','$lname', '$city','$state', '$country', '$phone', '$email', '$login','".md5($_POST['password'])."')";
	$result = @mysql_query($qry);
	
	//Check whether the query was successful or not
	if($result) {
		header("location: register-success.php");
		exit();
	}else {
		die("Query failed");
	}
?>