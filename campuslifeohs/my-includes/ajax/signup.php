<?php
include '../functions.php';
//conect to the database
require_once('../../wp-config.php');


if (empty($_POST)){
	exit('no form data');
}
	//GET THEM IN THE STUDENT TABLE

	//note to andy, for the next database you make. Google: TIMESTAP MYSQL
	//Grab current west coast time
	date_default_timezone_set('America/Los_Angeles');
	$date_format = "Y-n-j G:i:s";
	$timestamp = date($date_format);
					
	
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
	$email = trim($_POST['primaryEmail']);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  		exit ("Invalid email");
  	}
  	
  	//PHONE NUMBER
  	$mobilePhone = trim($_POST['mobilePhone']);
	$mobilePhoneCheck = checkNumber($mobilePhone);
	if (!$mobilePhoneCheck == 1){
		exit($mobilePhoneCheck);
	}
	$mobilePhone = filter_var($mobilePhone, FILTER_SANITIZE_STRING);

  	//TEXT OK
  	//If they want to screw with the submit page thats thier problem. It ain't going in our database
  	$textOK = trim($_POST['textok']);
  	if ($textOK == 'Yes'){
  		$text = 'Yes';
  	} else {
  		$text == 'No';
  	}
  	
  	//GRADE
  	//Remeber don't trust the user, if you don't have to give them db asscess don't do it
  	switch (trim($_POST['grade'])) {
    	case 'Freshman': //*Freshmeat
	        $grade = "Freshman";
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
	if ( !preg_match( "/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $birthday) )
	{ 
    exit ('Invalid Birthday Day');
	}

//CHECK IF EMAIL TAKEN
$emailCheck = $wpdb->get_row( "SELECT 'email' FROM student_list WHERE email = '$email'" );
if (!$emailCheck == NULL){
	exit('That email is already taken');
}

//Make a new Record in student tabled
$results = $wpdb->insert( 'student_list', array( 
		'date_submitted' => $timestamp,
		'First Name'     => $firstName, 
		'Last Name'      => $lastName,
		'Nickname'       => $nickName,
		'Phone Number'   => $mobilePhone,
		'Email'		     => $email,
		'Text Ok'        => $text,
		'Grade'          => $grade,
		'Birthday'       => $birthday
	), array( 
		'%s',  //database types, s for string
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s',
		'%s'
	) 
);
$studentID = $wpdb->insert_id;
if(!$studentID > 100){
	exit("Sorry thier seems to be a problem with our database...");
};

//CREATE A WORDPRESS USER
$password = wp_generate_password( 12, false);
$user_id = wp_create_user( $email, $password, $email ); 

if (!$user_id > 100){
	exit('Sorry, we couldn\'t create a account for you.');
}

$user = new WP_User( $user_id );
$user->set_role( 'subscriber' );

wp_update_user(
	array(
	
	 'user_nicename' 	=>  $firstName.'-'.$lastName,
	 'display_Name'  	=>	$firstName.' '.$lastName,
	 'nickname'     	=>  $nickName,
	 'first_name'		=>	$firstName,
	 'last_name'		=>	$lastName
	)
);

// Email the user
	$subject = "Welcome to Campus Life!"; //Putting name in the subject increases spam points
	$message  = $firstName . "\n";
	$message .=  "Thank you for signing up wth Campus Life at Oakmont High School!\n";
	$message .= "Your account on our website has automatically been created for you.\n";
	$message .= "Your username: " . $email . "\n";
	$message .= "Your Password: " . $password . "\n"; //ugh
	$message .= "We hope to see you Wednesdays during lunch and Mondays 2pm @ Cool River Pizza!\n";
	wp_mail($email,$subject,$message);
//
echo "1";

?>
