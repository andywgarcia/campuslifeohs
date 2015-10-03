<?php
include '../functions.php';
//conect to the database
require_once('../../wp-config.php');


if (empty($_POST)){
	exit('no form data');
}
	
	$firstName = trim($_POST['firstName']);
	//first we run it through  checkLetters() for lengh and regex 
	$firstNameCheck = checkLetters($firstName);
	if (!$firstNameCheck == 1){
		exit($firstNameCheck);
	}
	//becuase we have healthy paranoia we are going let PHP have a short at it too
	$firstName = ucfirst(filter_var($firstName, FILTER_SANITIZE_STRING));
	//and now the rest

	//LASTNAME
	$lastName = trim($_POST['lastName']);
	$lastNameCheck = checkLetters($lastName);
	if (!$lastNameCheck == 1){
		exit($lastNameCheck);
	}
	$lastName = ucfirst(filter_var($lastName, FILTER_SANITIZE_STRING));
	
	//NICKNAME
	$nickName = trim($_POST['nickName']);
	$nickNameCheck = checkLetters($nickName);
	if (!$nickNameCheck == 1){
		exit($nickNameCheck);
	}
	$nickName = ucfirst(filter_var($nickName, FILTER_SANITIZE_STRING));
	
	//EMAIL
	$email = trim($_POST['email']);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  		exit ("Invalid email");
  	}
  	
  	//PHONE NUMBER
  	$mobilePhone = trim($_POST['mobilePhone']);
	$mobilePhoneCheck = checkNumber($mobilePhone);
	if (!$checkNumberCheck == 1){
		exit($checkNumberCheck);
	}
	$mobilePhone = filter_var(mobilePhone, FILTER_SANITIZE_STRING);

  	//TEXT OK
  	//If they want to screw with the submit page thats thier problem. It ain't going in our database
  	$textOk = trim($_POST['textok']);
  	if ($textOK == 1){
  		$textOK = 'yes';
  	} else {
  		$text == 'no';
  	}
  	
  	//GRADE
  	//Remeber don't trust the user, if you don't have to give them db asscess don't do it
  	$formGrade  = trim($_POST['grade']);
  	switch ($formGrade) {
    	case "Freshmen": //*Freshmeat
	        $grade = "Freshmen";
	        break;
    	case "Sophomore":
	        $grade = "Sophomore";
	        break;
    	case "Junior":
	        $grade = "Junior";
	        break;
    	case "Senior":
	        $grade = "Senior";
	        break;
	}
	//BIRTHDAY
	$birthday = trim($_POST['birthday']);
	if (!preg_match('(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9]{2}', $birthday)){
		exit ('Invalid Birthday Day');
	}

	

//Make a new Record in student tabled
 $results = $wpdb->insert( 'student_list', array( 
		'First Name'   => $firstName, 
		'Last Name'    => $lastName,
		'Nickname'     => $nickName,
		'Phone Number' => $mobilePhone,
		'Email'		   => $email,
		'Text Ok'      => $text,
		'Grade'        => $grade,
		'Birthday'     => $birthday
	)
);

//wp_create_user( $username, $password, $email ); 

var_dump($results);

echo "1";
?>
