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

#include <avr/sleep.h>
#include <util/delay.h>

unsigned char i = 4;
<?php $pinLed = 4; ?>

int main(void) {
	while(true) {
		<?php echo $ArduinoProMini->pinMode($pinLed, $precompiler->OUTPUT); ?>
		//<?php echo $ArduinoProMini->digitalWrite($pinLed, 0); ?>
		<?php echo $ArduinoProMini->digitalWrite('i', 0); ?>
		_delay_ms(100);
		<?php echo $ArduinoProMini->digitalWrite($pinLed, 1); ?>
		_delay_ms(100);
	}
	
	return 0;
}