<?php
/*
Template Name: Student List
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
				<div class="entry-content">
				<?php the_content(); ?>
				<?php

					//Creation pass flag
					$pass = 1;
					
					// Create connection
					$conn = mysqli_connect($servername, $username, $password, $dbname);
					// Check connection
					if (!$conn) {
						echo "Connection failed: " . mysqli_connect_error();
						$pass = 0;
					}
					
					$header_info = array(
								"Date Submitted" => 1
								,"Identifier" => 0
								,"Photo" => 1
								,"First Name" => 1
								,"Last Name" => 1
								,"Preferred Name" => 1
								,"Phone Number" => 1
								,"Text Ok" => 1
								,"Email" => 0
								,"Grade" => 0
								,"Birthday" => 0
								,"Last Contacted" => 0
							);
					$show_graduated = 0;
					
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						$header_info["Date Submitted"] = validate_input($conn,$_POST["date_submitted"]);
						$header_info["Identifier"] = validate_input($conn,$_POST["identifier"]);
						$header_info["First Name"] = validate_input($conn,$_POST["first_name"]);
						$header_info["Last Name"] = validate_input($conn,$_POST["last_name"]);
						$header_info["Preferred Name"] = validate_input($conn,$_POST["preferred_name"]);
						$header_info["Phone Number"] = validate_input($conn,$_POST["phone"]);
						$header_info["Text Ok"] = validate_input($conn,$_POST["textok"]);
						$header_info["Email"] = validate_input($conn,$_POST["email"]);
						$header_info["Grade"] = validate_input($conn,$_POST["grade"]);
						$header_info["Birthday"] = validate_input($conn,$_POST["birthday"]);
						$header_info["Photo"] = validate_input($conn,$_POST["show_photo"]);
						$header_info["Last Contacted"] = validate_input($conn,$_POST["last_contacted"]);
						$show_graduated = validate_input($conn,$_POST["show_graduated"]);
					}
					$user = wp_get_current_user();
					if (in_array(("administrator"),$user->roles)){
				?>
						<button name="grade_update" onclick=grade_update()>Update Grade Level</button>
					<?php 
					}
					?>
				<form method="post" action="<?php echo get_permalink(); ?>">
					<table style="max-width: 60%;margin: 20px;padding: 10px">
						<tr>
							<td>
								<input type="checkbox" name="date_submitted" <?php if($header_info["Date Submitted"]){echo "checked"; }?> > Date Submitted
							</td>
							<td>
								<input type="checkbox" name="identifier" <?php if($header_info["Identifier"]){echo "checked"; }?>> Identifier
							</td>
						
							<td>
								<input type="checkbox" name="first_name" <?php if($header_info["First Name"]){echo "checked"; }?>> First Name 
							</td>
						</tr>
						<tr>	
							<td>
								<input type="checkbox" name="last_name" <?php if($header_info["Last Name"]){echo "checked"; }?>> Last Name
							</td>
						
							<td>
								<input type="checkbox" name="preferred_name" <?php if($header_info["Preferred Name"]){echo "checked"; }?>> Preferred Name
							</td>
							<td>
								<input type="checkbox" name="phone" <?php if($header_info["Phone Number"]){echo "checked"; }?>> Phone Number
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="textok" <?php if($header_info["Text Ok"]){echo "checked"; }?>> Text OK
							</td>
							<td>
								<input type="checkbox" name="email" <?php if($header_info["Email"]){echo "checked"; }?>> Email
							</td>
							<td>
								<input type="checkbox" name="grade" <?php if($header_info["Grade"]){echo "checked"; }?>> Grade
							</td>
						</tr>
						<tr>
							<td>
								<input type="checkbox" name="birthday" <?php if($header_info["Birthday"]){echo "checked"; }?>> Birthday
							</td>
							<td>
								<input type="checkbox" name="last_contacted" <?php if($header_info["Last Contacted"]){echo "checked"; }?>> Last Contacted
							</td>
							<td>
								<input type="checkbox" name="show_graduated"<?php if($show_graduated){echo "checked"; }?>> Show Graduated
							</td>
							<td>
								<input type="checkbox" name="show_photo"<?php if($header_info["Photo"]){echo "checked"; }?>> Show Photo
							</td>
						</tr>
						
						<tr>
							<td colspan="2" style="text-align: center">
								<input type="submit" value="submit">
							</td>
						</tr>
					</table>
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
					<?php
						$photo_pos = 0;
						if ($header_info["Date Submitted"]){
							$cols[] = '`date_submitted`';
							$photo_pos += 1;
						}
						if ($header_info["Identifier"]){
							$cols[] = '`identifier`';
							$photo_pos += 1;
						}
						if ($header_info["Photo"]){
							$cols[] = '`Uploaded Image Path`';
						}
						if ($header_info["First Name"]){
							$cols[] = '`First Name`';
						}
						if ($header_info["Last Name"]){
							$cols[] = '`Last Name`';
						}
						if ($header_info["Preferred Name"]){
							$cols[] = '`Nickname`';
						}
						if ($header_info["Phone Number"]){
							$cols[] = '`Phone Number`';
						}
						if ($header_info["Text Ok"]){
							$cols[] = '`Text Ok`';
						}
						if ($header_info["Email"]){
							$cols[] = '`Email`';
						}
						if ($header_info["Grade"]){
							$cols[] = '`Grade`';
						}
						if ($header_info["Birthday"]){
							$cols[] = '`Birthday`';
						}
						if ($header_info["Last Contacted"]){
							$cols[] = '`Last Contacted`';
						}
						$sql = 'SELECT ' . implode(',',$cols) . ' FROM student_list';
						
						if (!$show_graduated){
							$sql .= ' WHERE Grade != "Graduated"';
						}
						
						$result = mysqli_query($conn,$sql);
						if (!$result){
							echo "<p>There was an error. Please try again</p>";
						}
						else {
							$data = get_result_as_arrays($result);
						}
						
						
					?>
					<table class="tablesorter resize sieve" style="width: auto;box-sizing: content-box">
						<thead>
							<tr>
								<?php
									if (current($header_info)){
										echo "<th>" . key($header_info) . "</th>";
									}
									
									
									foreach($header_info as $col){
										if (current($header_info)){
											echo "<th>" . key($header_info) . "</th>";
										}
										next($header_info);
									}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							
							for($row = 0; $row < count($data); $row++){
								echo "<tr>";
								for ($col = 0; $col < count($data[$row]); $col++){
									if ($header_info["Photo"] && $photo_pos == $col){
										echo '<td><img src="' . $data[$row][$col] . '" style="max-width:100px"></td>';
									}
									else{
										echo "<td>" . $data[$row][$col] . "</td>";
									}
								}
								echo "</tr>";
							}
							?>
						</tbody>
					</table>
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
		widgets: ["zebra","resizable","stickyHeaders"],
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

function grade_update() {
	confirm("This will take you to the page to update all students' grade levels. Are you sure you want to continue?");
	window.location.href = "http://campuslifeohs.com/?p=57";

}

$(document).ready(function() {
  $("table.sieve").sieve();
}); 
</script>
<?php get_footer(); ?>