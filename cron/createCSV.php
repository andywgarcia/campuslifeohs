<?php
//Create CSV
//Create QR
//Upload to DB
//TODO
/*
Fix SQL Error Checking
Fix QR funtion to makes a qr Code
Change QR Funtion to it takes paths and varibles

*/

/*******************************************************************************************************************************
********************************************************************************************************************************
														Define Start Up Varibles
********************************************************************************************************************************
*******************************************************************************************************************************/

$debug = TRUE;

//Log File 
$LogFileLocation = dirname(__FILE__) . "\\log\\";
$logFileName     = "log_" . date('Y-m-d_H-i') . ".log";

//tempStorage

$CSVFileLocation = dirname(__FILE__) . "\\";
$CSVfileName     = 'student_list.csv';

//Database 
/** The name of the database  */
define('DB_NAME', 'cl');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');


/*******************************************************************************************************************************
********************************************************************************************************************************
														Import Vendor Code & Create Classes
********************************************************************************************************************************
*******************************************************************************************************************************/

//Composer AutoLoader
require 'vendor/autoload.php';


//Fire up the logger
$log = new Katzgrau\KLogger\Logger($LogFileLocation, Psr\Log\LogLevel::DEBUG, array (
    'filename' => $logFileName  
    ));

$log->info('Script Started'); 

///QR
require 'vendor/phpqrcode/qrlib.php';

$log->debug('QR Class Created'); 
/*
//Dropbox				
$accessToken = "mcWxFEgcVbIAAAAAAAACgctpLBLkmojYc8kXY4IJDgQvtBdKiPXaUBT5bRDoj9Mu";
$appInfo = dbx\AppInfo::loadFromJsonFile($includes . "vendor/dropbox-sdk/Dropbox/app-info.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
$accountInfo = $dbxClient->getAccountInfo();
*/

$log->debug('Drobox Created'); 


/*******************************************************************************************************************************
********************************************************************************************************************************
														Check Database Connetion
********************************************************************************************************************************
*******************************************************************************************************************************/
$log->debug('Testing SQL Connetion');

