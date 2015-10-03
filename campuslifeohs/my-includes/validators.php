<?php

//use mysql_real_escape_sctring()
function validate_input($conn,$data) {
	$data = mysqli_real_escape_string($conn,$data);
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

$firstName = "";
$lastName = "";
$nickName = "";
$mobilePhone = "";
$textok = "";
$primaryEmail = "";
$lunchPeriod = "";
$birthday = "";

$firstNameErr = "";
$lastNameErr = "";
$mobilePhoneErr = "";
$primaryEmailErr = "";
$birthdayErr = "";
?>