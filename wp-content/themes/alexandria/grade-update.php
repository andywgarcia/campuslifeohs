<?php
/*
Template Name: Grade Update
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
<script>
function grade_update(){
	if (confirm("This will move every student up one grade. This action cannot be undone. Are you sure you want to continue?")){
		input.submit();
	}
	else {
		return false;
	}
}
</script>
	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php
				if ($_SERVER["REQUEST_METHOD"] == "POST") {
				

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
							$sql = "UPDATE student_list SET Grade='Graduated' WHERE Grade='Senior';";
							$sql .= "UPDATE student_list SET Grade='Senior' WHERE Grade='Junior';";
							$sql .= "UPDATE student_list SET Grade='Junior' WHERE Grade='Sophomore';";
							$sql .= "UPDATE student_list SET Grade='Sophomore' WHERE Grade='Freshman';";
							echo $sql;
							
							$pass = 1;
							if ($result = mysqli_multi_query($conn,$sql)){
								do {
									if (!$result){
										echo "<p style=\"color: red;margin: 20px\">There was an error retrieving the data. Please try again.</p>";
										$message = "There was an error processing the request. Please try again.";
										echo "<script type='text/javascript'>confirm('$message');</script>";
										$pass = 0;
									}
									
									$result = mysqli_next_result($conn);
								} while (mysqli_more_results($conn));
							}
							if ($pass) {
								echo "Success! Returning... Please Wait";
							}
						}
					}
					echo '<meta http-equiv="refresh" content="0; URL=http://campuslifeohs.com/?p=49">';
				}
				else {
				?>
					<form method="post" action= "<?php echo get_permalink(); ?>">
						<input type="submit" value="Update Grade Level" onclick=grade_update() >
					</form>
				<?php 
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