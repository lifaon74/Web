<?php

if(isset($_REQUEST['fitness_id'])) {
	
$scores = [
	"score_serve"			=> ["string" => "Service", "value" => 0],
	"score_equipment"		=> ["string" => "Matériel", "value" => 0],
	"score_cleanliness"		=> ["string" => "Propreté", "value" => 0],
	"score_place"			=> ["string" => "Situation", "value" => 0],
	"score_price_quality"	=> ["string" => "Qualité/Prix", "value" => 0],
];

$results = $mySQL->select('SELECT * FROM scores WHERE score_user_id = ?  AND score_fitness_id = ?', [$_SESSION['user_id'], $_REQUEST['fitness_id']]);

for($i = 0, $size = count($results); $i < $size; $i++) {
	$result = $results[$i];
	$scores[$result["score_name"]]["value"] = $result["score_value"];
}

?>

<script>
	var fitness_id = <?php echo $_REQUEST['fitness_id']; ?>
	
	fnc.registerLib('Field', 'views/user_scores/user_scores.js');
	
	fnc.require(['Field'], function() {
		console.log("rdy");
		
		var fields = {
			<?php
				$i = 0;
				$str = "";
				
				foreach($scores as $score_name => $score_entry) {
					if($i > 0) { $str .= ",\r\n"; } 
					$str .= '"' . $score_name . '"	: new Field("' . $score_entry["string"] . '", ' . $score_entry["value"] . ')';
					$i++;
				}
				
				echo $str;
			?>
		};
	
		generateFields(fields);
	});
</script>

<div id="user_scores" class="scores_display">
	<table class="scores_fields">
	</table>
</div>

<?php
}
?>