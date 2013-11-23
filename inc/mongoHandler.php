<?php

include_once('db.php');

$mongoKeyKeywords = array("key", "entry");
$mongoDocKeywords = array("doc", "document", "file");

function processMongo($query, $keywords) {
	global $mongoDocKeywords, $mongoKeyKeywords;

	$doc = "";
	
	foreach($mongoDocKeywords as $keyword) {
		if(stripos($query,$keyword) !== false) {
			$doc = substr($query, stripos($query, $keyword));
			break;
		} 
	}

	preg_match('/".*?"/', $doc, $matches);
	
	$doc = "default";

	if(sizeof($matches) > 0) {
		$doc =  $matches[0];
		$query = str_replace($matches[0], "", $query);
	}

	$key = "";
	
	foreach($mongoKeyKeywords as $keyword) {
		if(stripos($query,$keyword) !== false) {
			$key = substr($query, stripos($query, $keyword));
			break;
		} 
	}

	preg_match('/".*?"/', $key, $matches);
	
	$key = "default";

	if(sizeof($matches) > 0) {
		$key =  $matches[0];
		$query = str_replace($matches[0], "", $query);
	}

	preg_match('/".*?"/', $query, $matches);
	
	$value = "Default_Data";

	if(sizeof($matches) > 0) {
		$value =  $matches[0];
		$query = str_replace($matches[0], "", $query);
	}
	
	if($doc && $key && $value)
	{
		return saveToDB($doc, $key, $value);
	}

	return "";
}

function saveToDB($doc, $key, $value)
{
	global $apiCollection;
	$stuff = array("DOC_ID" => $doc);
	$document = $apiCollection->findOne($stuff);

	$object = array();
	$object[$key] = $value;
	$object["DOC_ID"] = $doc;

	if(!empty($document)) {
        $apiCollection->update($stuff, $object);
    } else {
	    $apiCollection->save($object);
	}

	return "Successfully Added Entry to MongoDB";
}
?>