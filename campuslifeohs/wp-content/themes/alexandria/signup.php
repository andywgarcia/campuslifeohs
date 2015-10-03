<?php
/*
Template Name: Signup
*/
?>
<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package alexandria
 */

require ABSPATH . 'my-includes/includes.php';
require $includes . 'qrcode.php';
 
get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php if ($_SERVER["REQUEST_METHOD"] == "POST") { 

					//Creation pass flag
					$pass = 1;
					
					// Create connection
					$conn = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$conn) {
						echo "Connection failed: " . mysqli_connect_error();
						$pass = 0;
					}
					//Set flags
					$uploadOk = 1;
					$dropboxCsvFlag = 1;
					$dropboxImageFlag = 1;
					$csvFlag = 1;
					$accountCreationFlag = 1;
					$error = 1;
					$validated = 1;
					$stupid_upload_var = 0;
					$image_paths_update_flag = 1;
					
				
					global $firstName;
					global $lastName;
					$firstName = ucfirst(validate_input($conn,$_POST['firstName']));
					$lastName = ucfirst(validate_input($conn,$_POST['lastName']));
					$nickName = ucfirst(validate_input($conn,$_POST['nickName']));
					$mobilePhone = validate_input($conn,$_POST['mobilePhone']);
					$primaryEmail = validate_input($conn,$_POST['primaryEmail']);
					$textok = validate_input($conn,$_POST['textok']);
					$grade = validate_input($conn,$_POST['grade']);
					$birthday = validate_input($conn,$_POST['birthday']);
					
					if (empty($firstName)){
						$firstNameErr = "First Name is Required";
						$validated = 0;
					}
					else {
						if (!preg_match("/^[a-zA-Z ]*$/",$firstName)){
							$firstNameErr = "Only letters are allowed";
							$validated = 0;
						}
					}
					if (empty($lastName)){
						$lastNameErr = "Last Name is Required";
						$validated = 0;
					}
					else {
						if (!preg_match("/^[a-zA-Z ]*$/",$lastName)) {
							$lastNameErr = "Only letters are allowed";
							$validated = 0;
						}
					}
					if (empty($mobilePhone)){
						$mobilePhoneErr = "Phone Number is Required";
						$validated = 0;
					}
					else {					
						$regex = "/\([0-9]{3}\)\s[0-9]{3}\-[0-9]{4}/"; //format (###) ###-####
						if (!preg_match($regex,$mobilePhone)){
							$mobilePhoneErr = "Invalid Phone Number";
							$validated = 0;
						}
					}
					if (empty($primaryEmail)){
						$primaryEmailErr = "Email is Required";
						$validated = 0;
					}
					else {
						if (!filter_var($primaryEmail, FILTER_VALIDATE_EMAIL)){
							$primaryEmailErr = "Invalid email format"; 
							$validated = 0;
						}
					}
					if (empty($birthday)){
						$birthdayErr = "Birthday is required";
						$validated = 0;
					}
					
					
					
					//Dropbox Stuff
					$accessToken = "mcWxFEgcVbIAAAAAAAACgctpLBLkmojYc8kXY4IJDgQvtBdKiPXaUBT5bRDoj9Mu";
					$appInfo = dbx\AppInfo::loadFromJsonFile($includes . "dropbox-sdk/Dropbox/app-info.json");
					$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
					$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
					$accountInfo = $dbxClient->getAccountInfo();
					
					//Grab current west coast time
					date_default_timezone_set('America/Los_Angeles');
					$date_format = "Y-n-j G:i:s";
					$timestamp = date($date_format);
					
					$sql = "INSERT INTO student_list 
									(`date_submitted`,`First Name`,`Last Name`,`Nickname`,`Phone Number`,`Email`,`Text Ok`,`Grade`,`Birthday`) 
									VALUES ('$timestamp', '$firstName', '$lastName', '$nickName', '$mobilePhone', '$primaryEmail', '$textok', '$grade', '$birthday')";
					
					//Success or fail
					if ($validated == 1){
						
						if (mysqli_query($conn, $sql)) {
							//echo "<p>New record created successfully</p>";
							
						} 
						else {
							if (mysqli_errno($conn) == 1062){
								echo "<p>The email $primaryEmail has already registered!</p>";
							}
							else{
								echo "<p>There was an error registration. Please try again.</p>";
								echo "<p>Error code: " . mysqli_errno($conn) . "</p>";
							}
							$pass = 0;
						}
						
					}
					else {
						$pass = 0;
					}
					
					if ($pass) {
						$sql = 'SELECT `identifier` FROM student_list WHERE Email="' . $primaryEmail . '"';
						if ($result = mysqli_query($conn,$sql)){
							$result = get_result_as_arrays($result);
							$identifier = $result[0][0];
						}
						else {
							$uploadOk = 0;
							$dropxboxImageFlag = 0;
							$error = 0;
						}
					}
					
					
					//	All photo upload code here
					if (is_uploaded_file($_FILES['uploadedFile']['tmp_name'])){
						//Grab filetype extension
						$imageFileType = image_type_to_extension(exif_imagetype($_FILES['uploadedFile']['tmp_name']));
						// Allow certain file formats
						if($imageFileType != ".jpg" && $imageFileType != ".png" && $imageFileType != ".jpeg"
						&& $imageFileType != ".gif" ) {
							echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
							$uploadOk = 0;
							$dropxboxImageFlag = 0;
							$error = 0;
						}
						//Write custom filename when it gets uploaded
						$dropboxImageName = str_replace(" ","-","/$firstName-$lastName-$identifier".$imageFileType);
						$pathToImage = ABSPATH . "student_pictures";
						$uploadedImage = $pathToImage . $dropboxImageName;
						$imageUrl = get_site_url() . "/student_pictures" . $dropboxImageName;
						
						$sql = 'UPDATE student_list SET `Dropbox Image Path`="' . $dropboxImageName . '",`Uploaded Image Path`="' . $imageUrl. '" WHERE identifier="' . $identifier. '"';
						if (!mysqli_query($conn,$sql)){
							$image_paths_update_flag = 0;
						}
						
					}
					if (is_uploaded_file($_FILES['uploadedFile']['tmp_name'])){
						$stupid_upload_var = 1;
						//Get wordpress file upload code
						require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						require_once(ABSPATH . "wp-admin" . '/includes/file.php');
						require_once(ABSPATH . "wp-admin" . '/includes/media.php');
						
						
						//Change the directory for upload
						add_filter('upload_dir','my_upload_dir');
						function my_upload_dir($upload){
							//$upload['subdir'] = '/student_pictures';
							//$upload['path']   = $upload['basedir'] . $upload['subdir'];
							//$upload['url']    = $upload['baseurl'] . $upload['subdir'];
							$upload['subdir'] = 'student_pictures';
							$upload['path']   = ABSPATH . $upload['subdir'];
							$upload['url']    = $upload['baseurl'] . $upload['subdir'];
							return $upload;
						}
						

						$check = getimagesize($_FILES["uploadedFile"]["tmp_name"]);

						if($check == false) {
							echo "<p>File is not an image.</p>";
							$uploadOk = 0;
							$error = 0;
							$dropxboxImageFlag = 0;
						}
						
						
						
						// Check if file already exists
						if (file_exists($uploadedImage) && $pass == 1) {
							unlink($uploadedImage);
						}
						
						//Not sure if this is ok to do, but it works
						$_FILES['uploadedFile']['name'] = $dropboxImageName;
						// Check if $uploadOk is set to 0 by an error
						if ($uploadOk == 1 && $pass == 1) {
							$upload_overrides = array('test_form' => false);
							//Upload the picture
							$move_success = wp_handle_upload($_FILES["uploadedFile"],$upload_overrides);
							if ($move_success['error'] != null) {
								echo "<p>There was an error uploading your image</p>";
								$uploadOk = 0;
								$error = 0;
								$dropxboxImageFlag = 0;
							}
						}
						remove_filter('upload_dir','my_upload_dir');
					}
					//Create CSV File of Database
					$output_file = $root . 'campus_life/student_list.csv';
					if (file_exists($output_file) && $pass == 1){
						unlink($output_file);
					}
					
					$sql = "
							SELECT 'date_submitted', 'identifier', 'firstName','lastName','nickName','mobilePhone','primaryEmail','custom1','custom2','custom3','importPhotoPath'
							UNION ALL
							SELECT `date_submitted`,`identifier`,`First Name`,`Last Name`,IFNULL(Nickname,''),`Phone Number`,`Email`,`Text Ok`,`Grade`,`Birthday`,`Dropbox Image Path` 
							FROM student_list
							WHERE `Grade`!='Graduated';";
							
							//INTO OUTFILE '$output_file'
							//FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"'
							//LINES TERMINATED BY '\n'
					if ($pass == 1){
						if (!($result = mysqli_query($conn, $sql))) {
							$csv_error_message = "Error Message: " . mysqli_error($conn) . "\n";
							$csv_error_no = "Error Number: " . mysqli_errno($conn) . "\n";
							$csvFlag = 0;
							$error = 0;
							$dropboxCsvFlag = 0;
						}
						else {
							//echo "<br><br> " . $sql . "<br><br>";
							if (!write_result_to_csv($output_file,$result)){
								$csv_error_message = "Error Message: Write result to csv failed \n";
								$csvFlag = 0;
								$error = 0;
								$dropboxCsvFlag = 0;
							}
							
							mysqli_free_result($result);
						}
					}
					
					mysqli_close($conn);
					
					//Update the dropbox
					if ($pass == 1){
						if ($csvFlag == 1){
							$dropbox_student_list = "/Apps/Attendance2/student_list.csv";
							$f = fopen($output_file,"rb");
							if ($f){
								$result = $dbxClient->uploadFile("/Apps/Attendance2/student_list.csv",dbx\WriteMode::force(),$f);
								fclose($f);
							}
							else {
								$error = 0;
								$dropboxCsvFlag = 0;
							}
						}
						if ($stupid_upload_var == 1){
							if ($uploadOk == 1){
								$f = fopen($uploadedImage,"rb");
								if ($f){
									$result = $dbxClient->uploadFile("/Apps/Attendance2/$dropboxImageName",dbx\WriteMode::force(),$f);
									fclose($f);
								}
								else {
									$dropboxImageFlag = 0;
									$error = 0;
								}
							}
						}
						else {
							//var_dump($_FILES['uploadedFile']);
							if (!empty($uploadedImage)){
								$dropboxImageFlag = 0;
							}
						}
					}

                    //Create the qrcode

					
					//Create the wordpress account
					if ($pass == 1){
						$username = $primaryEmail;
						if(username_exists($primaryEmail) == null) {

							// Generate the password and create the user
							$password = wp_generate_password( 12, false );
							$user_id = wp_create_user($username, $password, $primaryEmail );
							if ( is_wp_error($user_id)){
								echo "<p>".$user_id->get_error_message()."</p>";
								$accountCreationFlag = 0;
								$error = 0;
							}
							if ($accountCreationFlag == 1){
								// Set the nickname
								wp_update_user(
									array(
									  'ID'          	=>  $user_id,
									  'user_nicename' 	=>  $firstName.'-'.$lastName,
									  'display_Name'	=>	$firstname.' '.$lastName,
									  'nickname'    	=>  $nickName,
									  'first_name'		=>	$firstName,
									  'last_name'		=>	$lastName
									)
								);

								// Set the role
								$user = new WP_User( $user_id );
								$user->set_role( 'subscriber' );

								// Email the user
								$subject = "Welcome to Campus Life " . $firstName . "!";
								$message = "Thank you for signing up wth Campus Life at Oakmont High School!\n";
								$message .= "Your account on our website has automatically been created for you.\n";
								$message .= "Your username: " . $primaryEmail . "\n";
								$message .= "Your Password: " . $password . "\n";
								$message .= "We hope to see you Wednesdays during lunch and Mondays 2pm @ Cool River Pizza!\n";
								wp_mail($primaryEmail,$subject,$message);
							}
						} // end if
					}
					
					if ($pass == 1 && $error == 0) {
						$message = "$firstName $lastName has registered with errors\n";
						$message .= "Email: $primaryEmail\n";
						$message .= "Errors: \n";
						if ($uploadOk == 0){
							$message .= "- Image File Upload to server error\n";
						}
						if ($dropboxImageFlag == 0) {
							$message .= "- Image File Upload to Dropbox error\n";
						}
						if ($image_paths_update_flag == 0){
							$message .= "- User $identifer failed to update image paths\n";
						}
						if ($csvFlag == 0) {
							$message .= "- CSV creation error\n\n";
							$message .= $csv_error_no . $csv_error_message;
							$message .= "\n";
						}
						if ($dropboxCsvFlag == 0) {
							$message .= "- CSV Upload to Dropbox error\n";
						}
						if ($accountCreationFlag == 0) {
							$message .= "- Wordpress account creation error\n";
						}
						$subject = "New Student Registration Error";
						$webmaster_email = 'andygarcia@campuslifeohs.com';
						wp_mail($webmaster_email,$subject,$message);
						
					}
					if ($pass == 1){ ?>
						<p> Thank you for registering! You will receive an email with 
							all of the information you need! </p>
					<?php
					}
					
					}
				?>
				<?php 
				if ($_SERVER["REQUEST_METHOD"] != "POST" || $pass == 0){ 
				?>
              <style type="text/css">
					.error{
						padding: 5px 9px;
						border: 1px solid red;
						color: red;
						border-radius: 3px;
					}

					.success{
						padding: 5px 9px;
						border: 1px solid green;
						color: green;
						border-radius: 3px;
					}

					form span{
						color: red;
						white-space:nowrap;
					}
					table {
						margin: 10px;
						width: auto;
					}
					td {
						vertical-align: middle;
						/*border: 1px solid black;*/
					}
					#required {
						margin: 10px;
						color: red;
					}
					#details {
						text-align: right;
					}
					#inputs {
						text-align: left;
					}
				
				</style>
				<div id="respond">
					<?php echo $response; ?>
					<form method="post" action="<?php echo get_permalink(); ?>" enctype="multipart/form-data">
						
						<table>
							<tr><td id="details"><p id="required"><b>*</b> Required</p><tr>				
							<tr><td align="right" id="details"><span><b>*</b></span> First Name:</td> <td id="inputs"><input type="text" name="firstName" value="<?php echo $firstName; ?>"><?php if (!empty($firstNameErr)) { ?> <span class="error">* <?php echo $firstNameErr;?></span><?php } ?></td></tr>
							<tr><td id="details"><span><b>*</b></span> Last name: </td><td id="inputs"><input type="text" name="lastName" value="<?php echo $lastName; ?>"><?php if (!empty($lastNameErr)) { ?> <span class="error">* <?php echo $lastNameErr;?></span><?php } ?></td></tr>
							<tr><td id="details">Preferred Name: </td><td id="inputs"><input type="text" name="nickName" value="<?php echo $nickName; ?>"></td></tr>
							<tr><td id="details"><span><b>*</b></span> Phone Number: </td><td id="inputs"><input type="text" name="mobilePhone" class="phone_us" value="<?php echo $mobilePhone; ?>"><?php if (!empty($mobilePhoneErr)) { ?> <span class="error">* <?php echo $mobilePhoneErr;?></span><?php } ?></td></tr>
							<tr><td id="details"><span><b>*</b></span> Text OK: </td><td id="inputs"><label><input type="radio" name="textok" value="Yes" checked>Yes</label>
									 <label><input type="radio" name="textok" value="No" <?php if ($textok == 'No'){echo 'checked';} ?>>No</label></td></tr>
							<tr><td id="details"><span><b>*</b></span> Email: </td><td id="inputs"><input type="email" name="primaryEmail" value="<?php echo $primaryEmail; ?>"><?php if (!empty($primaryEmailErr)) { ?> <span class="error">* <?php echo $primaryEmailErr;?></span><?php } ?></td></tr>
							<tr><td id="details"><span><b>*</b></span> Grade: </td><td id="inputs">
											<select name="grade">
												<option value="Freshman" selected>Freshman</option>
												<option value="Sophomore" <?php if ($grade == 'Sophomore'){echo 'selected';} ?>>Sophomore</option>
												<option value="Junior" <?php if ($grade == 'Junior'){echo 'selected';} ?>>Junior</option>
												<option value="Senior" <?php if ($grade == 'Senior'){echo 'selected';} ?>>Senior</option>
											</select>
							</td></tr>
							<tr><td id="details"><span><b>*</b></span> Birthday: </td><td id="inputs"><input type="date" name="birthday" value="<?php echo $birthday; ?>"><?php if (!empty($birthdayErr)){ ?> <span class="error">* <?php echo $birthdayErr;?></span><?php } ?></td></tr>
							<tr><td id="details">Upload a Photo of Yourself:</td><td align="right"><input type="file" name="uploadedFile" id="uploadedFile" accept="image/*" capture="camera"></tr></td>
							<tr><td colspan="2" style="text-align: center"><input type="submit" value="submit"></td></tr>
						</table>
					</form>
				
				</div>
				<?php } ?>
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>