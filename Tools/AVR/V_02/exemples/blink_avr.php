#include <avr/sleep.h>
#include <util/delay.h>


<?php $pinLed = 4; ?>

int main(void) {
	while(true) {
		<?php echo $ArduinoProMini->pinMode($pinLed, $optimizer->OUTPUT); ?>
		<?php echo $ArduinoProMini->digitalWrite($pinLed, 0); ?>
		_delay_ms(100);
		<?php echo $ArduinoProMini->digitalWrite($pinLed, 1); ?>
		_delay_ms(100);
	}
	
	return 0;
}