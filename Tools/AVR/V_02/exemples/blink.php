#include <Arduino.h>

unsigned char pinLed	= 13;
<?php $pinLed = 13; ?>

void setup() {
	<?php echo $ArduinoProMini->begin() . EOL(); ?>
	
	Serial.begin(9600);
	Serial.println("start");
	
	<?php echo $ArduinoProMini->pinMode($pinLed, $optimizer->OUTPUT); ?>
	
	unsigned long t1 = micros();
	<?php
		for($i = 0; $i < 1000; $i++) {
			//echo "pinMode(pinLed, OUTPUT);" . EOL(); // 8760ns at 8Mhz = 70 ticks
			//echo $ArduinoProMini->pinMode('pinLed', 'OUTPUT') . EOL(); // 6248ns at 8Mhz = 50 ticks
			//echo $ArduinoProMini->pinMode($pinLed, $optimizer->OUTPUT) . EOL(); // 256ns at 8Mhz = 2 ticks
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
	<?php echo $ArduinoProMini->pinMode('pinLed', 'OUTPUT'); ?>
	<?php echo $ArduinoProMini->digitalWrite('pinLed', 'LOW'); ?>
	delay(500);
	<?php echo $ArduinoProMini->digitalWrite('pinLed', 'HIGH'); ?>
	delay(500);

		// blink_optimized
	<?php echo $ArduinoProMini->pinMode($pinLed, $optimizer->OUTPUT); ?>
	<?php echo $ArduinoProMini->digitalWrite($pinLed, 0); ?>
	delay(500);
	<?php echo $ArduinoProMini->digitalWrite($pinLed, 1); ?>
	delay(500);*/
}