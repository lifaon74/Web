#ifndef INTERRUPTS_H
#define INTERRUPTS_H

#include "C:\Users\valentin\Documents\Workspace\Arduino\Libraries\FAST_EXECUTION\FAST_EXECUTION.h"


char _unshiftByte(unsigned char byte) {
	for(char _i = 0; _i < 8; _i++) {
		if((byte >> _i) == 1) {
			return _i;
		}
	}
	
	return -1;
}

void printArray(char array[], unsigned char length) {
	for(unsigned char i = 0; i < length; i++) {
		if(i > 0) { Serial.print(", "); }
		Serial.print(array[i], DEC);
	}
	
	Serial.print('\n');
}



#define INTR_NONE	0
#define INTR_CHANGE	1
#define INTR_LOW	2
#define INTR_HIGH	3

typedef void (*callbackFunction) (bool state);

struct INTR_pinStruct {
	unsigned char		digitalPinMask;
	RegisterPointer*	digitalPinPortIn;
	
	unsigned char		mode	= INTR_NONE;
	bool				state	= 0;
	unsigned char		interruptPinMask;
	callbackFunction 	callback;
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
	
	#if defined(FE_INTERRUPT_GROUP_0_MASK)
		FE_enableInterruptOnGroup(FE_INTERRUPT_GROUP_0_MASK);
	#endif
	
	#if defined(FE_INTERRUPT_GROUP_1_MASK)
		FE_enableInterruptOnGroup(FE_INTERRUPT_GROUP_1_MASK);
	#endif
	
	#if defined(FE_INTERRUPT_GROUP_2_MASK)
		FE_enableInterruptOnGroup(FE_INTERRUPT_GROUP_2_MASK);
	#endif
}

void INTR_attachInterrupt(unsigned char pin, callbackFunction callback, unsigned char mode) {

	unsigned char 		digitalPinMask		= FE_pinTo_DIGITAL_PIN_MASK(pin);
	unsigned char		interruptPinMask	= FE_pinTo_INTERRUPT_PIN_MASK(pin);
	RegisterPointer*	digitalPinPortIn	= FE_pinTo_DIGITAL_PIN_PORT_IN(pin);
	
	FE_pinModeInput(
		digitalPinMask,
		*FE_pinTo_DIGITAL_PIN_PORT_MODE(pin)
	);
	
	/*FE_pinWriteHigh(
		digitalPinMask,
		*FE_pinTo_DIGITAL_PIN_PORT_OUT(pin)
	);*/
	
	
	INTR_pinStruct *INTR_pin		= &INTR_pins[pin];
	
	INTR_pin->digitalPinMask		= digitalPinMask;
	INTR_pin->digitalPinPortIn	= digitalPinPortIn;
	
	INTR_pin->mode				= mode;
	INTR_pin->state				= FE_pinRead(
		digitalPinMask,
		*digitalPinPortIn
	);
	INTR_pin->callback			= callback;
	INTR_pin->interruptPinMask	= interruptPinMask;
	
	
	INTR_groupToPin[FE_pinTo_INTERRUPT_PIN_GROUP_NUMBER(pin)][_unshiftByte(interruptPinMask)] = pin;
	
	FE_enableInterruptOnPin(interruptPinMask, *FE_pinTo_INTERRUPT_PIN_GROUP(pin));

		
	/*printArray(INTR_groupToPin[0], 8);
	printArray(INTR_groupToPin[1], 8);
	printArray(INTR_groupToPin[2], 8);*/
}

void INTR_detachInterrupt(unsigned char pin) {
	INTR_pinStruct *INTR_pin	= &INTR_pins[pin];
	INTR_pin->mode			= INTR_NONE;
	
	INTR_groupToPin[FE_pinTo_INTERRUPT_PIN_GROUP_NUMBER(pin)][_unshiftByte(INTR_pin->interruptPinMask)] = -1;
	FE_disableInterruptOnPin(INTR_pin->interruptPinMask, *FE_pinTo_INTERRUPT_PIN_GROUP(pin));
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


