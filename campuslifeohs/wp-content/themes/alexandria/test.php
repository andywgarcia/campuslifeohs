<?php
/*
Template Name: TEST
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


require ABSPATH . 'my-includes/phpqrcode/qrlib.php';

get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php 
				$filename = ABSPATH . 'qr/newcl.png';
				QRcode::png("YAY!", $filename);
				
				
				$black_image = imagecreatetruecolor(300, 100);
				$black = imagecolorallocate($black_image, 0x00, 0xF0, 0x00);
				$fontfile = ABSPATH . "my-includes/fonts/ARIALUNI.ttf";
				if (file_exists($fontfile)){
					echo "File does exist<br>";
				}
				else {
					echo "File does not exist <br>";
				}
				//echo $fontfile . "<br>";
				$text = 'Andy Garcia';
				
				$bounds = imagettfbbox(12,0,$fontfile,$text);
				var_dump($bounds);
				
				$im = imagecreatefrompng($filename);
				$width=imagesx($im);
				$height=imagesy($im);
				$newwidth = 125;
				$newheight = 175;
				$output = imagecreate($newwidth, $newheight);
				imagecopy($output, $im, (($newwidth-$width)/2), 10, 0, 0, $width, $height);
				imagettftext($output, 12, 0, 10,10, $black, $fontfile, $text);
				
				imagepng($output,ABSPATH . 'qr/newerimage.png');
				imagedestroy($output);
				imagedestroy($im);
				
				
				echo "Site url: " . site_url() . "<br>";
				echo "ABSPATH: " . ABSPATH . "<br>";
				
				?>
				<img src="<?php echo ABSPATH . '/qr/newcl.png' . "<br>"; ?>" />
				<img src="<?php echo ABSPATH . '/qr/newerimage.png' . "<br>"; ?>" />
				
				<img src="<?php echo site_url() . '/qr/newcl.png'; ?>" />
				<img src="<?php echo site_url() . '/qr/newerimage.png'; ?>" />
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>