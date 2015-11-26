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
//require ABSPATH . 'my-includes/ImageWithText/Image.php';
//require ABSPATH . 'my-includes/ImageWithText/Text.php';
require ABSPATH . 'my-includes/vendor/autoload.php';

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);

get_header(); ?>

	<div id="primary" class="full-page-content-area">
		<div id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>
				<?php 
				function createQrPng($firstName,$lastName,$id){
					$filename = ABSPATH . 'qr/' . $id . '-' . $firstName . '-' . $lastName . '.png';
					$size = 6;
					$margin = 1; 
					QRcode::png($id, $filename,"L",$size,$margin);
					
					//create the blank image
					$im = imagecreatefrompng($filename);
					$width=imagesx($im);
					$height=imagesy($im);

					//setting up variables for putting text on the image
					$fontfile = ABSPATH . "my-includes/fonts/Avenir Next Condensed.ttc";
					$string = $firstName . " " . $lastName;
					$fontsize = 16;
					$bbox = imagettfbbox($fontsize,0,$fontfile,$string);
					$text_width = $bbox[4] - $bbox[0];
					$maxchars = 40;
					$margin = 20; //10 pixels each side
					
					//adjust fontsize for size of text until it fits
					while (($text_width + $margin) > $width){
						$fontsize -= 1;
						$bbox = imagettfbbox($fontsize, 0, $fontfile, $string);
						$text_width = $bbox[4] - $bbox[0];
					}
					
					//Set up the canvas with the size and black fill
					$newwidth = 144;
					$newheight = 216;
					$bordersize = 1;
					$wwidth = $newwidth - 2*$bordersize;
					$wheight = $newheight - 2*$bordersize;
					$white_space = imagecreatetruecolor($wwidth,$wheight);
					$output = imagecreatetruecolor($newwidth, $newheight);
					$white = imagecolorallocate($output, 255, 255, 255);
					$black = imagecolorallocate($output, 0,0,0);
					imagefill($output,0,0,$black);
					imagefill($white_space,0,0,$white);

					//Put border by filling with white box except for border
					imagecopy($output,$white_space,$bordersize,$bordersize,0,0,$wwidth,$wheight);
					
					//overlay png image of qr code
					imagecopy($output, $im, (($newwidth-$width)/2), 5, 0, 0, $width, $height);
					
					//output image with qr code
					imagepng($output,$filename);
					
					//add text to image

					$image = new \NMC\ImageWithText\Image($filename);
					$text = new \NMC\ImageWithText\Text($string,1,$maxchars);
					$text->font = $fontfile;
					$text->align = 'left';
					$text->size = $fontsize;
					$text->startY = (int)($newheight * (215/300)); //below qr code. Adjusted for dif image sizes
					$text->startX = (int)(($newwidth - $text_width)/2); //center
					$image->addText($text);
					$image->render($filename);

					
					//clean up
					imagedestroy($output);
					imagedestroy($im);
				}
				/*
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
				*/
				
				$firstName = "Andrew";
				$lastName = "Garcia";
				$id = 999;
				$filename = 'qr/' . $id . '-' . $firstName . '-' . $lastName . '.png';
				/*
				$fontfile = ABSPATH . "my-includes/fonts/Avenir Next Condensed.ttc";
				
				$height = 216;
				$width = 144;
				
				$fontsize = 16;
				$string = "Andy Garcia";
				$bbox = imagettfbbox($fontsize, 0, $fontfile, $string);
				$maxchars = 40;
				$image = new \NMC\ImageWithText\Image($filename);
				$text = new \NMC\ImageWithText\Text($string,1,$maxchars);
				$text->font = $fontfile;
				$bbox = imagettfbbox($fontsize, 0, $fontfile, $string);
				var_dump($bbox);//17
				$weird_offset = 18;
				$text_width = $bbox[4] - $bbox[0];
				$margin = 20; //10 pixels each side
				//adjust fontsize for size of text until it fits
				while (($text_width + $margin) > $width){
					$fontsize -= 1;
					$bbox = imagettfbbox($fontsize, 0, $fontfile, $string);
					$text_width = $bbox[4] - $bbox[0];
					
				}

				$startx = (int)(($width - $text_width)/2);

				$text->align = 'left';
				$text->size = $fontsize;
				$text->startY = (int)($height * (215/300));
				$text->startX = $startx;
				$image->addText($text);
				$image->render($filename);*/
				createQrPng($firstName,$lastName,$id);

				?>
				
				<img src="<?php echo site_url() . '/' . $filename; ?>" />
				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->

<?php get_footer(); ?>