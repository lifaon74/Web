#include <avr/pgmspace.h>
#include <avr/io.h>
#include <avr/interrupt.h>
#include <stdlib.h>
#include <stdbool.h>
#include <string.h>
#include <math.h>
#include "constants.h"
#include "functions.h"

ADMUX &= B11011111;
ADCSRA = (ADCSRA & B11111000) | 3;


unsigned char state = ((bool) (PINC & 1));

SREG |= 128;

if(j) {
	PCICR |= (1 << i);
} else { 
	PCICR &= ~(1 << i);
}if(j) {
	interrupt_pinToPinChangeMaskRegister(i) |= interrupt_pinToPinMask(i);
} else { 
	interrupt_pinToPinChangeMaskRegister(i) &= ~interrupt_pinToPinMask(i);
}