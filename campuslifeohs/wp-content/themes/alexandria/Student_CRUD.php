<?php
/*
Template Name: Studnet CRUD
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


require 'my-includes/xcrud/xcrud.php';
$xcrud = Xcrud::get_instance();
get_header(); ?>



		<?php
			
				if ( is_user_logged_in() ) {
					if (in_array(("administrator"),wp_get_current_user()->roles)){

					$xcrud->table('student_list');
					$xcrud->label('date_submitted','Joined');
					$xcrud->label('First Name','First');
					$xcrud->label('Last Name','Last');
					$xcrud->label('Phone Number','Cell');
					//$xcrud->change_type('Uploaded Image Path', 'remote_image', '', array('width'=>300, 'link'=>site_url() ));


					    $xcrud->change_type('Uploaded Image Path', 'image', false, array(
				        'width' => 450,
				        'path' => '/../../../student_pictures/',
				        'thumbs' => array(array(
				                'height' => 55,
				                'width' => 120,
				                'crop' => true,
				           			))));

					$xcrud->columns('identifier, Nickname, Dropbox Image Path, Uploaded Image Path, Last Contacted', true); //hide these in list view
					
					echo $xcrud->render();

					echo dirname(__FILE__);


					}else {
					echo "<p>You are not an administrator</p>";
					}
				}

	
		?>
<style>
h1.entry-title {
    display: none;
}
</style>

<?php //get_footer(); ?>