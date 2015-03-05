<?php

require_once("Converter.class.php");

$hostname = '{imap.gmail.com:993/imap/ssl}[Gmail]/Tous les messages';
$username = 'valentinrich@gmail.com';
$password = 'biloute74';



$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

	// list mailboxes
//print_r(imap_list($inbox, $hostname, "*"));

	// count number of msg
//print_r(imap_num_msg($inbox));

$emails = imap_search($inbox, 'ALL', SE_UID);

/*if($emails) {
	
	$output = '';
	
	//rsort($emails);
	
	foreach($emails as $email_number) {
		
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
		
		$output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		$output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
		$output.= '<span class="from">'.$overview[0]->from.'</span>';
		$output.= '<span class="date">on '.$overview[0]->date.'</span>';
		$output.= '</div>';
		
		//$output.= '<div class="body">'.$message.'</div>';
	}
	
	echo $output;
} */

//print_r($emails);


$i = count($emails) - 1;
//print_r(imap_fetchheader($inbox , $emails[0], FT_UID));
$overview = imap_fetch_overview($inbox , $emails[$i], FT_UID);
print_r($overview);

/*$message = imap_body($inbox, $emails[$i], FT_UID);
print_r($message);*/

//echo "\n\n\n\n\n\n\n\n\n\n";


print_r(imap_fetchstructure($inbox, $emails[$i], FT_UID));

echo "\n\n---- SECTION 0 ----\n\n";

$message = imap_fetchbody($inbox, $emails[$i], 0,FT_UID);
print_r($message);

echo "\n\n---- SECTION 1 ----\n\n";

$message = imap_fetchbody($inbox, $emails[$i], "1.1",FT_UID);
print_r($message);

echo "\n\n---- SECTION 2 ----\n\n";

$message = imap_fetchbody($inbox, $emails[$i], 2,FT_UID);
print_r($message); //base64_decode

echo "\n\n---- SECTION 3 ----\n\n";

$message = imap_fetchbody($inbox, $emails[$i], 3,FT_UID);
print_r($message);


//echo $Converter->dateTimeToTimestamp($overview[0]->date);


imap_close($inbox)

?>