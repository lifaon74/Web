<?php
	global $precompiler, $ArduinoProMini, $microcontroller;
	
	echo $precompiler->_include('avr/pgmspace.h', 2);
	echo $precompiler->_include('avr/io.h', 2);
	echo $precompiler->_include('avr/interrupt.h', 2);
	
	echo $precompiler->_include('stdlib.h', 2);
	echo $precompiler->_include('stdbool.h', 2);
	echo $precompiler->_include('string.h', 2);
	echo $precompiler->_include('math.h', 2);
	
	echo $precompiler->_include('constants.h', 1);
	echo $precompiler->_include('functions.h', 1);
?>

<?php
//echo $avr->digital_mode('A0', 0) . EOL(2);

echo $ArduinoProMini->begin() . EOL(1);

/*echo $ArduinoProMini->analogReference('DEFAULT') . EOL(1);
echo $ArduinoProMini->analogRead('unsigned int analogValue', 'A1') . EOL(2);


echo $ArduinoProMini->pinMode('A0', $optimizer->INPUT);*/

?>

unsigned char state = <?php echo $ArduinoProMini->digitalRead('A0'); ?>;

<?php

/*echo $ArduinoProMini->pinMode('A0', 'OUTPUT');
echo $ArduinoProMini->digitalWrite('A0', 0) . EOL(1);
echo $ArduinoProMini->digitalWrite(2, 1) . EOL(1);
echo $ArduinoProMini->digitalWrite('i', 1) . EOL(1);
echo $ArduinoProMini->digitalWrite('i', 'j') . EOL(1);*/

echo $ArduinoProMini->microcontroller->interrupt_enable() . EOL(1);
/*echo $ArduinoProMini->microcontroller->interrupt_pinToGroupMask(8);
echo $ArduinoProMini->microcontroller->interrupt_pinToPinMask('8');*/

echo $ArduinoProMini->microcontroller->interrupt_groupState('i', 'j');
echo $ArduinoProMini->microcontroller->interrupt_pinState('i', 'j');


?>
