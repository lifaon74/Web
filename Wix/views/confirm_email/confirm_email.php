<?php

if(isset($_REQUEST['user'])) {
	
		// pseudo check
	$results = $mySQL->select('SELECT * FROM users WHERE user_alias = ?', [$_REQUEST['user']]);
	if(count($results) == 0) {
		$error = "Cet utilisateur n'existe pas.";
	} else {
		$result = $results[0];
		
			// activation check
		if($result['user_activated']) {
			$error = "Cet utilisateur a déjà validé son inscription.";
		} else {
				// key check
			if(isset($_REQUEST['key'])) {
				$expectedKey = hash('sha256', $result['user_alias'] . $result['user_email'] . $result['user_password']);
				if($_REQUEST['key'] != $expectedKey) {
					$error = "La clé n'est pas valide.";
				} else {
					$mySQL->query('UPDATE users SET user_activated = 1 WHERE user_id = ?', [$result['user_id']]);
					
					$feedback = "Votre inscription est validée.";
				}
			} else {
				$error = "Paramètre 'key' manquant.";
			}
		}
	}
} else {
	$error = "Paramètre 'user' manquant.";
}

if(isset($error)) {
	echo '<div class="error">' . $error . '</div>';
}

if(isset($feedback)) {
	echo '<div class="feedback">' . $feedback . '</div>';
}

?>

<!-- TODO : put good url -->
<!--<meta http-equiv="refresh" content="4; url=http://example.com/">-->
