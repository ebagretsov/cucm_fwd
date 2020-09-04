<?php
$host="192.168.1.1"; //CUCM IP
$username="admin"; // AXLApi username
$password="123456"; // AXLApi account password

$client = new SoapClient("AXLAPI.wsdl",
    array('trace'=>true,
   'exceptions'=>true,
   'location'=>"https://".$host.":8443/axl",
   'login'=>$username,
   'password'=>$password,
));

function getLineByUserID($client, $userid){
	//get line by userid
	// ==============================================
	$returnedTags = array("uuid"=>"", "userid"=>"", "firstName"=>"", "telephoneNumber"=>"");
	// "%" is a wild card to find every line
	$searchCriteria = array("userid"=>$userid);
	
	try {
	    $response = $client->listUser(array("returnedTags"=>$returnedTags,"searchCriteria"=>$searchCriteria));
		$pattern = $response->return->user->telephoneNumber;
	}
	catch (SoapFault $sf) {
	    echo "SoapFault: " . $sf . "<BR>";
	}
	catch (Exception $e) {
	    echo "Exception: ". $e ."<br>";
	}
	// ==============================================

	return $pattern;
}

function updateLineForwardAll($client, $pattern, $redirect_num) {
	//get uuid for line
	// ==============================================
	if (isset($pattern)){
		$returnedTags = array("uuid"=>"", "pattern"=>"", "description"=>"", "alertingName"=>"", "shareLineAppearanceCssName"=>"");
		// "%" is a wild card to find every line
		$searchCriteria = array("pattern"=>$pattern);
		
		try {
			$response = $client->listLine(array("returnedTags"=>$returnedTags,"searchCriteria"=>$searchCriteria));
			$line_uuid = $response->return->line->uuid;
		}
		catch (SoapFault $sf) {
			echo "SoapFault: " . $sf . "<BR>";
		}
		catch (Exception $e) {
			echo "Exception: ". $e ."<br>";
		}
	}
	// ==============================================
	
	if (isset($line_uuid)){
		// update the Line CSS
		try {
			$response = $client->updateLine(array("uuid"=>$line_uuid, //find by line uuid 
					"callForwardAll"=>array("destination"=>$redirect_num, "callingSearchSpaceName"=>array("uuid"=>"b23bd9da0-2c1c-d220-76d8-2ff895549777")))); //change Forward all option for DN
		}
		catch (SoapFault $sf) {
			echo "SoapFault: " . $sf . "<BR>";
		}
		catch (Exception $e) {
			echo "Exception: " . $e . "<br>";
		}
	}
	
    return $response;
}