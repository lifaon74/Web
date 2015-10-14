<?php
session_start();
?>

<?php
	require_once('config.php');
	require_once('scripts/php/mySQL.class.php');
	
	switch($_REQUEST['view']) {
			// no need of registration
		case 'confirm_email':
			$include = $_REQUEST['view'];
		break;
			// need to be disconnected
		case 'register_user':
		case 'login_user':
			if(isset($_SESSION['user_id'])) {
				$include = 'void';
			} else {
				$include = $_REQUEST['view'];
			}
		break;
			// need to be connected
		case 'logout_user':
		case 'user_scores':
			if(isset($_SESSION['user_id'])) {
				$include = $_REQUEST['view'];
			} else {
				$include = 'void';
			}
		break;
		default:
			$include = '404';
		break;
	}
	
	function getFile($filePath) {
		if(file_exists($path)) {
			include($path);
		}
	}

	$view = [];
	$view["path"]		= 'views/' . $include . '/';
	$view["php_path"]	= $view["path"] . $include . '.php';
	$view["css_path"]	= $view["path"] . $include . '.acss';
	$view["js_path"]	= $view["path"] . $include . '.js';
	
	if(file_exists($view["php_path"])) {
		ob_start();
		include($view["php_path"]);
		$view["php"] = ob_get_clean();
	} else {
		$view["php"] = "";
	}
	
	if(file_exists($view["css_path"])) {
		$view["css"] = '<link rel="stylesheet" type="text/css" href="' . $view["css_path"] . '"/>';
	} else {
		$view["css"] = "";
	}
	
	if(file_exists($view["js_path"])) {
		$view["js"] = '<script type="text/javascript" src="' . $view["js_path"] . '"></script>';
	} else {
		$view["js"] = "";
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Wix Comment</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="fr"/>

		
		
		<script type="text/javascript" src="../Libraries/js/fnc.js"></script>
		<!--<script type="text/javascript" src="scripts/js/Parser.js"></script>-->
		
		<link rel="stylesheet" type="text/css" href="scripts/css/style.acss"/>
		
		<?php echo $view["js"]; ?>
		<?php echo $view["css"]; ?>
	</head>
	
		
	<body>	
		<?php echo $view["php"]; ?>
	</body>
</html>