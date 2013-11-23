<?php

include_once('flaggedKeywords.php');
include_once('twilioHandler.php');
include_once('sendGridHandler.php');
include_once('mongoHandler.php');

if($_GET['q'] == null) { return;}

$query = $_GET['q'];

$keywordSearchVersion = preg_replace('/".*?"/', "", $query);

$file = file_get_contents('http://access.alchemyapi.com/calls/text/TextGetRankedKeywords?apikey=fda1e7725193cdb5de390bda841f5152fa746f0d&outputMode=json&text='.urlencode($keywordSearchVersion));
$d_file = json_decode($file);

$keywords = array();

foreach($d_file->keywords as $keyword) {
	$keyword->text = strtolower($keyword->text);
	array_push($keywords, $keyword->text);
}

foreach($flags as $flag) {
	$flag = strtolower($flag);
	if(stripos($keywordSearchVersion,$flag) !== false) {
		array_push($keywords, $flag);
	} 
}

$output = getOutput($query, $keywords);

if($output == "") {
	$output = "Could not Understand your Request! Please Rephrase!";
} 

echo($output);

function getOutput($query, $keywords) {
	global $twilio, $sendgrid, $record, $emailRecieved, $mongo, $rdio;

	if (count(array_intersect($keywords, $record)) > 0) {
		return processTwilioRecording($query, $keywords);
	}

	if(count(array_intersect($keywords, $emailRecieved)) > 0) {
		return processEmailRecieved($query, $keywords);
	}

	foreach($keywords as $keyword) {
		if(in_array($keyword, $rdio)) {
			return processRdio($query, $keywords);
		}

		if(in_array($keyword, $twilio)) {
			return processTwilio($query, $keywords);
		}

		if(in_array($keyword, $sendgrid)) {
			return processSendGrid($query, $keywords);
		}

		if(in_array($keyword, $mongo)) {
			return processMongo($query, $keywords);
		}
	}

	return "";
}

?>