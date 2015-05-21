volatile unsigned char* pinToDigitalPortMode(unsigned char element) {
	switch(element) {
		case 2:
			return &PIND;
		break;
		case 3:
			return &PIND;
		break;
		case 4:
			return &PIND;
		break;
		case 5:
			return &PIND;
		break;
		case 6:
			return &PIND;
		break;
		case 7:
			return &PIND;
		break;
		case 8:
			return &PINB;
		break;
		case 9:
			return &PINB;
		break;
		case 10:
			return &PINB;
		break;
		case 11:
			return &PINB;
		break;
		case 12:
			return &PINB;
		break;
		case 13:
			return &PINB;
		break;
		default:
			return NULL;
		break;
	}
}

volatile unsigned char* pinToDigitalPortIn(unsigned char element) {
	switch(element) {
		case 2:
			return &PIND;
		break;
		case 3:
			return &PIND;
		break;
		case 4:
			return &PIND;
		break;
		case 5:
			return &PIND;
		break;
		case 6:
			return &PIND;
		break;
		case 7:
			return &PIND;
		break;
		case 8:
			return &PINB;
		break;
		case 9:
			return &PINB;
		break;
		case 10:
			return &PINB;
		break;
		case 11:
			return &PINB;
		break;
		case 12:
			return &PINB;
		break;
		case 13:
			return &PINB;
		break;
		default:
			return NULL;
		break;
	}
}

volatile unsigned char* pinToDigitalPortOut(unsigned char element) {
	switch(element) {
		case 2:
			return &PORTD;
		break;
		case 3:
			return &PORTD;
		break;
		case 4:
			return &PORTD;
		break;
		case 5:
			return &PORTD;
		break;
		case 6:
			return &PORTD;
		break;
		case 7:
			return &PORTD;
		break;
		case 8:
			return &PORTB;
		break;
		case 9:
			return &PORTB;
		break;
		case 10:
			return &PORTB;
		break;
		case 11:
			return &PORTB;
		break;
		case 12:
			return &PORTB;
		break;
		case 13:
			return &PORTB;
		break;
		default:
			return NULL;
		break;
	}
}

unsigned char pinToDigitalPinMask(unsigned char element) {
	switch(element) {
		case 2:
			return B00000100;
		break;
		case 3:
			return B00001000;
		break;
		case 4:
			return B00010000;
		break;
		case 5:
			return B00100000;
		break;
		case 6:
			return B01000000;
		break;
		case 7:
			return B10000000;
		break;
		case 8:
			return B00000001;
		break;
		case 9:
			return B00000010;
		break;
		case 10:
			return B00000100;
		break;
		case 11:
			return B00001000;
		break;
		case 12:
			return B00010000;
		break;
		case 13:
			return B00100000;
		break;
		default:
			return element;
		break;
	}
}

unsigned char analogReferenceToAnalogReferenceMask(unsigned char element) {
	switch(element) {
		case DEFAULT:
			return B01000000;
		break;
		case INTERNAL:
			return B11000000;
		break;
		case EXTERNAL:
			return B00000000;
		break;
		default:
			return element;
		break;
	}
}

unsigned char analogPrescalerToAnalogPrescalerMask(unsigned char element) {
	switch(element) {
		case 2:
			return B00000001;
		break;
		case 4:
			return B00000010;
		break;
		case 8:
			return B00000011;
		break;
		case 16:
			return B00000100;
		break;
		case 32:
			return B00000101;
		break;
		case 64:
			return B00000110;
		break;
		case 128:
			return B00000111;
		break;
		default:
			return element;
		break;
	}
}

unsigned char pinToAnalogPinMask(unsigned char element) {
	switch(element) {
		case 0:
			return B00000000;
		break;
		case 1:
			return B00000001;
		break;
		case 2:
			return B00000010;
		break;
		case 3:
			return B00000011;
		break;
		case 4:
			return B00000100;
		break;
		case 5:
			return B00000101;
		break;
		case 6:
			return B00000110;
		break;
		case 7:
			return B00000111;
		break;
		default:
			return element;
		break;
	}
}

