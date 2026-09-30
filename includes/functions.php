<?php
/* ---------- HELPER FUNCTIONS ---------- */

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function money($v) {
    return number_format((float)$v, 2);
}

function go($url) {
    header("Location: " . $url);
    exit;
}

function upiQrUrl($upiId,$shopName,$amount,$billNo){
    if(trim($upiId)==='' || strtoupper(trim($upiId))==='YOUR_UPI_ID@UPI') return '';
    $params=['pa'=>$upiId,'pn'=>$shopName,'am'=>number_format((float)$amount,2,'.',''),'cu'=>'INR','tn'=>'Bill '.$billNo];
    return 'https://quickchart.io/qr?size=220&margin=1&text='.rawurlencode('upi://pay?'.http_build_query($params));
}

