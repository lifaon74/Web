<?php 

header("Content-Type:text/plain");

if(!isset($fnc)) { require_once('../scripts/php/fnc.php'); }
require_once($fnc->absolutePath('scripts/php/APIFunctions.php'));


function getJSONLastError() {
	static $errors = array(
		JSON_ERROR_NONE             => null,
		JSON_ERROR_DEPTH            => "The maximum stack depth has been exceeded",
		JSON_ERROR_STATE_MISMATCH   => "Invalid or malformed JSON",
		JSON_ERROR_CTRL_CHAR        => "Control character error, possibly incorrectly encoded",
		JSON_ERROR_SYNTAX           => "Syntax error",
		JSON_ERROR_UTF8             => "Malformed UTF-8 characters, possibly incorrectly encoded",
		JSON_ERROR_RECURSION		=> "One or more recursive references in the value to be encoded",
		JSON_ERROR_INF_OR_NAN		=> "One or more NAN or INF values in the value to be encoded",
		JSON_ERROR_UNSUPPORTED_TYPE	=> "A value of a type that cannot be encoded was given"
	);
	$error = json_last_error();
	return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
}

$APIFunctions->mySQL->beginTransaction();
$reply = $APIFunctions->executeQueryFromREQUEST();
$APIFunctions->mySQL->commit();
//print_r($reply);
$reply = json_encode($reply);
echo $reply;
?>