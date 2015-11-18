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
				function createQrPng($firstName,$lastName,$id){
					$filename = ABSPATH . 'qr/' . $id . '-' . $firstName . '-' . $lastName . '.png';
					QRcode::png($id, $filename);
					
					//create the blank image
					$im = imagecreatefrompng($filename);

					//setting up putting text on the image
					$fontfile = ABSPATH . "my-includes/fonts/ARIALUNI.ttf";
					$text = $firstName . " " . $lastName;
					$font_size = 12;
					$bounds = imagettfbbox($font_size,0,$fontfile,$text);
					
					//can't remember what this does. I think it sets up the canvas with the size
					$width=imagesx($im);
					$height=imagesy($im);
					$newwidth = 400;
					$newheight = 600;
					$bordersize = 1;
					$wwidth = $newwidth - 2*$bordersize;
					$wheight = $newheight - 2*$bordersize;
					$white_space = imagecreatetruecolor($wwidth,$wheight);
					$output = imagecreatetruecolor($newwidth, $newheight);
					$white = imagecolorallocate($output, 255, 255, 255);
					$black = imagecolorallocate($output, 0,0,0);
					if (!imagefill($output,0,0,$black)) {
						echo "Image fill failed <br>";
					}
					if (!imagefill($white_space,0,0,$white)) {
						echo "Image fill failed <br>";
					}

					//overlay png image of qr code and text
					imagecopy($output,$white_space,$bordersize,$bordersize,0,0,$wwidth,$wheight);
					imagettftext($output, 12, 0, 20,130, $black, $fontfile, $text);
					imagecopy($output, $im, (($newwidth-$width)/2), 10, 0, 0, $width, $height);

					//create the final image
					imagepng($output,ABSPATH . 'qr/newerimage.png');
					imagepng($im, ABSPATH. 'qr/newcl.png');

					//clean up
					imagedestroy($output);
					imagedestroy($im);
				}

				$filename = ABSPATH . 'qr/newcl.png';
				QRcode::png("YAY!", $filename);
				
				
				
				$im = imagecreatefrompng($filename);
				//$black = imagecolorallocate($im, 0x00, 0x00, 0x00);
				$fontfile = ABSPATH . "my-includes/fonts/ARIALUNI.ttf";
				$text = 'Andy Garcia';
				
				$bounds = imagettfbbox(12,0,$fontfile,$text);
				var_dump($bounds);
				
				
				$width=imagesx($im);
				$height=imagesy($im);
				$newwidth = 125;
				$newheight = 175;
				$bordersize = 1;
				$wwidth = $newwidth - 2*$bordersize;
				$wheight = $newheight - 2*$bordersize;
				$white_space = imagecreatetruecolor($wwidth,$wheight);
				$output = imagecreatetruecolor($newwidth, $newheight);
				$white = imagecolorallocate($output, 255, 255, 255);
				$black = imagecolorallocate($output, 0,0,0);
				if (!imagefill($output,0,0,$black)) {
					echo "Image fill failed <br>";
				}
				if (!imagefill($white_space,0,0,$white)) {
					echo "Image fill failed <br>";
				}
				imagecopy($output,$white_space,$bordersize,$bordersize,0,0,$wwidth,$wheight);
				imagettftext($output, 12, 0, 20,130, $black, $fontfile, $text);
				imagecopy($output, $im, (($newwidth-$width)/2), 10, 0, 0, $width, $height);
				
				//imagettftext($im, 12, 0, 20,20, $black, $fontfile, $text);
				//imagestring($im,3,20,130,$text,$black);
				imagepng($output,ABSPATH . 'qr/newerimage.png');
				imagepng($im, ABSPATH. 'qr/newcl.png');
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