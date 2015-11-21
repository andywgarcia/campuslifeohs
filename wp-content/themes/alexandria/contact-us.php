<?php
/*
Template Name: Contact Us
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

require ABSPATH . 'my-includes/validators.php';
require ABSPATH . 'my-includes/sql_to_csv.php';
require ABSPATH . 'my-includes/recaptchalib.php';
 
get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php 
					$admin_email = 'andygarcia@campuslifeohs.com';
					$webmaster_email = 'andygarcia@campuslifeohs.com';
				
					if ($_SERVER["REQUEST_METHOD"] != "POST"){
				?>
						<style>
							.input {
								text-align: left;
								width: 140px;
							}
							.submit {
								text-align: center;
							}
						</style>
						<form method="post" action="<?php echo get_permalink(); ?>">
							<table>
								<tr><td class="input">Full Name:</td><td><input type="text" name="full_name"></tr></td>
								<tr><td class="input">Email:</td><td> <input type="text" name="email"></tr></td>
								<tr><td colspan="2"><textarea name="message" rows="7" cols="30" placeholder="Please enter your message here"></textarea></tr></td>
								
								<tr><td>
									<?php
									$publickey = "6LdnigYTAAAAAN5oEhvtqm4BUNN_0Up4cnpa7Fy7"; // You got this from the signup page.
									echo recaptcha_get_html($publickey);
									?>
									<div class="g-recaptcha" data-sitekey="6LdnigYTAAAAAN5oEhvtqm4BUNN_0Up4cnpa7Fy7"></div></tr></td>
								
								<tr><td class="submit" colspan="2"><input type="submit" value="submit"></tr></td>
							</table>
						
						</form>
				<?php 
					}
					
					else {
						
						 $privatekey = "6LdnigYTAAAAAFtJFRk7Rzs39f15nuhRqJog0C79";
						 $resp = recaptcha_check_answer ($privatekey,
														 $_SERVER["REMOTE_ADDR"],
														 $_POST["recaptcha_challenge_field"],
														 $_POST["recaptcha_response_field"]);
						if (!$resp->is_valid) {
							// What happens when the CAPTCHA was entered incorrectly
							die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
							"(reCAPTCHA said: " . $resp->error . ")");
						} 
						else {
						 
 
							$sender = $_POST['full_name'];
							$sender_email = $_POST['email'];
							$message = $_POST['message'];
							
							$subject = "Campus Life Contact Page";
							$body = '';
							if ($message) {
								if ($sender){
									$body .= "Sender: $sender\n";
								}
								else {
									$body .= "Sender: Anonymous\n";
								}
								
								if ($sender_email) {
									$body .= "Email: $sender_email\n";
								}
								else {
									$body .= "Email: Anonymous\n";
								}
								
								$body .= "$message\n\n";
							}
							if (!wp_mail($webmaster_email,$subject,$body)){
								echo "<p>There was an error processing your request. Please try again.</p>";
							}
							else { 
						
						
						
				?>
					
								<p>Thank you. We will respond as quickly as we can!</p>
								<p>Message: <?php echo $message; ?></p>
				<?php
							}
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