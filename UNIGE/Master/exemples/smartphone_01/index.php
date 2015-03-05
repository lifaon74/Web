<?php

?>

<!DOCTYPE html>
<html>
	<head>
		<title>ThingBook - Test 01</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="fr"/>


		<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
		<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
		
		<script type="text/javascript" src="../../../../Libraries/js/fnc.js"></script>
		
		<link rel="stylesheet" type="text/css" href="style.acss"/>
		<script type="text/javascript" src="script.js"></script>
	</head>
	
		
	<body>
		<form onsubmit="return false;">
			<table id="mainWrapper">
				<tbody>
					<tr>
						<td colspan="2">
							<div id="feedback">Lorem</div>
						</td>
					</tr>
					<tr>
						<td class="half">
							<input id="registerButton" type="button" value="S'enregistrer">
						</td>
						<td>
							<input id="askForNewOwnerButton" type="button" value="Demande de propriétaire">
						</td>
					</tr>
					<tr>
						<td class="half">
							<input id="createRelationshipButton" type="button" value="Vérifier les demandes d'ami">
						</td>
						<td>
							<input id="startStopPublicationsButton" type="button" value="Lancer les publications">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input id="slider" type="range" name="slider" data-highlight="true" min="0" max="255" value="255">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<select id="switch" name="switch" data-role="slider">
						        <option value="0">Off</option>
						        <option value="1">On</option>
						    </select>
						</td>
					</tr>
				<tbody>
			</table>
		</form>
	</body>
</html>