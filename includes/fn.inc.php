<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Render country flag (GLOBAL, SAFE, CDN-BASED)
 *
 * @param int|string|null $value  country_id OR ISO code
 * @param int $size               desired display size (px)
 * @return string
 */


require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';


function render_country_flag($value = null, int $size = 24): string
{
    global $pdo;

    if (empty($value)) {
        return '';
    }

    $isoCode = null;

    // CASE 1: ISO code directly (e.g. "US")
    if (is_string($value) && strlen($value) === 2) {
        $isoCode = strtoupper($value);
    }

    // CASE 2: country_id
    if (is_numeric($value) && $pdo) {
        try {
            $stmt = $pdo->prepare(
                "SELECT iso_code 
                 FROM countries 
                 WHERE id = ? AND is_active = 1 
                 LIMIT 1"
            );
            $stmt->execute([(int)$value]);
            $isoCode = $stmt->fetchColumn();
        } catch (PDOException $e) {
            return '';
        }
    }

    if (!$isoCode || strlen($isoCode) !== 2) {
        return '';
    }

    $isoCode = strtolower($isoCode);

    // FlagCDN allowed widths
    $allowedSizes = [20, 40, 80, 160, 320, 640];
    $cdnSize = 40;

    foreach ($allowedSizes as $allowed) {
        if ($size <= $allowed) {
            $cdnSize = $allowed;
            break;
        }
    }

    return '<img
        src="https://flagcdn.com/w' . $cdnSize . '/' . $isoCode . '.png"
        alt="' . strtoupper($isoCode) . ' flag"
        loading="lazy"
        style="width:' . (int)$size . 'px; height:auto; vertical-align:middle; margin-right:6px;"
    >';
}

function sendEmail ($email, $subject, $message)
{   
    //include_once 'messages.php';
    try{
        $mail = new PHPMailer(true);
        $mail -> isSMTP();
        $mail -> Host =  'smtp.gmail.com';
        $mail -> SMTPAuth = true;
        $mail -> Username = 'wezimosiuoa@gmail.com';
        $mail -> Password = 'exyv qdzk yyan gelw'; 
        $mail -> SMTPSecure = 'ssl';
        $mail -> Port = 465;

        $mail -> setFrom('wezimosiuoa@gmail.com');
        $mail -> addAddress($email);

        $mail -> isHTML(true);
        $mail -> Subject = $subject;
        $mail -> Body = $message;

        $mail -> send();

        if (function_exists('showSuccessMessage')) {
            showSuccessMessage("Email sent successfully");
        }
    }catch(Exception $err){
        if (function_exists('showErrorMessage')) {
            showErrorMessage("Email could not be sent. Mailer Error: " . $err->getMessage());
        }
    }
}

//User IP Address Identification 
function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}



