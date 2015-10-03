<?php
require_once ABSPATH . 'my-includes/phpqrcode/qrlib.php';


function create_qr($firstName,$lastName,$id){
    QRcode::png("Yay: QR CODES!",ABSPATH);
	
}
?>