<?php
require "Services/Twilio.php";


$AccountSid = "#####################";
$AuthToken = "######################";
$sendNumber = "########";

$callKeywords = array("call", "dial");

function processTwilio($query, $keywords) {
	global $callKeywords;

	$pattern = '/(?:1-?)?(?:\(\d{3}\)|\d{3})[-\s.]?\d{3}[-\s.]?\d{4}/';
	preg_match($pattern, $query, $matches);

	$bCall = false;

	if(count(array_intersect($keywords, $callKeywords)) > 0) {
		$bCall = true;
	}

	$recipient = "";

	if(sizeof($matches) > 0) {
		$recipient = $matches[0];
	}

	preg_match('/".*?"/', $query, $matches);
	
	$message = "";

	if(sizeof($matches > 0)) {
		$message = str_replace("\"", "", $matches[0]);
	}

	if($recipient != "" && $message != "") {
		if($bCall) {
			sendCall($recipient, $message);
			return ("Successfully Called: ".$recipient. " Saying: ".$message);		
		} else {
			sendSMS($recipient, $message);
			return ("Successfully Sent Text To: ".$recipient. " Saying: ".$message);		
		}
	}
	return "";
}

function processTwilioRecording($query, $keywords) {
	global $twilio, $sendgrid, $callKeywords, $record;

//=========================FIND TYPE===============================
	if (count(array_intersect($keywords, $twilio)) > 0) {
		if(count(array_intersect($keywords, $callKeywords)) > 0) {
			$type = "call";
		} else {
			$type = "text";
		}
	} else if (count(array_intersect($keywords, $sendgrid)) > 0) {
		$type = "email";
	}
//======================END FIND TYPE=================================

//==========================FIND TARGET==================================
	$recipient = "";

	foreach($record as $keyword) {
		if(stripos($query,$keyword) !== false) {
			$recipient = substr($query, stripos($query, $keyword));
			break;
		} 
	}

	$pattern = '/(?:1-?)?(?:\(\d{3}\)|\d{3})[-\s.]?\d{3}[-\s.]?\d{4}/';
	preg_match($pattern, $recipient, $matches);

	$recipient = "";

	if(sizeof($matches) > 0) {
		$recipient = $matches[0];
		$pos = strpos($query, $recipient);
		if($pos !== false) {
			$query = substr_replace($query, "", $pos, strlen($recipient));
		}
	}
//=========================END FIND TARGET================================

//=========================FIND RESPONDER====================================
	if($type == "email") {
		$pattern = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i";
		preg_match($pattern, $query, $matches);

		$responder = "";

		if(sizeof($matches > 0)) {
			$responder = $matches[0];
			$query = str_replace($matches[0], "", $query);
		}
	} else {
		$pattern = '/(?:1-?)?(?:\(\d{3}\)|\d{3})[-\s.]?\d{3}[-\s.]?\d{4}/';
		preg_match($pattern, $query, $matches);

		$responder = "";
		if(sizeof($matches) > 0) {
			$responder = $matches[0];
			$query = str_replace($matches[0], "", $query);
		}
	}
//===========================END FIND RESPONDER================================

//============================FIND QUESTION=====================================
	preg_match('/".*?"/', $query, $matches);
	
	$message = "";

	if(sizeof($matches > 0)) {
		$message = str_replace("\"", "", $matches[0]);
	}
//============================END FIND QUESTION================================

	if($recipient != "" && $responder != "" && $message != "" && $type != "") {
		sendRecordCall($recipient, $responder, $message, $type);
		return ("Successfully Asked for recording from: ".$recipient. " Saying: ".$message);		
	}

	return "";
}

function sendSMS($recipient, $message) {
	global $AccountSid;
	global $AuthToken;
	global $sendNumber;
	$client = new Services_Twilio($AccountSid, $AuthToken);
	$sms = $client->account->sms_messages->create($sendNumber, $recipient, $message, array());
}

function sendCall($recipient, $message) {
	global $AccountSid;
	global $AuthToken;
	global $sendNumber;
	$client = new Services_Twilio($AccountSid, $AuthToken);
	try {
		$call = $client->account->calls->create($sendNumber, $recipient, 'http://jel-massih.biz/APISearch/inc/callText.php?type=call&message='.urlencode($message));
	} catch (Exception $e) {
	}
}

function sendRecordCall($recipient, $responder, $question, $outType) {
	global $AccountSid;
	global $AuthToken;
	global $sendNumber;
	$client = new Services_Twilio($AccountSid, $AuthToken);
	if($outType == "call") {
		$transcribe = "false";
	} else {
		$transcribe = "true";
	}

	try {
		$call = $client->account->calls->create($sendNumber, $recipient, 'http://jel-massih.biz/APISearch/inc/callText.php?type=record&message='.urlencode($question)."&out=".$outType."&recipient=".$responder."&transcribe=".$transcribe);
	} catch (Exception $e) {
	}
}

function sendPlaybackCall($recipient, $playbackURL) {
	global $AccountSid;
	global $AuthToken;
	global $sendNumber;
	$client = new Services_Twilio($AccountSid, $AuthToken);
	try {
		$call = $client->account->calls->create($sendNumber, $recipient, 'http://jel-massih.biz/APISearch/inc/callText.php?type=playback&url='.$playbackURL);
	} catch (Exception $e) {
	}
}
?>