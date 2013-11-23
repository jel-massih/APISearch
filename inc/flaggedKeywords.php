<?php

$twilio = array("call", "dial", "text", "sms", "message");
$sendgrid = array("email", "mail");
$record = array("record", "response");
$emailRecieved = array("recieve");
$mongo = array("mongo", "database");
$rdio = "rdio";

$flags = array_merge($twilio, $sendgrid, $record, $emailRecieved, $rdio);

?>