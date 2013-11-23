<?php
include_once('sendGridHandler.php');
include_once('twilioHandler.php');

if($_GET['out'] == "call") {
	sendPlaybackCall($_GET['recipient'], $_POST["RecordingUrl"]);
	return;
}

if(sizeof($_POST) > 0 && $_POST["TranscriptionText"] != "") 
{
	$speech = $_POST["TranscriptionText"];
} else {
    $speech = "";
}

if($_GET['out'] == "email") {
	sendMail($_GET['recipient'], "SendgridIsAwesome@Woooo.net", "Recording Response", $speech);
} else if($_GET['out'] == "text") {
	sendSMS($_GET['recipient'], $speech);
}
?>