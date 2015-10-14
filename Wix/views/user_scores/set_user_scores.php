<?php
session_start();

$root = "../../";

require_once($root . 'config.php');
require_once($root . 'scripts/php/mySQL.class.php');

$return = (object) array(
	"status" => "ERROR"
);
			
if(isset($_SESSION['user_id'])) {
	if(isset($_REQUEST['score_fitness_id'])) {
		if(isset($_REQUEST['score_name'])) {
			if(isset($_REQUEST['score_value'])) {
				$results = $mySQL->select('SELECT * FROM fitness WHERE fitness_id = ?', [$_REQUEST['score_fitness_id']]);
				
				if(count($results) == 0) {
					$return->message = "no fitness with this id";
				} else {
					$available_score_names = ["score_serve", "score_equipment", "score_cleanliness", "score_place", "score_price_quality"];
					
					if(in_array($_REQUEST['score_name'], $available_score_names)) {
						if((0 <= $_REQUEST['score_value']) && ($_REQUEST['score_value'] <= 5)) {
							$results = $mySQL->select('SELECT * FROM scores WHERE score_user_id = ?  AND score_fitness_id = ? AND score_name = ?', [$_SESSION['user_id'], $_REQUEST['score_fitness_id'], $_REQUEST['score_name']]);
							if(count($results) == 0) {
								$mySQL->insert('scores', [$_SESSION['user_id'], $_REQUEST['score_fitness_id'], $_REQUEST['score_name'], $_REQUEST['score_value']]);
								$return->status = "OK";
							} else {
								$mySQL->query('UPDATE scores SET score_value = ? WHERE score_user_id = ?  AND score_fitness_id = ? AND score_name = ?', [$_REQUEST['score_value'], $_SESSION['user_id'], $_REQUEST['score_fitness_id'], $_REQUEST['score_name']]);
								$return->status = "OK";
							}
						} else {
							$return->message = "invalid value for attribute 'score_value'";
						}
					} else {
						$return->message = "invalid value for attribute 'score_name'";
					}
				}
			} else {
				$return->message = "missing attribute 'score_value'";
			}
		} else {
			$return->message = "missing attribute 'score_name'";
		}
	} else {
		$return->message = "missing attribute 'score_fitness_id'";
	}
} else {
	$return->message = "user not connected";
}

echo json_encode($return);
?>