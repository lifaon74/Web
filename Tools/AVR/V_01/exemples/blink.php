#include <Arduino.h>
<?php echo $avr->getIncludes() . EOL(); ?>

unsigned char pinLed	= 13;
<?php $pinLed = 13; ?>

void setup() {
	Serial.begin(9600);
	Serial.println("start");
	
	<?php echo $avr->digital->mode($pinLed, $avr->OUTPUT) . EOL(); ?>
	
	unsigned long t1 = micros();
	<?php
		for($i = 0; $i < 1000; $i++) {
			echo "pinMode(pinLed, OUTPUT);" . EOL(); // 8760ns at 8Mhz = 70 ticks
			//echo $avr->digital->mode('pinLed', 'OUTPUT') . EOL(); // 6248ns at 8Mhz = 50 ticks
			//echo $avr->digital->mode($pinLed, $avr->OUTPUT) . EOL(); // 256ns at 8Mhz = 2 ticks
		}
	?>
	unsigned long t2 = micros();
	Serial.println(t2 - t1, DEC);
	
}

void loop() {
		// blink_very_slow
	/*
	pinMode(pinLed, OUTPUT);
	digitalWrite(pinLed, LOW);
	delay(500);
	digitalWrite(pinLed, HIGH);
	delay(500);
	
		// blink_slow
	<?php echo $avr->digital->mode('pinLed', 'OUTPUT') . EOL(); ?>
	<?php echo $avr->digital->write('pinLed', 'LOW') . EOL(); ?>
	delay(500);
	<?php echo $avr->digital->write('pinLed', 'HIGH') . EOL(); ?>
	delay(500);

	<?php echo $avr->digital->read('pinLed') . EOL(); ?>
	
		// blink_optimized
	<?php echo $avr->digital->mode($pinLed, $avr->OUTPUT) . EOL(); ?>
	<?php echo $avr->digital->write($pinLed, 0) . EOL(); ?>
	delay(500);
	<?php echo $avr->digital->write($pinLed, 1) . EOL(); ?>
	delay(500);*/
}