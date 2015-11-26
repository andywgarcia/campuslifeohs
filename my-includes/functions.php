<?php


//use mysql_real_escape_sctring()
function validate_input($conn,$data) {
	$data = mysqli_real_escape_string($conn,$data);
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

//check if only letters and lenght
function checkLetters($text)
{
	if (strlen($text) <= 1){
		return 'Name to short';
	}
	if (!preg_match("/^[a-zA-Z ]*$/",$text)){
			return "Only letters are allowed";
	}
	return 1;
}


function checkNumber($number){
	if (strlen($number) <= 13){
		return 'Check your phone number';
	}
						
	$regex = "/\([1-9]{3}\)\s[1-9]{3}\-[0-9]{4}/"; //format (###) ###-####
	if (!preg_match($regex,$number)){
		$mobilePhoneErr = "Check your phone number, format (###) ###-####";
	}
	return 1;
}

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

?>