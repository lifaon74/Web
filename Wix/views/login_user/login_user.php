<?php

if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'submit_form')) {
	
		// user_alias check
	if(isset($_REQUEST['user_alias'])) {
		$results = $mySQL->select('SELECT * FROM users WHERE user_alias = ?', [$_REQUEST['user_alias']]);
		
		if(count($results) == 0) {
			$error = "Cet utilisateur n'existe pas.";
		} else {
			$result = $results[0];
				// activation check
			if($result['user_activated']) {
					// password check
				if(isset($_REQUEST['password'])) {
					if(password_verify($_REQUEST['password'], $result['user_password'])) {
						$_SESSION['user_id'] = $result['user_id'];
						
						$feedback = "Vous êtes désormais connecté.";
					} else {
						$error = "Combinaison nom d'utilisateur / mot de passe invalide.";
					}
				} else {
					$error = "Le champ mot de passe doit être rempli.";
				}
			} else {
				$error = "Vous n'avez pas encore validé votre inscription, consultez vos emails.";
			}
		}
	} else {
		$error = "Le champ nom d'utilisateur doit être rempli";
	}
}

?>

<div class="form_type_01">
	<form action="" method="POST">
	
		<input type="hidden" name="view" value="login_user"/>
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
				<th>Mot de passe : </th>
				<td><input type="password" name="password"  value="<?php if(isset($_REQUEST['password'])) { echo htmlspecialchars($_REQUEST['password']); } ?>"/></td>
			</tr>
			
			<tr>
				<th></th>
				<td><input type="submit" value="Se connecter"></td>
			</tr>
		</table>
		
	</form>
</div>