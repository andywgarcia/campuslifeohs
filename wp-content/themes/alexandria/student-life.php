<?php
/*
Template Name: Student Life
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
				<ul>
					<?php
						if ( is_user_logged_in() ) {
							$user = wp_get_current_user();
							if (in_array(("administrator"),$user->roles)){
								?>
								<li>
									<a href="http://campuslifeohs.com/edit-student/">Edit Student</a>
								</li>
							<?php 
							}
							if (in_array(("administrator"),$user->roles) || in_array(("editor"),$user->roles) || in_array(("author"),$user->roles)){
								?>
								<li>
									<a href="http://campuslifeohs.com/student-list/">Student List</a>
								</li>
								
								
								<?php
							}
							
						}
					?>
					<li>
						<a href="http://campuslifeohs.com/alumni/">Alumni</a>
					</li>
					<li>
						<a href="http://campuslifeohs.com/student-leaders/">Student Leaders</a>
					</li>
				</ul>
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>