<?php
//echo "result : <br>";

function readDirectory($path) {
	$formatedFiles = [];
	$files = scandir($path);
	
	foreach($files as $file) {
		if($file == "." || $file == "..") {
			continue;
		} else {
			$fullPath = $path . $file;
			if(is_dir($fullPath)) {
				$fullPath .= "/";
				$isDir = true;
			} else if(is_file($fullPath)) {
				$isDir = false;
			} else {
				continue;
			}
			
			$formatedFiles[] = (object) [
				"path"	=> $fullPath,
				"name"	=> $file,
				"isDir"	=> $isDir
			];
		}
	}
	
	return $formatedFiles;
}

function loadViews($path) {
	$views = [];
	$files = readDirectory($path);
	
	foreach($files as $file) {
		if($file->isDir) {
			$_path = $file->path . $file->name;
			$view = (object) [];
				
			if(file_exists($_path . ".js")) {
				$view->js = file_get_contents($_path . ".js");
			}
			
			if(file_exists($_path . ".acss")) {
				//$view->css = file_get_contents('http://localhost/Web/UNIGE/Master/' . $_path . ".acss"); // CRITICAL : MUST CHANGE IN FUTURE ! => to parse acss, i call file with http !
				$view->css = file_get_contents($_path . ".acss");
			} else if(file_exists($_path . ".css")) {
				$view->css = file_get_contents($_path . ".css");
			}
			
			if(file_exists($file->path . "subViews/")) {
				$view->subViews = loadViews($file->path . "subViews/");
			}
			
			$views[$file->path] = $view;
		}
	}
	return $views;
}

function extractViewsScripts($views) {
	$scripts = (object) [
		"js"	=> "",
		"css"	=> ""
	];
	
	foreach($views as $view) {
		if(isset($view->js)) {
			$scripts->js .= $view->js . "\n";
		}
		
		if(isset($view->css)) {
			$scripts->css .= $view->css . "\n";
		}
		
		if(isset($view->subViews)) {
			$_scripts = extractViewsScripts($view->subViews);
			$scripts->js .= $_scripts->js;
			$scripts->css .= $_scripts->css;
		}
	}
	
	return $scripts;
}

function compileViews($path) {
	$scripts = extractViewsScripts(loadViews($path));
	$js = "
	
fnc.registerLib('Views', 'views/views.js');
fnc.require(['View'], function() {
	
	" . $scripts->js . "
	
	fnc.libReady('Views', {});
});
	";
	
	
	file_put_contents($path . "views.js", $js);
	file_put_contents($path . "views.acss", $scripts->css);
}

compileViews('views/');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>ThingBook - User Interface</title>

		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Content-Language" content="fr"/>
		
		<meta http-equiv="cache-control" content="max-age=0" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta http-equiv="expires" content="0" />
		<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
		<meta http-equiv="pragma" content="no-cache" />

		<!--<link rel="shortcut icon" type="image/x-icon" href="images/favicon/favicon.ico"/>-->
		
		<link rel="stylesheet" type="text/css" href="style.acss"/>
		
		<script type="text/javascript" src="../../../../Libraries/js/fnc.js"></script>
		
		
		
		<?php
			echo '<link rel="stylesheet" type="text/css" href="views/views.acss"/>';
			echo '<script type="text/javascript" src="views/views.js"></script>';
		?>
		
		<!--<script type="text/javascript" src="../../Libraries/js/JSON2.class.js"></script>-->
		<script type="text/javascript" src="core.js"></script>
	</head>
	
		
	<body>
	</body>
</html>