$db = new MysqliDb(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($db->getLastError() == null) {
    $log->error("Failed to connect to MySQL: " . $db->getLastError());
    //failed();
    exit();
}

$log->info('We have a good SQL Connetion');

/*******************************************************************************************************************************
********************************************************************************************************************************
														CREATE CSV
********************************************************************************************************************************
*******************************************************************************************************************************/
$log->debug('Creating CSV');

//FULL CSV Path
$CVSFilePath = $CSVFileLocation . $CSVfileName;

//Delete old file CSV file
$log->debug('Deleting old CSV File');

if (!unlink($CVSFilePath)){
		$log->error('Could not delete old CSV');
	}

//Prepare the SQL
$SQL = "SELECT 'date_submitted', 'identifier', 'firstName','lastName','nickName','mobilePhone','primaryEmail','custom1','custom2','custom3','importPhotoPath'
		UNION ALL
		SELECT `date_submitted`,`identifier`,`First Name`,`Last Name`,IFNULL(Nickname,''),`Phone Number`,`Email`,`Text Ok`,`Grade`,`Birthday`,`Dropbox Image Path` 
		FROM student_list
		WHERE `Grade`!='Graduated';";

$log->debug('Running getting Uers and running SQL');
//Excute it
$resultRaw = $db->rawQuery($SQL);

if (!$resultRaw) {
    $log->error('SQL query failed');
    $log->error('Invalid query: ' . mysql_error());
    failed();
    exit();
}

$log->info('Creating CVS File on Disk');

//Create the file on Disk
if (!($handle = fopen($CVSFilePath,'w'))){
		$log->error('Could not create CSV file on Disk');
		failed();
    	exit();
}
$log->debug('Writing data to CSV file');

foreach($resultRaw as $row){
	fputcsv($handle,$row);
}
$log->info('Created CSV file with Data on Disk');

//Close the file 
fclose($handle);
$log->debug('Closed file buffer');	
			


/*******************************************************************************************************************************
********************************************************************************************************************************
														Create QR Codes
********************************************************************************************************************************
*******************************************************************************************************************************/
/*
$log->info('Creating QR Codes');

//Mysql Hack to create a full join
$rawQRResult = $db->rawQuery("SELECT `identifier`, `First Name`, `Last Name`, `qr_codes`.`file` FROM `student_list`
			   LEFT JOIN `qr_codes` ON `student_list`.`identifier` = `qr_codes`.`studnetID`
			   UNION
			   SELECT `identifier`, `First Name`, `Last Name`, `qr_codes`.`file` FROM student_list
			   RIGHT JOIN `qr_codes` ON `student_list`.`identifier` = `qr_codes`.`studnetID`");

//var_dump($rawQRResult);
$log->debug('Checking for new QR codes to make');

foreach ($rawQRResult as $key ) {
	if($key['file'] == NULL){
		//Create the QR Codes
		$qrFileName = $key['identifier'] . '-' . $key['First Name'] . '-' . $key['Last Name'] . '.png';

		createQrPng($key['First Name'], $key['Last Name'], $key['identifier']);
		
		$log->info('Created QR Code for ' . $key['First Name'] . ' ' . $key['Last Name']);
		$log->info('Updating Database');

		//Array to Insert
		$data = Array ("studnetID" => $key['identifier'], "file" => $qrFileName);

		$QRID = $db->insert('qr_codes', $data);
		if(!$QRID){
			$log->error('Failed to update database for ' . $key['First Name'] . ' ' . $key['Last Name']);
		}
	}
}
*/
$image = new \NMC\ImageWithText\Image('test.jpg');


/*******************************************************************************************************************************
********************************************************************************************************************************
														Upload to Dropbox
********************************************************************************************************************************
*******************************************************************************************************************************/						

$log->info('Starting Dropbox Upload');

$accessToken = "mcWxFEgcVbIAAAAAAAACgctpLBLkmojYc8kXY4IJDgQvtBdKiPXaUBT5bRDoj9Mu";
$appInfo = dbx\AppInfo::loadFromJsonFile($includes . "dropbox-sdk/Dropbox/app-info.json");
$webAuth = new dbx\WebAuthNoRedirect($appInfo, "PHP-Example/1.0");
$dbxClient = new dbx\Client($accessToken, "PHP-Example/1.0");
$accountInfo = $dbxClient->getAccountInfo();

$log->info('Starting CSV File Upload');

$dropbox_student_list = "/Apps/Attendance2/student_list.csv";

$log->info('Starting Dropbox Upload');

$CVSFileHandle = fopen($CVSFilePath,"rb");

if (!$CVSFileHandle){
		$log->error('Could not Open CSV file');
		failed();
    	exit();
}

$DropboxFileUploadResult = $dbxClient->uploadFile("/Apps/Attendance2/student_list.csv",dbx\WriteMode::force(),$CVSFileHandle);

if(!$DropboxFileUploadResult){
		$log->error('Could not Upload CSV file to Dropbox');
		failed();
    	exit();
}

$log->info('CSV File Uploaded to dropbox');
fclose($CVSFileHandle);
	
$log->info('Starting Drobox Picture upload');						
	$f = fopen($uploadedImage,"rb");
	if ($f){
		$result = $dbxClient->uploadFile("/Apps/Attendance2/$dropboxImageName",dbx\WriteMode::force(),$f);
		fclose($f);
	}
	
		
/*******************************************************************************************************************************
********************************************************************************************************************************
														Funtions
********************************************************************************************************************************
*******************************************************************************************************************************/



function failed(){

	$to      = $errorEmail;
	$subject = 'Creating CVS Failed on CampuslifeOHS';
	$message = $r;
	$headers = 'From: Cvsmaker@Campuslifeohs.com' . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();

	mail($to, $subject, $message, $headers);
}



function createQrPng($firstName,$lastName,$id){
					$filename = dirname(__FILE__) . '/qr/' . $id . '-' . $firstName . '-' . $lastName . '.png';
					$size = 8;
					$margin = 1; 
					QRcode::png($id, $filename,"L",$size,$margin);
					
					//create the blank image
					$im = imagecreatefrompng($filename);
					//setting up putting text on the image
					$fontfile = dirname(__FILE__) . "/fonts/ARIALUNI.ttf";
					$text = $firstName . " " . $lastName;
					$font_size = 12;
					$bounds = imagettfbbox($font_size,0,$fontfile,$text);
					
					//can't remember what this does. I think it sets up the canvas with the size
					$width=imagesx($im);
					$height=imagesy($im);
					$newwidth = 200;
					$newheight = 300;
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
					$size = 15;
					$angle = 0;
					$x = 10;
					$y = 10;
						// Create the textbox
						//$textbox = imagecreatetruecolor($newwidth-50, 30);
						//imagefill($textbox, 0, 0, $black);
						//imagefill($textbox, 0, 0, $white);
						// Add the text
						//$tb = imagettftext($textbox, $size, $angle, 0, 0, $black, $fontfile, $text);
						
					//array imagettftext ( resource $image , float $size , float $angle , int $x , int $y , int $color , string $fontfile , string $text )
					//imagecopy ( resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h )
					//Put border
					imagecopy($output,$white_space,$bordersize,$bordersize,0,0,$wwidth,$wheight);
					
					//overlay png image of qr code
					//imagecopy($output, $im, (($newwidth-$width)/2), 10, 0, 0, $width, $height);
					//overlay text
					//$x = ceil(($newwidth - $tb[2]) / 2); // lower left X coordinate for text
					//$y = ceil($newheight*3/4);
					//imagecopy($output,$textbox,$x,$y,0,0,$width,$height);
					//imagettftext($output, $size, $angle, $x, $y, $black, $fontfile, $text);
					$bbox = imagettfbbox($size, 0, $fontfile, $text);
					//$x = $bbox[0] + (imagesx($output) / 2) - ($bbox[4] / 2) + 10;
					//$y = $bbox[1] + (imagesy($output) / 2) - ($bbox[5] / 2) - 5;
					//imagettftext($output, $size, 0, $x, $y, $black, $fontfile, $testext);
					imagestring($output,1,$x,$y,$text,$black);
					//create the final image
					imagepng($output,$filename);
					//clean up
					imagedestroy($output);
					imagedestroy($im);
				}				