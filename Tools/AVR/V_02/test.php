<?php
//echo $avr->digital_mode('A0', 0) . EOL(2);

echo $ArduinoProMini->begin() . EOL(1);

echo $ArduinoProMini->analogReference('DEFAULT') . EOL(1);
echo $ArduinoProMini->analogRead('unsigned int analogValue', 'A1') . EOL(2);


echo $ArduinoProMini->pinMode('A0', $optimizer->INPUT);

?>

unsigned char state = <?php echo $ArduinoProMini->digitalRead('A0'); ?>;

<?php

echo $ArduinoProMini->pinMode('A0', 'OUTPUT');
echo $ArduinoProMini->digitalWrite('A0', 0) . EOL(1);
echo $ArduinoProMini->digitalWrite(2, 1) . EOL(1);
echo $ArduinoProMini->digitalWrite('i', 1) . EOL(1);
echo $ArduinoProMini->digitalWrite('i', 'j') . EOL(1);
?>
