<?php

$xml = new XMLWriter();

$xml->openURI("php://output");
$xml->startDocument();
$xml->setIndent(true);

$xml->startElement('Response');

if($_GET['type'] == "playback") {
	$xml->startElement('Play');
	$xml->writeRaw($_GET['url']);
	$xml->endElement();
}
else {
	$xml->startElement('Say');
	$xml->writeAttribute('voice', 'alice');
	$xml->writeRaw($_GET['message']);
	$xml->endElement();

	if($_GET['type'] == "record") {
		$xml->startElement('Record');
		if($_GET['transcribe'] == "true") {
			$xml->writeAttribute('transcribe', "true");
			$xml->writeAttribute('transcribeCallback', "handleTranscribe.php?out=".$_GET['out']."&recipient=".$_GET['recipient']);
		} else {
			$xml->writeAttribute('action', "handleTranscribe.php?out=".$_GET['out']."&recipient=".$_GET['recipient']);
		}
		$xml->endElement();
	}
}

$xml->endElement();

?>