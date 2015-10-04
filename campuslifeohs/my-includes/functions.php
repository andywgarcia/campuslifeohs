<?php


//use mysql_real_escape_sctring()
function validate_input($conn,$data) {
	$data = mysqli_real_escape_string($conn,$data);
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

//check if only letters and lenght
function checkLetters($text)
{
	if (strlen($text) <= 1){
		return 'Name to short';
	}
	if (!preg_match("/^[a-zA-Z ]*$/",$text)){
			return "Only letters are allowed";
	}
	return 1;
}


function checkNumber($number){
	if (strlen($number) <= 13){
		return 'Check your phone number';
	}
						
	$regex = "/\([1-9]{3}\)\s[1-9]{3}\-[0-9]{4}/"; //format (###) ###-####
	if (!preg_match($regex,$number)){
		$mobilePhoneErr = "Check your phone number, format (###) ###-####";
	}
	return 1;
}

?>