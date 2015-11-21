<?php
/*
Template Name: Alumni
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

require ABSPATH . "my-includes/includes.php";

get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php 

					// Create connection
					$conn = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$conn) {
						echo "Connection failed: " . mysqli_connect_error();
					}
					$sql = "SELECT `First Name`,`Last Name`,`identifier` FROM `student_list` WHERE Grade='Graduated' ORDER BY `Last Name`";
					$result = mysqli_query($conn,$sql);
					if (!$result){
						echo "<p style=\"color: red;margin: 20px\">There was an error retrieving the data. Please try again.</p>";
						$data = null;
					}
					else {
						$data = get_result_as_arrays($result);
					}
					
						
				?>
				<table>
					<?php
					$num_cols = 4;
					for ($person = 0; $person < count($data); $person++){
						if ($person % $num_cols == 0 || $person == 0){
							echo "<tr>";
						}
						echo '<td style="text-align: center">';
							if (file_exists(ABSPATH . 'student_pictures/' . $data[$person][0] . '-' . $data[$person][1] . '-' . $data[$person][2] . '.jpeg')){
								echo '<img src="' . site_url() . '/student_pictures/' . $data[$person][0] . '-' . $data[$person][1] . '-' . $data[$person][2] . '.jpeg"' . 
									' style="width: 150px;height: auto"><br>';
							}
							echo $data[$person][0];
							echo "<br>";
							echo $data[$person][1];
						echo "</td>";
						if ($person % $num_cols == 1 || $person == (count($data) - 1)){
							echo "</tr>";
						}
					}
					?>
						
						
				</table>
				
				
				
				<?php
					mysqli_close($conn);
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