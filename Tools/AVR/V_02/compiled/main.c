#include <stdlib.h>
#include <stdbool.h>
#include <string.h>
#include <math.h>
#include <avr/pgmspace.h>
#include <avr/io.h>
#include <avr/interrupt.h>
#include "constants.h"
#include "functions.h"


#include <avr/sleep.h>
#include <util/delay.h>



int main(void) {
	while(true) {
		DDRD |= 16;
		PORTD &= 239;
		_delay_ms(100);
		PORTD |= 16;
		_delay_ms(100);
	}
	
	return 0;
}