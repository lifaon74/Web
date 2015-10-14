unsigned char digital_pinToPinMask(unsigned char element) {
	switch(element) {
		case 0:
			return 8;
		break;
		case 1:
			return 16;
		break;
		case 6:
			return 64;
		break;
		case 7:
			return 128;
		break;
		case 8:
			return 32;
		break;
		case 9:
			return 64;
		break;
		case 10:
			return 128;
		break;
		case 11:
			return 1;
		break;
		case 12:
			return 2;
		break;
		case 13:
			return 4;
		break;
		case 14:
			return 8;
		break;
		case 15:
			return 16;
		break;
		case 16:
			return 32;
		break;
		case 22:
			return 1;
		break;
		case 23:
			return 2;
		break;
		case 24:
			return 4;
		break;
		case 25:
			return 8;
		break;
		case 26:
			return 16;
		break;
		case 27:
			return 32;
		break;
		case 28:
			return 64;
		break;
		case 29:
			return 1;
		break;
		case 30:
			return 2;
		break;
		case 31:
			return 4;
		break;
		default:
			return element;
		break;
	}
}

unsigned char interrupt_pinToPinMask(unsigned char element) {
	switch(element) {
		case 0:
			return 8;
		break;
		case 1:
			return 16;
		break;
		case 6:
			return 64;
		break;
		case 7:
			return 128;
		break;
		case 8:
			return 32;
		break;
		case 9:
			return 64;
		break;
		case 10:
			return 128;
		break;
		case 11:
			return 1;
		break;
		case 12:
			return 2;
		break;
		case 13:
			return 4;
		break;
		case 14:
			return 8;
		break;
		case 15:
			return 16;
		break;
		case 16:
			return 32;
		break;
		case 22:
			return 1;
		break;
		case 23:
			return 2;
		break;
		case 24:
			return 4;
		break;
		case 25:
			return 8;
		break;
		case 26:
			return 16;
		break;
		case 27:
			return 32;
		break;
		case 28:
			return 64;
		break;
		case 29:
			return 1;
		break;
		case 30:
			return 2;
		break;
		case 31:
			return 4;
		break;
		default:
			return element;
		break;
	}
}

volatile unsigned char* digital_pinToPortIn(unsigned char element) {
	switch(element) {
		case 0:
			return &PIND;
		break;
		case 1:
			return &PIND;
		break;
		case 6:
			return &PINB;
		break;
		case 7:
			return &PINB;
		break;
		case 8:
			return &PIND;
		break;
		case 9:
			return &PIND;
		break;
		case 10:
			return &PIND;
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
		case 14:
			return &PINB;
		break;
		case 15:
			return &PINB;
		break;
		case 16:
			return &PINB;
		break;
		case 22:
			return &PINC;
		break;
		case 23:
			return &PINC;
		break;
		case 24:
			return &PINC;
		break;
		case 25:
			return &PINC;
		break;
		case 26:
			return &PINC;
		break;
		case 27:
			return &PINC;
		break;
		case 28:
			return &PINC;
		break;
		case 29:
			return &PIND;
		break;
		case 30:
			return &PIND;
		break;
		case 31:
			return &PIND;
		break;
		default:
			return NULL;
		break;
	}
}

volatile unsigned char* digital_pinToPortMode(unsigned char element) {
	switch(element) {
		case 0:
			return &DDRD;
		break;
		case 1:
			return &DDRD;
		break;
		case 6:
			return &DDRB;
		break;
		case 7:
			return &DDRB;
		break;
		case 8:
			return &DDRD;
		break;
		case 9:
			return &DDRD;
		break;
		case 10:
			return &DDRD;
		break;
		case 11:
			return &DDRB;
		break;
		case 12:
			return &DDRB;
		break;
		case 13:
			return &DDRB;
		break;
		case 14:
			return &DDRB;
		break;
		case 15:
			return &DDRB;
		break;
		case 16:
			return &DDRB;
		break;
		case 22:
			return &DDRC;
		break;
		case 23:
			return &DDRC;
		break;
		case 24:
			return &DDRC;
		break;
		case 25:
			return &DDRC;
		break;
		case 26:
			return &DDRC;
		break;
		case 27:
			return &DDRC;
		break;
		case 28:
			return &DDRC;
		break;
		case 29:
			return &DDRD;
		break;
		case 30:
			return &DDRD;
		break;
		case 31:
			return &DDRD;
		break;
		default:
			return NULL;
		break;
	}
}

unsigned char interrupt_pinToGroupNumber(unsigned char element) {
	switch(element) {
		case 0:
			return 2;
		break;
		case 1:
			return 2;
		break;
		case 6:
			return 0;
		break;
		case 7:
			return 0;
		break;
		case 8:
			return 2;
		break;
		case 9:
			return 2;
		break;
		case 10:
			return 2;
		break;
		case 11:
			return 0;
		break;
		case 12:
			return 0;
		break;
		case 13:
			return 0;
		break;
		case 14:
			return 0;
		break;
		case 15:
			return 0;
		break;
		case 16:
			return 0;
		break;
		case 22:
			return 1;
		break;
		case 23:
			return 1;
		break;
		case 24:
			return 1;
		break;
		case 25:
			return 1;
		break;
		case 26:
			return 1;
		break;
		case 27:
			return 1;
		break;
		case 28:
			return 1;
		break;
		case 29:
			return 2;
		break;
		case 30:
			return 2;
		break;
		case 31:
			return 2;
		break;
		default:
			return element;
		break;
	}
}

unsigned char interrupt_pinToPinChangeMaskRegister(unsigned char element) {
	switch(element) {
		case 0:
			return PCMSK2;
		break;
		case 1:
			return PCMSK2;
		break;
		case 6:
			return PCMSK0;
		break;
		case 7:
			return PCMSK0;
		break;
		case 8:
			return PCMSK2;
		break;
		case 9:
			return PCMSK2;
		break;
		case 10:
			return PCMSK2;
		break;
		case 11:
			return PCMSK0;
		break;
		case 12:
			return PCMSK0;
		break;
		case 13:
			return PCMSK0;
		break;
		case 14:
			return PCMSK0;
		break;
		case 15:
			return PCMSK0;
		break;
		case 16:
			return PCMSK0;
		break;
		case 22:
			return PCMSK1;
		break;
		case 23:
			return PCMSK1;
		break;
		case 24:
			return PCMSK1;
		break;
		case 25:
			return PCMSK1;
		break;
		case 26:
			return PCMSK1;
		break;
		case 27:
			return PCMSK1;
		break;
		case 28:
			return PCMSK1;
		break;
		case 29:
			return PCMSK2;
		break;
		case 30:
			return PCMSK2;
		break;
		case 31:
			return PCMSK2;
		break;
		default:
			return element;
		break;
	}
}

