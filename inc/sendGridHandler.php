<?php
function processSendGrid($query, $keywords) {
	$from = "";
	
	if(stripos($query,"from") !== false) {
		$from = substr($query, stripos($query, "from"));
	} 
	$pattern = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i";

	preg_match($pattern, $from, $matches);
	
	$from = "APISearch@IHeartSendgrid.net";

	if(sizeof($matches) > 0) {
		$from =  $matches[0];
		$query = str_replace($matches[0], "", $query);
	}

	preg_match($pattern, $query, $matches);

	$recipient = "";

	if(sizeof($matches > 0)) {
		$recipient = $matches[0];
	}
	$subject = "";

	if(stripos($query,"subject") !== false) {
		$subject = substr($query, stripos($query, "subject"));
	} 

	preg_match('/".*?"/', $subject, $matches);
	
	$subject = "Generic Email Subject";

	if(sizeof($matches) > 0) {
		$subject = str_replace("\"", "", $matches[0]);
		$query = str_replace($matches[0], "", $query);
	}

	preg_match('/".*?"/', $query, $matches);

	$message = "";

	if(sizeof($matches > 0)) {
		$message = str_replace("\"", "", $matches[0]);
	}

	if($recipient != "" && $message != "") {
		sendMail($recipient, $from, $subject, $message);
		return ("Successfully Emailed: ".$recipient. " From: ".$from." With Subject: ".$subject." and Message: ".$message);		
	}
}

function sendMail($recipient, $from, $subject, $body) {
	$url = 'http://sendgrid.com/';

	$params = array(
	    'api_user'  => "USERNAME",
	    'api_key'   => "PASSWORD",
	    'to'        => $recipient,
	    'subject'   => $subject,
	    'html'      => $body,
	    'text'      => $body,
	    'from'      => $from
	    ,
	  );


	$request =  $url.'api/mail.send.json';

	$session = curl_init($request);
	curl_setopt ($session, CURLOPT_POST, true);
	curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($session);
	curl_close($session);
}
//130.215.24.234
function processEmailRecieved($query, $keywords) {
	global $twilio, $sendgrid;
	$callKeywords = array("call", "dial");

	if (count(array_intersect($keywords, $twilio)) > 0) {
		if(count(array_intersect($keywords, $callKeywords)) > 0) {
			$type = "call";
		} else {
			$type = "text";
		}
	} else if (count(array_intersect($keywords, $sendgrid)) > 0) {
		$type = "email";
	}

	if($type == "email") {
		$pattern = "/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i";
		preg_match($pattern, $query, $matches);

		$recipient = "";

		if(sizeof($matches) > 0) {
			$recipient = $matches[0];
			$query = str_replace($matches[0], "", $query);
		}
	} else {
		$pattern = '/(?:1-?)?(?:\(\d{3}\)|\d{3})[-\s.]?\d{3}[-\s.]?\d{4}/';
		preg_match($pattern, $query, $matches);

		$recipient = "";
		if(sizeof($matches) > 0) {
			$recipient = $matches[0];
			$query = str_replace($matches[0], "", $query);
		}
	}

	preg_match('/".*?"/', $query, $matches);
	
	$output = "#{body}"; //Um I dont know Really tired right now so its like Ruby.

	if(sizeof($matches) > 0) {
		$output = str_replace("\"", "", $matches[0]);
		$query = str_replace($matches[0], "", $query);
	}

	if($recipient != "" && $type != "" && $output != "") {
		file_put_contents("globalSettings.dat", '{"recipient":"'.$recipient.'","type":"'.$type.'","output":"'.$output.'"}');
		return ("Successfully set action to ".$type." ".$recipient." when email is recieved.");
	}

	return "";
}
?>