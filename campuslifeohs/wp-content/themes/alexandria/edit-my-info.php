<?php
/*
Template Name: Edit My Info
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
 
get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php
				if ( is_user_logged_in() ) {
					
					

					$pass = 1;
					$uploadOk = 1;
					$dropboxCsvFlag = 1;
					$dropboxImageFlag = 1;
					$csvFlag = 1;
					$accountCreationFlag = 1;
					$error = 1;
					$validated = 1;
					$stupid_upload_var = 0;
					// Create connection
					$conn = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$conn) {
						echo "Connection failed: " . mysqli_connect_error();
						$pass = 0;
					}
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						$firstName = ucfirst(validate_input($conn,$_POST['firstName']));
						$lastName = ucfirst(validate_input($conn,$_POST['lastName']));
						$nickName = ucfirst(validate_input($conn,$_POST['nickName']));
						$mobilePhone = validate_input($conn,$_POST['mobilePhone']);
						$primaryEmail = validate_input($conn,$_POST['primaryEmail']);
						$textok = validate_input($conn,$_POST['textok']);
						$grade = validate_input($conn,$_POST['grade']);
						$birthday = validate_input($conn,$_POST['birthday']);
						$identifier = validate_input($conn,$_POST['id']);
						
						if (!empty($firstName)){
							if (!preg_match("/^[a-zA-Z ]*$/",$firstName)){
								$firstNameErr = "Only letters are allowed";
								$validated = 0;
							}
						}
						if (!empty($lastName)){
							if (!preg_match("/^[a-zA-Z ]*$/",$lastName)) {
								$lastNameErr = "Only letters are allowed";
								$validated = 0;
							}
						}
						if (!empty($mobilePhone)){					
							$regex = "/\([0-9]{3}\)\s[0-9]{3}\-[0-9]{4}/"; //format (###) ###-####
							if (!preg_match($regex,$mobilePhone)){
								$mobilePhoneErr = "Invalid Phone Number";
								$validated = 0;
							}
						}
						/*if (!empty($birthday)){
							$regex = "/\d{2}\-\d{2}-\d{4}\/"; //format 00-00-0000
							if (!preg_match($regex,$birthday)){
								$birthdayErr = "Invalid Format (DD-MM-YYYY)";
								$validated = 0;
							}
						}*/
						if (!$validated){
							$pass = 0;
						}
						
						
						//Dropbox Stuff
						$accessToken = "mcWxFEgcVbIAAAAAAAACgctpLBLkmojYc8kXY4IJDgQvtBdKiPXaUBT5bRDoj9Mu";
						$appInfo = dbx\AppInfo::loadFromJsonFile("/home/clohs15/dropbox-sdk/Dropbox/app-info.json");
						$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
						$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
						$accountInfo = $dbxClient->getAccountInfo();
						
						//	All photo upload code here except file type and name
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
								}
							}
							remove_filter('upload_dir','my_upload_dir');
						}
						if ($pass){
						
							if ($firstName) {
								$setters[] = "`First Name`='$firstName'";
							}
							if ($lastName) {
								$setters[] = "`Last Name`='$lastName'";
							}
							if ($nickName) {
								$setters[] = "`Nickname`='$nickName'";
							}
							if ($mobilePhone) {
								$setters[] = "`Phone Number`='$mobilePhone'";
							}
							if ($primaryEmail) {
								$setters[] = "`Email`='$primaryEmail'";
							}
							if ($textok) {
								$setters[] = "`Text Ok`='$textok'";
							}
							if ($grade) {
								$setters[] = "`Grade`='$grade'";
							}
							if ($birthday) {
								$setters[] = "`Birthday`='$birthday'";
							}
							if ($last_contacted) {
								$setters[] = "`Last Contacted`='$last_contacted'";
							}
							
							$sql = "UPDATE student_list SET " . implode(',',$setters) . " WHERE `identifier`='" . $identifier	. "'";
							echo $sql;
							if ($result = mysqli_query($conn,$sql)){
								//Create CSV File of Database
								$output_file = '/home/clohs15/campus_life/student_list.csv';
								if (file_exists($output_file) && $pass == 1){
									unlink($output_file);
								}
								
								$sql = "
										SELECT 'date_submitted', 'identifier', 'firstName','lastName','nickName','mobilePhone','primaryEmail','custom1','custom2','custom3','importPhotoPath'
										UNION ALL
										SELECT `date_submitted`,`identifier`,`First Name`,`Last Name`,IFNULL(Nickname,''),`Phone Number`,`Email`,`Text Ok`,`Grade`,`Birthday`,`Dropbox Image Path` 
										FROM student_list
										WHERE `Grade`!='Graduated';";
								if (!($result = mysqli_query($conn, $sql))) {
									$csv_error_message = "Error Message: " . mysqli_error($conn) . "\n";
									$csv_error_no = "Error Number: " . mysqli_errno($conn) . "\n";
									$csvFlag = 0;
									$error = 0;
									$dropboxCsvFlag = 0;
								}
								else {
									if (!write_result_to_csv($output_file,$result)){
										$csv_error_message = "Error Message: Write result to csv failed \n";
										$csvFlag = 0;
										$error = 0;
										$dropboxCsvFlag = 0;
									}
									
									mysqli_free_result($result);
								}
								//Update the dropbox
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
								echo "Success! Returning... Please Wait";
								echo '<meta http-equiv="refresh" content="0; URL=http://campuslifeohs.com/?p=84">';
								
								
							}
								
							
							
							else {
									echo "<p style=\"color: red;margin: 20px\">There was an error retrieving the data. Please try again.</p>";
									echo "<p>Error code: " . mysqli_errno($conn) . "</p>";
									echo "<p>Error Message: " . mysqli_error($conn) . "</p>";
									$message = "There was an error processing the request. Please try again.";
									echo "<script type='text/javascript'>confirm('$message');</script>";
							}
						
						
						}
						else {
							echo "<p> An unknown error occurred. Please contact your administrator</p>";
						}
					}
					else {
						$user = wp_get_current_user();
						$primaryEmail = $user->user_login;
						$cols[] = "`identifier`";
						$cols[] = '`First Name`';
						$cols[] = '`Last Name`';
						$cols[] = '`Nickname`';
						$cols[] = '`Phone Number`';
						$cols[] = '`Text Ok`';
						$cols[] = '`Email`';
						$cols[] = '`Grade`';
						$cols[] = '`Birthday`';
						$cols[] = '`Uploaded Image Path`';
						$sql = "SELECT " . implode(',',$cols) . " FROM student_list WHERE Email='$primaryEmail'";
						$result = mysqli_query($conn,$sql);
						if (!$result){
							echo "<p>There was an error. Please try again</p>";
							echo "<p>Errorno: " . mysqli_errno($conn) . "</p>";
							echo "<p>Error Message: " . mysqli_error($conn) . "</p>";
						}
						else {
							$data = get_result_as_arrays($result);
						}
						
						$identifier = $data[0][0];
						$firstName = $data[0][1];
						$lastName = $data[0][2];
						$nickName = $data[0][3];
						$mobilePhone = $data[0][4];
						$textok = $data[0][5];
						$primaryEmail = $data[0][6];
						$grade = $data[0][7];
						$birthday = $data[0][8];
						$photo = $data[0][9];
						
						
						
					?>
					
						<style>
							table.edit {
								margin: 10px;
								width: auto;
							}
							table.edit td {
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
						<form method="post" action="<?php echo get_permalink(); ?>" enctype="multipart/form-data">
					
							<table class="edit">
								<tr><td align="right" id="details">Identifier:</td> <td id="inputs"><?php echo $identifier; ?></td></tr>
								<tr><td align="right" id="details">First Name:</td> <td id="inputs"><?php echo $firstName; ?></td></tr>
								<tr><td id="details">Last name:</td><td id="inputs"><?php echo $lastName; ?></td></tr>
								<tr><td id="details">Preferred Name:</td><td id="inputs"><input type="text" name="nickName" value="<?php echo $nickName; ?>"></td></tr>
								<tr><td id="details">Phone Number:</td><td id="inputs"><input type="text" name="mobilePhone" class="phone_us" value="<?php echo $mobilePhone; ?>"></td></tr>
								<tr><td id="details"> Text OK:</td><td id="inputs"><label><input type="radio" name="textok" value="Yes" checked>Yes</label>
										 <label><input type="radio" name="textok" value="No" <?php if ($textok == 'No'){echo 'checked';} ?>>No</label></td></tr>
								<tr><td id="details"> Email:</td><td id="inputs"><?php echo $primaryEmail; ?></td></tr>
								<tr><td id="details"> Grade:</td><td id="inputs">
														<select name="grade">
															<option value="Freshman" selected>Freshman</option>
															<option value="Sophomore" <?php if ($grade == 'Sophomore'){echo 'selected';} ?>>Sophomore</option>
															<option value="Junior" <?php if ($grade == 'Junior'){echo 'selected';} ?>>Junior</option>
															<option value="Senior" <?php if ($grade == 'Senior'){echo 'selected';} ?>>Senior</option>
															<option value="Graduated" <?php if ($grade == 'Graduated'){echo 'selected';} ?>>Graduated</option>
														</select></td></tr>
								<tr><td id="details"> Birthday:</td><td id="inputs"><input type="date" name="birthday" value="<?php echo $birthday; ?>"></td></tr>
								<tr><td id="details">Upload a Photo of Yourself:</td><td align="right"><?php if ($photo){ ?><img src="<?php echo $photo; ?>" style="max-width:100px"><?php } ?><input type="file" name="uploadedFile" id="uploadedFile" accept="image/*" capture="camera"></tr></td>
								<tr><td colspan="2" style="text-align: center"><input type="submit" value="submit"></td></tr>
							</table>
							<input type="hidden" name="id" value="<?php echo $identifier; ?>">
							<input type="hidden" name="id" value="<?php echo $firstName; ?>">
							<input type="hidden" name="id" value="<?php echo $lastName; ?>">
						</form>
						
				
							
							
								
				<?php
					}
				}
			?>
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>