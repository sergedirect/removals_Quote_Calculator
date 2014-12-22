<?php
    error_reporting(0);
header("content-type:application/json");
if(!empty($_GET['address1'])){
$postcode1R = "".$_GET['address1']." , UK";
$postcode2R = "".$_GET['address2'].", UK";
$postcode1 = str_replace(' ', '+', $postcode1R);
$postcode2 = str_replace(' ', '+', $postcode2R);
//$postcode1 = "Edinburgh, UK";
//$postcode2 = "Dundee+UK";
$result2 = array();

$url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=$postcode1&destinations=$postcode2&mode=driving&language=en-EN&sensor=false";

$result2 = json_decode(file_get_contents($url));

        $rows =  $result2->rows[0];
        
        $elem =  $rows->elements[0];
        $dist = $elem->distance;
        
        $distText = $dist->text;
        $distValue = $dist->value;
        $durt = $elem->duration;
        
        $durText = $durt->text;
        $durtValue = $durt->value;
        if($durtValue > " "){
		$error = " ";
		}else{
		$error = "1";
		}
		if(!$distValue)
    {
        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error1', 'text' => 'Should include address/postcode e.g \"EH1 1QU or 123 Street Name, City Name\".'));
        die($output);
    }else{
		$driveTime = round($durtValue)/60;
	    $path1 = $_GET['carPark1'];
	    $path2 = $_GET['carPark2'];
        $floor1 = $_GET['floor1'];
        $floor2 = $_GET['floor2'];
        $floor = $floor1 + $floor2;  //summ of floors 
		$box1 = $_GET['box1']; 
		$box2 = $_GET['box2'];
		$box3 = $_GET['box3'];
		$box4 = $_GET['box4'];   
        if($_GET['propPrep'] != 1){
		$propPrep = 'No';
		$proPrepCheck = 1;
		}else{$propPrep = 'Yes';$proPrepCheck = 0;}
        if($_GET['helpMan'] != 1){
		$helpMan = 2;
		$help = 'yes';
		}else{$helpMan = 1;$help = 'No';}
        if ($_GET['men'] == 'man'){
		
		$men = '1 man &amp; van';   
		$hourPrice = 25;
		}else{$hourPrice = 35;
		$men = '2 men &amp; van';   }
		if ($driveTime > 30){
		  $drivePlus = (($driveTime-30)/60*$hourPrice)*2+$hourPrice;
		}else{
		  $drivePlus = $driveTime/60*$hourPrice;
		}		
        $xboxS = (((($box1+$box2)*$floor)*20*2.5)*$proPrepCheck)+((($box1+$box2)*$floor)*20*1.5);// time spend on all floor e.g. 1 floor ~ 20 steps --> 40 there and back 1.5 sec/step
		$xboxB = (((($box4+$box3)*$floor)*20*4.5)*$proPrepCheck)+(($box4+$box3)*$floor*20*1.5);//bigger items takes longer to bring down-stairs, but the same time to walk back upstairs
		$pathS = (($path1+$path2)*($box1+$box2))*2; // empty walking small boxes;
        $pathB = (($path1+$path2)*($box3+$box4))*2;
        $pathLoadedSmall = (($path1+$path2)*($box1+$box2))*3;  // small boxes van to stair door;
        $pathLoadedBig = (($path1+$path2)*($box1+$box2))*6; 
		$propTimeS = ($box1+$box2)*60*$proPrepCheck+(($box1+$box2)*60);//time spend inside property small BOXES
        $propTimeB = ($box3+$box4)*240*$proPrepCheck+(($box3+$box4)*240);		
        $loadTimeS = ($box1+$box2)*20*2; //LOAD TIME SMALL BOXES
		$loadTimeB = ((($box3+$box4)*60)+(($box3+$box4)*40));
		$totalBoxTimeS = $xboxS+$pathS+$propTimeS+$loadTimeS+$pathLoadedSmall;
		$totalBoxTimeB = $xboxB+$pathB+$propTimeB+$loadTimeB+$pathLoadedBig;
		$oneManPlus = ($totalBoxTimeS/2+$totalBoxTimeB)/60;
		$oneMan = ($totalBoxTimeS+$totalBoxTimeB)/60;
		$twoMen = ($totalBoxTimeS/2+$totalBoxTimeB*2)/60;
		if($hourPrice == 25 && $helpMan == 1){
		$quote = ($oneManPlus/60*25)+$drivePlus;
		}elseif($hourPrice == 25 && $helpMan == 2){
		$quote = $oneMan/60*25+$drivePlus;
		}elseif($hourPrice == 35){
		$quote = $twoMen/60*35+$drivePlus;
		}
	 
		  
		      if ($quote < 25){
              $totalprice = 25;
              }
               else {
               $totalprice = round($quote,2);
               }
		
		
		
		
		
		
		
      $result = array(
                   array('men'=> ''.$men.'','help' => $help, 'propPrep' => $propPrep, 'address1' => $postcode1R, 'address2'=>$postcode2R, 'box1' => $box1, 'box2' => $box2, 'box3' => $box3,  'box4' => $box4, 'floor1' => $floor1, 'floor2' => $floor2,'carPark1' => $path1, 'carPark2' => $path2, 'distance' => ''.$distValue.'','time' => ''.$durText.'','timeValue'=> ''.$durtValue.'','totalPrice' =>$totalprice)
                   );
                   
            echo json_encode($result);
 
      }
}elseif(!empty($_GET['name']))
{
    $to_email       = "itpal24@gmail.com"; //Recipient email, Replace with own email here
  
    
    //Sanitize input data using PHP filter_var().
    $user_name      = filter_var($_GET["name"], FILTER_SANITIZE_STRING);
    $user_email     = filter_var($_GET["email"], FILTER_SANITIZE_EMAIL);
    $phone_number   = filter_var($_GET["phone"], FILTER_SANITIZE_NUMBER_INT);
    $message        = filter_var($_GET["message"], FILTER_SANITIZE_STRING);
    $hiddenResults  = filter_var($_GET["hiddenResults"], FILTER_SANITIZE_STRING);
    $totalPrice = $_GET['totalPrice'];
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
        $output = json_encode(array(array('type'=>'error', 'text' => 'Could not send mail! Please check your PHP mail configuration.')));
        die($output);
    }else{
         $output = json_encode(array(array('type' => 'done', 'totalPrice' => ''.$totalPrice.'', 'text' => '<h2>Dear  '.$user_name.'</h2><p> Thank you for contacting Man &amp; Van Edinburgh services</p><p>We will review your request and will be in touch with you within 24 hours.')));
       die($output);
    }
}

?>