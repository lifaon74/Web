#ifndef INTERRUPTS_H
#define INTERRUPTS_H

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



char _unshiftByte(unsigned char byte) {
	for(char _i = 0; _i < 8; _i++) {
		if((byte >> _i) == 1) {
			return _i;
		}
	}
	
	return -1;
}

/*void printArray(char array[], unsigned char length) {
	for(unsigned char i = 0; i < length; i++) {
		if(i > 0) { Serial.print(", "); }
		Serial.print(array[i], DEC);
	}
	
	Serial.print('\n');
}*/



#define INTR_NONE	0
#define INTR_CHANGE	1
#define INTR_LOW	2
#define INTR_HIGH	3

typedef void (*InterruptCallbackFunction) (bool state);

struct INTR_pinStruct {
	unsigned char				digitalPinMask;
	RegisterPointer*			digitalPinPortIn;
	
	unsigned char				mode;
	bool						state;
	unsigned char				interruptPinMask;
	InterruptCallbackFunction 	callback;
};


#define NUMBER_OF_PINS	16
INTR_pinStruct		INTR_pins[NUMBER_OF_PINS];
char				INTR_groupToPin[3][8];

void INTR_begin() {	
	for(unsigned char i = 0; i < 3; i++) {
		for(unsigned char j = 0; j < 8; j++) {
			INTR_groupToPin[i][j] = -1;
		}
	}
	
	<?php echo $ArduinoProMini->microcontroller->interrupt_enable() . EOL(1); ?>
	
		// TODO : could be regrouped
	<?php echo $ArduinoProMini->microcontroller->interrupt_enableOnGroup(0); ?>
	<?php echo $ArduinoProMini->microcontroller->interrupt_enableOnGroup(1); ?>
	<?php echo $ArduinoProMini->microcontroller->interrupt_enableOnGroup(2); ?>
}

void INTR_attachInterrupt(unsigned char pin, InterruptCallbackFunction callback, unsigned char mode) {

	unsigned char 		digitalPinMask		= <?php echo $ArduinoProMini->microcontroller->digital_pinToPinMask('pin'); ?>;
	unsigned char		interruptPinMask	= <?php echo $ArduinoProMini->microcontroller->interrupt_pinToPinMask('pin'); ?>;
	RegisterPointer*	digitalPinPortIn	= <?php echo $ArduinoProMini->microcontroller->digital_pinToPortIn('pin'); ?>;
	
	digitalPinMask &= ~<?php echo $ArduinoProMini->microcontroller->digital_pinToPortMode('pin'); ?>;
	
	INTR_pinStruct *INTR_pin		= &INTR_pins[pin];
	
	INTR_pin->digitalPinMask	= digitalPinMask;
	INTR_pin->digitalPinPortIn	= digitalPinPortIn;
	
	INTR_pin->mode				= mode;
	INTR_pin->state				= *digitalPinPortIn & digitalPinMask;
	INTR_pin->callback			= callback;
	INTR_pin->interruptPinMask	= interruptPinMask;
	
	
	INTR_groupToPin[<?php echo $ArduinoProMini->microcontroller->interrupt_pinToGroupNumber('pin'); ?>][_unshiftByte(interruptPinMask)] = pin;
	
		// enable interrupt
	*<?php echo $ArduinoProMini->microcontroller->interrupt_pinToPinChangeMaskRegister('pin'); ?> |= interruptPinMask;

		
	/*printArray(INTR_groupToPin[0], 8);
	printArray(INTR_groupToPin[1], 8);
	printArray(INTR_groupToPin[2], 8);*/
}

void INTR_detachInterrupt(unsigned char pin) {
	INTR_pinStruct *INTR_pin	= &INTR_pins[pin];
	INTR_pin->mode				= INTR_NONE;
	
	INTR_groupToPin[<?php echo $ArduinoProMini->microcontroller->interrupt_pinToGroupNumber('pin'); ?>][_unshiftByte(INTR_pin->interruptPinMask)] = -1;
		// disable interrupt
	*<?php echo $ArduinoProMini->microcontroller->interrupt_pinToPinChangeMaskRegister('pin'); ?> &= ~INTR_pin->interruptPinMask;
}


void INTR_onInterrupt(unsigned char groupNumber) {
	cli();
	char *groupToPin = INTR_groupToPin[groupNumber];
	
	for(unsigned char i = 0; i < 8; i++) {
		char pin = groupToPin[i];
		if(pin != -1) { //pin >= 0
			INTR_pinStruct *INTR_pin	= &INTR_pins[pin];
			
			bool state = FE_pinRead(
				INTR_pin->digitalPinMask,
				*INTR_pin->digitalPinPortIn
			);
			
			if(state != INTR_pin->state) {
			
				switch(INTR_pin->mode) {
					case INTR_CHANGE:
						INTR_pin->callback(state);
					break;
					case INTR_HIGH:
						if(state) {
							INTR_pin->callback(state);
						}
					break;
					case INTR_LOW:
						if(!state) {
							INTR_pin->callback(state);
						}
					break;
				}
				
				INTR_pin->state = state;
			}
		}
	}
	sei();
}


#if defined(FE_INTERRUPT_GROUP_0_INTR_NAME)
	ISR(FE_INTERRUPT_GROUP_0_INTR_NAME) {
		INTR_onInterrupt(0);
	}
#endif

#if defined(FE_INTERRUPT_GROUP_1_INTR_NAME)
	ISR(FE_INTERRUPT_GROUP_1_INTR_NAME) {
		INTR_onInterrupt(1);
	}
#endif

#if defined(FE_INTERRUPT_GROUP_2_INTR_NAME)
	ISR(FE_INTERRUPT_GROUP_2_INTR_NAME) {
		INTR_onInterrupt(2);
	}
#endif

#endif