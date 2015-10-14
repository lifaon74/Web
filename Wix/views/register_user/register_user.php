<?php

if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'submit_form')) {
	
	// user_alias check
	if(!isset($_REQUEST['user_alias'])) {
		$error = "Le champ nom d'utilisateur doit être rempli";
	} else {
		$strlen = strlen($_REQUEST['user_alias']);
		if($strlen < 4) {
			$error = "Le champ nom d'utilisateur doit contenir au moins 4 caractères.";
		} else if($strlen > 64) {
			$error = "Le champ nom d'utilisateur doit contenir au plus 64 caractères.";
		} else {
			$results = $mySQL->select('SELECT * FROM users WHERE user_alias = ?', [$_REQUEST['user_alias']]);
			if(count($results) > 0) {
				$error = "Ce nom d'utilisateur est déjà utilisé.";
			}
			
		}
	}
		// email check
	if(!isset($error)) {
		if(!isset($_REQUEST['email'])) {
			$error = "Le champ email doit être rempli";
		} else {
			$results = $mySQL->select('SELECT * FROM users WHERE user_email = ?', [$_REQUEST['email']]);
			if(count($results) > 0) {
				$error = "Cette adresse email est déjà utilisée.";
			}
		}
	}
	
		// password check
	if(!isset($error)) {
		if(!isset($_REQUEST['password'])) {
			$error = "Le champ mot de passe doit être rempli.";
		} else {
			$strlen = strlen($_REQUEST['password']);
			if($strlen < 4) {
				$error = "Le champ mot de passe doit contenir au moins 4 caractères pour votre propre sécurité.";
			} else if($strlen > 255) {
				$error = "Le champ mot de passe doit contenir au plus 255 caractères.";
			}
		}
	}
	
		// everything seems ok
	if(!isset($error)) {
		$hashedPassword = password_hash($_REQUEST['password'], PASSWORD_DEFAULT);
		$mySQL->insert('users', [NULL, $_REQUEST['user_alias'], $_REQUEST['email'], $hashedPassword, time(), false]);

		$currentUrl	= preg_replace('#/[^/]+$#isU', '', $_SERVER['HTTP_REFERER']) . '/';
		$key		= hash('sha256', $_REQUEST['user_alias'] . $_REQUEST['email'] . $hashedPassword);
		$link		= $currentUrl . '?view=confirm_email&user=' . $_REQUEST['user_alias'] . '&key=' . $key;
		
		$to			= $_REQUEST['email'];
		$from		= 'fitnessadvisor <valentinrich@gmail.com>';
		$subject	= 'Inscription fitnessadvisor';

		ob_start();
		include('email_template.php');
		$emailContent = ob_get_clean();

		echo $emailContent;
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

		$headers .= 'To: ' . $to . "\r\n";
		$headers .= 'From: ' . $from . "\r\n";

		if(mail($to, $subject, $emailContent, $headers)) {
			$feedback = "Un email de confirmation vous a été envoyé pour valider votre inscription.";
		} else {
			$error = "Une erreur est survenue lors de l'envoie de l'email de confirmation. Vérifiez le champ email.";
		}
	}
	
}

?>

<div class="form_type_01">
	<form action="" method="POST">
	
		<input type="hidden" name="view" value="register_user"/>
		<input type="hidden" name="action" value="submit_form"/>
		
		<table>
			<?php
				if(isset($error)) {
			?>
				<tr>
					<th class="error" colspan="2"><?php echo $error; ?></th>
				</tr>
			<?php
				}
			?>
			
			<?php
				if(isset($feedback)) {
			?>
				<tr>
					<th class="feedback" colspan="2"><?php echo $feedback; ?></th>
				</tr>
			<?php
				}
			?>
			
			<tr>
				<th>Nom d'utilisateur : </th>
				<td><input type="text" name="user_alias" value="<?php if(isset($_REQUEST['user_alias'])) { echo htmlspecialchars($_REQUEST['user_alias']); } ?>"/></td>
			</tr>
			
			<tr>
				<th>Email : </th>
				<td><input type="text" name="email"  value="<?php if(isset($_REQUEST['email'])) { echo htmlspecialchars($_REQUEST['email']); } ?>"/></td>
			</tr>
			
			<tr>
				<th>Mot de passe : </th>
				<td><input type="password" name="password"  value="<?php if(isset($_REQUEST['password'])) { echo htmlspecialchars($_REQUEST['password']); } ?>"/></td>
			</tr>
			
			<tr>
				<th></th>
				<td><input type="submit" value="S'inscrire"></td>
			</tr>
		</table>
		
	</form>
</div>