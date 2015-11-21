<?php
/*
Template Name: Edit Student
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

//require ABSPATH . 'my-includes/includes.php';
require '/my-includes/includes.php';
get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php
				if ( is_user_logged_in() ) {
					$user = wp_get_current_user();
					if (in_array(("administrator"),$user->roles)){

				
						// Create connection
						$conn = mysqli_connect($servername, $username, $password, $dbname);
						// Check connection
						if (!$conn) {
							echo "Connection failed: " . mysqli_connect_error();
							$pass = 0;
						}
						
						//Dropbox Stuff
						$accessToken = "mcWxFEgcVbIAAAAAAAACgctpLBLkmojYc8kXY4IJDgQvtBdKiPXaUBT5bRDoj9Mu";
						$appInfo = dbx\AppInfo::loadFromJsonFile($includes . 'dropbox-sdk/Dropbox/app-info.json');
						$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
						$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
						$accountInfo = $dbxClient->getAccountInfo();
						
						$error = 0;
						
						if ($_SERVER["REQUEST_METHOD"] == "POST") {
							$identifier = validate_input($conn,$_POST['identifier']);
							$firstName = ucfirst(validate_input($conn,$_POST['firstName']));
							$lastName = ucfirst(validate_input($conn,$_POST['lastName']));
							$nickName = ucfirst(validate_input($conn,$_POST['nickName']));
							$mobilePhone = validate_input($conn,$_POST['mobilePhone']);
							$primaryEmail = validate_input($conn,$_POST['primaryEmail']);
							$textok = validate_input($conn,$_POST['textok']);
							$grade = validate_input($conn,$_POST['grade']);
							$birthday = validate_input($conn,$_POST['birthday']);
							$last_contacted = validate_input($conn,$_POST['last_contacted']);
							
							if ($identifier) {
								$setters[] = "`identifier`='$identifier'";
							}
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
							
							$sql = "UPDATE student_list SET " . implode(',',$setters) . " WHERE `identifier`='" . $_POST['old_id'] . "'";
							
							
							
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
									echo "<p>Error Message: " . mysqli_error($conn) . "</p>";
									echo "<p>Error Number: " . mysqli_errno($conn) . "</p>";
									echo "<p>Basically, could not retrieve data to update the excel file of the students</p>";
									$error = 1;
								}
								else {
									if (!write_result_to_csv($output_file,$result)){
										echo "<p>Could not create the excel file of students<p>";
										$error = 1;
									}
									
									mysqli_free_result($result);
								}
								//Update the dropbox
								if (!$error){
									$dropbox_student_list = "/Apps/Attendance2/student_list.csv";
									$f = fopen($output_file,"rb");
									if ($f){
										$result = $dbxClient->uploadFile("/Apps/Attendance2/student_list.csv",dbx\WriteMode::force(),$f);
										fclose($f);
									}
									else {
										echo "<p>There was an error updating the dropbox, but the student was updated</p>";
										$error = 1;
									}
								}
								if ($error){
									echo "<p>Please try again, if the problem persists, please email the webmaster</p>";
								}
								else {
									echo "Success! Returning... Please Wait";
									echo '<meta http-equiv="refresh" content="0; URL=http://campuslifeohs.com/?p=49">';
								}
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
							if ($id = validate_input($conn,$_GET["id"])){
								$_SESSION['identifier'] = $id;
								$cols[] = "`identifier`";
								$cols[] = '`First Name`';
								$cols[] = '`Last Name`';
								$cols[] = '`Nickname`';
								$cols[] = '`Phone Number`';
								$cols[] = '`Text Ok`';
								$cols[] = '`Email`';
								$cols[] = '`Grade`';
								$cols[] = '`Birthday`';
								$cols[] = '`Last Contacted`';
								$sql = "SELECT " . implode(',',$cols) . " FROM student_list WHERE identifier='$id'";
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
								$last_contacted = $data[0][9];
								
								
								
								
								
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
										<tr><td align="right" id="details">Identifier:</td> <td id="inputs"><input type="text" name="identifier" value="<?php echo $identifier; ?>"></td></tr>
										<tr><td align="right" id="details">First Name:</td> <td id="inputs"><input type="text" name="firstName" value="<?php echo $firstName; ?>"></td></tr>
										<tr><td id="details"> Last name: </td><td id="inputs"><input type="text" name="lastName" value="<?php echo $lastName; ?>"></td></tr>
										<tr><td id="details">Preferred Name: </td><td id="inputs"><input type="text" name="nickName" value="<?php echo $nickName; ?>"></td></tr>
										<tr><td id="details"> Phone Number: </td><td id="inputs"><input type="text" name="mobilePhone" class="phone_us" value="<?php echo $mobilePhone; ?>"></td></tr>
										<tr><td id="details"> Text OK: </td><td id="inputs"><label><input type="radio" name="textok" value="Yes" checked>Yes</label>
												 <label><input type="radio" name="textok" value="No" <?php if ($textok == 'No'){echo 'checked';} ?>>No</label></td></tr>
										<tr><td id="details"> Email: </td><td id="inputs"><input type="email" name="primaryEmail" value="<?php echo $primaryEmail; ?>"></td></tr>
										<tr><td id="details"> Grade: </td><td id="inputs">
														<select name="grade">
															<option value="Freshman" selected>Freshman</option>
															<option value="Sophomore" <?php if ($grade == 'Sophomore'){echo 'selected';} ?>>Sophomore</option>
															<option value="Junior" <?php if ($grade == 'Junior'){echo 'selected';} ?>>Junior</option>
															<option value="Senior" <?php if ($grade == 'Senior'){echo 'selected';} ?>>Senior</option>
															<option value="Graduated" <?php if ($grade == 'Graduated'){echo 'selected';} ?>>Graduated</option>
														</select>
										</td></tr>
										<tr><td id="details"> Birthday: </td><td id="inputs"><input type="date" name="birthday"  value="<?php echo $birthday; ?>"></td></tr>
										<tr><td id="details"> Last Contacted: </td><td id="inputs"><input type="text" name="last_contacted" value="<?php echo $last_contacted; ?>"></td></tr>
										<!--<tr><td id="details">Upload a Photo of Yourself:</td><td align="right"><input type="file" name="uploadedFile" id="uploadedFile" accept="image/*" capture="camera"></tr></td>-->
										<tr><td colspan="2" style="text-align: center"><input type="submit" value="submit"></td></tr>
									</table>
									<input type="hidden" name="old_id" value="<?php echo $identifier?>">
								</form>
								
				<?php
							}
							else {
								$sql = "SELECT `identifier`,`First Name`,`Last Name`,`Email` FROM student_list";
								$result = mysqli_query($conn,$sql);
								if (!$result){
									echo "<p>There was an error. Please try again</p>";
									echo "<p>Errorno: " . mysqli_errno($conn) . "</p>";
									echo "<p>Error Message: " . mysqli_error($conn) . "</p>";
								}
								else {
									$data = get_result_as_arrays($result);
							
									$header_info = array("ID","First Name","Last Name","Email");
								
					?>
									<p>Who would you like to edit?</p>
									<style>
										table.tablesorter tbody tr.normal-row td {
										  background: #ffffff;
										  color: #000000;
										}
										table.tablesorter tbody tr.alt-row td {
										  background: #ababab;
										  color: #000000;
										}
									</style>
									<form method="get" action= "<?php echo get_permalink(); ?>">
										<table class="tablesorter resize sieve" style="width: auto;box-sizing: content-box">
											<thead>
												<tr>
													<?php
														echo "<th>Edit</th>";
														for ($i = 0; $i < count($header_info); $i++){
															echo "<th>" . $header_info[$i] . "</th>";
														}
													?>
												</tr>
											</thead>
											<tbody>
												<?php
												for($row = 0; $row < count($data); $row++){
													echo "<tr>";
													echo '<td style="width: 40px;text-align: center" >';
													echo "<input type='radio' name='id' value='" . $data[$row][0] . "'>";
													echo "</td>";
													for ($col = 0; $col < count($data[$row]); $col++){
														echo "<td>" . $data[$row][$col] . "</td>";
													}
													echo "</tr>";
												}
												?>
											</tbody>
											<tfoot>
												<tr>
													<td colspan="5" style="text-align: center">
														<input type="submit" value="submit">
													</td>
												</tr>
											</tfoot>
										</table>
									</form>
								
						<?php	
								}
							}
						}
						
						?>
					<?php 
						
					} 
				}
				else {
					echo "<p>You are not an administrator</p>";
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
<script type="text/javascript">
$(function(){
  $(".tablesorter").tablesorter({
		theme : 'blue',
		// initialize zebra striping and resizable widgets on the table
		widgets: ["zebra","resizable"],
		widgetOptions: {
			//resizable: true,
			storage_useSessionStorage : true,
			zebra : [ "normal-row", "alt-row" ],
		}
	});
});
$(function(){
  $(".resize").colResizable({
	liveDrag: true,
	postbackSafe: true,
	draggingClass:"dragging",
  
  });
});

$(document).ready(function() {
  $("table.sieve").sieve();
}); 

jQuery(document).ready(function($){
$('.date').mask('00/00/0000');
$('.time').mask('00:00:00');
$('.date_time').mask('00/00/0000 00:00:00');
$('.cep').mask('00000-000');
$('.phone').mask('0000-0000');
$('.phone_with_ddd').mask('(00) 0000-0000');
$('.phone_us').mask('(000) 000-0000', {placeholder: "(___) ___-____"});
$('.mixed').mask('AAA 000-S0S');
$('.ip_address').mask('099.099.099.099');
$('.percent').mask('##0,00%', {reverse: true});
$('.clear-if-not-match').mask("00/00/0000", {clearIfNotMatch: true});
$('.placeholder').mask("00/00/0000", {placeholder: "__/__/____"});
$('.fallback').mask("00r00r0000", {
  translation: {
	'r': {
	  pattern: /[\/]/, 
	  fallback: '/'
	}, 
	placeholder: "__/__/____"
  }
});

$('.selectonfocus').mask("00/00/0000", {selectOnFocus: true});

$('.cep_with_callback').mask('00000-000', {onComplete: function(cep) {
	console.log('Mask is done!:', cep);
  },
   onKeyPress: function(cep, event, currentField, options){
	console.log('An key was pressed!:', cep, ' event: ', event, 'currentField: ', currentField.attr('class'), ' options: ', options);
  },
  onInvalid: function(val, e, field, invalid, options){
	var error = invalid[0];
	console.log ("Digit: ", error.v, " is invalid for the position: ", error.p, ". We expect something like: ", error.e);
  }
});

$('.crazy_cep').mask('00000-000', {onKeyPress: function(cep, e, field, options){
  var masks = ['00000-000', '0-00-00-00'];
	mask = (cep.length>7) ? masks[1] : masks[0];
  $('.crazy_cep').mask(mask, options);
}});

$('.cpf').mask('000.000.000-00', {reverse: true});
$('.money').mask('#.##0,00', {reverse: true});

var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
},
spOptions = {
  onKeyPress: function(val, e, field, options) {
	  field.mask(SPMaskBehavior.apply({}, arguments), options);
	}
};

$('.sp_celphones').mask(SPMaskBehavior, spOptions);

$(".bt-mask-it").click(function(){
  $(".mask-on-div").mask("000.000.000-00");
  $(".mask-on-div").fadeOut(500).fadeIn(500)
})

$('pre').each(function(i, e) {hljs.highlightBlock(e)});
});


</script>
<?php get_footer(); ?>