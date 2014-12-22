<?php

    error_reporting(0);
if($_POST)
{
    $to_email       = "itpal24@gmail.com"; //Recipient email, Replace with own email here
    if(empty($_POST["hiddenResults"])){
    $hiddenResults = "";}else{$hiddenResult  = $_POST["hiddenResults"];$hiddenResults = "Quote Results:\r\n ".$hiddenResult."";}
    //check if its an ajax request, exit if not
   
    
    //Sanitize input data using PHP filter_var().
    $user_name      = filter_var($_POST["user_name"], FILTER_SANITIZE_STRING);
    $user_email     = filter_var($_POST["user_email"], FILTER_SANITIZE_EMAIL);
    $phone_number   = filter_var($_POST["phone_number"], FILTER_SANITIZE_NUMBER_INT);
    $message        = filter_var($_POST["msg"], FILTER_SANITIZE_STRING);
    
    //additional php validation

    $subject = "Quote Request || Man & Van Edinburgh";
    //email body
    $message_body = $message."\r\n\r\n-".$user_name."\r\nEmail : ".$user_email."\r\nPhone Number : ". $phone_number."\r\n".$hiddenResults."";
    
    //proceed with PHP email.
    $headers = 'From: mail@manwithvanedinburgh.org.uk' . "\r\n" .
    'Reply-To: '.$user_email.'' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    
    $send_mail = mail($to_email, $subject, $message_body, $headers);
    
    if(!$send_mail)
    {
        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error', 'text' => 'Could not send mail! Please check your PHP mail configuration.'));
        die($output);
    }else{
        $output = json_encode(array('type'=>'done', 'text' => '<h2>Dear  '.$user_name .'</h2><p> Thank you for contacting Man &amp; Van Edinburgh services</p><p>We will review your request and will be in touch with you within 24 hours.'));
        die($output);
    }
}else{$output = json_encode(array('type'=>'error', 'text' => 'Could not send mail! Please check your PHP mail configuration.'));
        die($output);}
?>