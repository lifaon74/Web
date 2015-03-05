#ifndef FE_TO_H
#define FE_TO_H

	#define FE_NONE 255

	#if defined(DDRB)
		#define FE_DIGITAL_PORT_0_MODE DDRB
	#endif
	#if defined(PINB)
		#define FE_DIGITAL_PORT_0_IN PINB
	#endif
	#if defined(PORTB)
		#define FE_DIGITAL_PORT_0_OUT PORTB
	#endif
	#if defined(DDRC)
		#define FE_DIGITAL_PORT_1_MODE DDRC
	#endif
	#if defined(PINC)
		#define FE_DIGITAL_PORT_1_IN PINC
	#endif
	#if defined(PORTC)
		#define FE_DIGITAL_PORT_1_OUT PORTC
	#endif
	#if defined(DDRD)
		#define FE_DIGITAL_PORT_2_MODE DDRD
	#endif
	#if defined(PIND)
		#define FE_DIGITAL_PORT_2_IN PIND
	#endif
	#if defined(PORTD)
		#define FE_DIGITAL_PORT_2_OUT PORTD
	#endif
	#if defined(DDRE)
		#define FE_DIGITAL_PORT_3_MODE DDRE
	#endif
	#if defined(PINE)
		#define FE_DIGITAL_PORT_3_IN PINE
	#endif
	#if defined(PORTE)
		#define FE_DIGITAL_PORT_3_OUT PORTE
	#endif
	#if defined(DDRF)
		#define FE_DIGITAL_PORT_4_MODE DDRF
	#endif
	#if defined(PINF)
		#define FE_DIGITAL_PORT_4_IN PINF
	#endif
	#if defined(PORTF)
		#define FE_DIGITAL_PORT_4_OUT PORTF
	#endif
	#if defined(PCMSK0)
		#define FE_INTERRUPT_GROUP_0 PCMSK0
	#endif
	#if defined(B00000001)
		#define FE_INTERRUPT_GROUP_0_MASK B00000001
	#endif
	#if defined(PCINT0_vect)
		#define FE_INTERRUPT_GROUP_0_INTR_NAME PCINT0_vect
	#endif
	#if defined(PCMSK1)
		#define FE_INTERRUPT_GROUP_1 PCMSK1
	#endif
	#if defined(B00000010)
		#define FE_INTERRUPT_GROUP_1_MASK B00000010
	#endif
	#if defined(PCINT1_vect)
		#define FE_INTERRUPT_GROUP_1_INTR_NAME PCINT1_vect
	#endif
	#if defined(PCMSK2)
		#define FE_INTERRUPT_GROUP_2 PCMSK2
	#endif
	#if defined(B00000100)
		#define FE_INTERRUPT_GROUP_2_MASK B00000100
	#endif
	#if defined(PCINT2_vect)
		#define FE_INTERRUPT_GROUP_2_INTR_NAME PCINT2_vect
	#endif


	volatile unsigned char* FE_portTo_DIGITAL_PORT_MODE(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PORT_0_MODE)
				case 0:
					return &FE_DIGITAL_PORT_0_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_1_MODE)
				case 1:
					return &FE_DIGITAL_PORT_1_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_2_MODE)
				case 2:
					return &FE_DIGITAL_PORT_2_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_3_MODE)
				case 3:
					return &FE_DIGITAL_PORT_3_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_4_MODE)
				case 4:
					return &FE_DIGITAL_PORT_4_MODE;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	volatile unsigned char* FE_portTo_DIGITAL_PORT_IN(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PORT_0_IN)
				case 0:
					return &FE_DIGITAL_PORT_0_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_1_IN)
				case 1:
					return &FE_DIGITAL_PORT_1_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_2_IN)
				case 2:
					return &FE_DIGITAL_PORT_2_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_3_IN)
				case 3:
					return &FE_DIGITAL_PORT_3_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_4_IN)
				case 4:
					return &FE_DIGITAL_PORT_4_IN;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	volatile unsigned char* FE_portTo_DIGITAL_PORT_OUT(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PORT_0_OUT)
				case 0:
					return &FE_DIGITAL_PORT_0_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_1_OUT)
				case 1:
					return &FE_DIGITAL_PORT_1_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_2_OUT)
				case 2:
					return &FE_DIGITAL_PORT_2_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_3_OUT)
				case 3:
					return &FE_DIGITAL_PORT_3_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PORT_4_OUT)
				case 4:
					return &FE_DIGITAL_PORT_4_OUT;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}


	volatile unsigned char* FE_groupTo_INTERRUPT_GROUP(unsigned char element) {
		switch(element) {
			#if defined(FE_INTERRUPT_GROUP_0)
				case 0:
					return &FE_INTERRUPT_GROUP_0;
				break;
			#endif
			#if defined(FE_INTERRUPT_GROUP_1)
				case 1:
					return &FE_INTERRUPT_GROUP_1;
				break;
			#endif
			#if defined(FE_INTERRUPT_GROUP_2)
				case 2:
					return &FE_INTERRUPT_GROUP_2;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}


	unsigned char FE_pinTo_DIGITAL_PIN_MASK(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PIN_D0_MASK)
				case 0:
					return FE_DIGITAL_PIN_D0_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D1_MASK)
				case 1:
					return FE_DIGITAL_PIN_D1_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D2_MASK)
				case 2:
					return FE_DIGITAL_PIN_D2_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D3_MASK)
				case 3:
					return FE_DIGITAL_PIN_D3_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D4_MASK)
				case 4:
					return FE_DIGITAL_PIN_D4_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D5_MASK)
				case 5:
					return FE_DIGITAL_PIN_D5_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D6_MASK)
				case 6:
					return FE_DIGITAL_PIN_D6_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D7_MASK)
				case 7:
					return FE_DIGITAL_PIN_D7_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D8_MASK)
				case 8:
					return FE_DIGITAL_PIN_D8_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D9_MASK)
				case 9:
					return FE_DIGITAL_PIN_D9_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D10_MASK)
				case 10:
					return FE_DIGITAL_PIN_D10_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D11_MASK)
				case 11:
					return FE_DIGITAL_PIN_D11_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D12_MASK)
				case 12:
					return FE_DIGITAL_PIN_D12_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D13_MASK)
				case 13:
					return FE_DIGITAL_PIN_D13_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D14_MASK)
				case 14:
					return FE_DIGITAL_PIN_D14_MASK;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D15_MASK)
				case 15:
					return FE_DIGITAL_PIN_D15_MASK;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

	volatile unsigned char* FE_pinTo_DIGITAL_PIN_PORT_MODE(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PIN_D0_PORT_MODE)
				case 0:
					return &FE_DIGITAL_PIN_D0_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D1_PORT_MODE)
				case 1:
					return &FE_DIGITAL_PIN_D1_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D2_PORT_MODE)
				case 2:
					return &FE_DIGITAL_PIN_D2_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D3_PORT_MODE)
				case 3:
					return &FE_DIGITAL_PIN_D3_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D4_PORT_MODE)
				case 4:
					return &FE_DIGITAL_PIN_D4_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D5_PORT_MODE)
				case 5:
					return &FE_DIGITAL_PIN_D5_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D6_PORT_MODE)
				case 6:
					return &FE_DIGITAL_PIN_D6_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D7_PORT_MODE)
				case 7:
					return &FE_DIGITAL_PIN_D7_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D8_PORT_MODE)
				case 8:
					return &FE_DIGITAL_PIN_D8_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D9_PORT_MODE)
				case 9:
					return &FE_DIGITAL_PIN_D9_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D10_PORT_MODE)
				case 10:
					return &FE_DIGITAL_PIN_D10_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D11_PORT_MODE)
				case 11:
					return &FE_DIGITAL_PIN_D11_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D12_PORT_MODE)
				case 12:
					return &FE_DIGITAL_PIN_D12_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D13_PORT_MODE)
				case 13:
					return &FE_DIGITAL_PIN_D13_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D14_PORT_MODE)
				case 14:
					return &FE_DIGITAL_PIN_D14_PORT_MODE;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D15_PORT_MODE)
				case 15:
					return &FE_DIGITAL_PIN_D15_PORT_MODE;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	volatile unsigned char* FE_pinTo_DIGITAL_PIN_PORT_IN(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PIN_D0_PORT_IN)
				case 0:
					return &FE_DIGITAL_PIN_D0_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D1_PORT_IN)
				case 1:
					return &FE_DIGITAL_PIN_D1_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D2_PORT_IN)
				case 2:
					return &FE_DIGITAL_PIN_D2_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D3_PORT_IN)
				case 3:
					return &FE_DIGITAL_PIN_D3_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D4_PORT_IN)
				case 4:
					return &FE_DIGITAL_PIN_D4_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D5_PORT_IN)
				case 5:
					return &FE_DIGITAL_PIN_D5_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D6_PORT_IN)
				case 6:
					return &FE_DIGITAL_PIN_D6_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D7_PORT_IN)
				case 7:
					return &FE_DIGITAL_PIN_D7_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D8_PORT_IN)
				case 8:
					return &FE_DIGITAL_PIN_D8_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D9_PORT_IN)
				case 9:
					return &FE_DIGITAL_PIN_D9_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D10_PORT_IN)
				case 10:
					return &FE_DIGITAL_PIN_D10_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D11_PORT_IN)
				case 11:
					return &FE_DIGITAL_PIN_D11_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D12_PORT_IN)
				case 12:
					return &FE_DIGITAL_PIN_D12_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D13_PORT_IN)
				case 13:
					return &FE_DIGITAL_PIN_D13_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D14_PORT_IN)
				case 14:
					return &FE_DIGITAL_PIN_D14_PORT_IN;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D15_PORT_IN)
				case 15:
					return &FE_DIGITAL_PIN_D15_PORT_IN;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	volatile unsigned char* FE_pinTo_DIGITAL_PIN_PORT_OUT(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PIN_D0_PORT_OUT)
				case 0:
					return &FE_DIGITAL_PIN_D0_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D1_PORT_OUT)
				case 1:
					return &FE_DIGITAL_PIN_D1_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D2_PORT_OUT)
				case 2:
					return &FE_DIGITAL_PIN_D2_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D3_PORT_OUT)
				case 3:
					return &FE_DIGITAL_PIN_D3_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D4_PORT_OUT)
				case 4:
					return &FE_DIGITAL_PIN_D4_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D5_PORT_OUT)
				case 5:
					return &FE_DIGITAL_PIN_D5_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D6_PORT_OUT)
				case 6:
					return &FE_DIGITAL_PIN_D6_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D7_PORT_OUT)
				case 7:
					return &FE_DIGITAL_PIN_D7_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D8_PORT_OUT)
				case 8:
					return &FE_DIGITAL_PIN_D8_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D9_PORT_OUT)
				case 9:
					return &FE_DIGITAL_PIN_D9_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D10_PORT_OUT)
				case 10:
					return &FE_DIGITAL_PIN_D10_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D11_PORT_OUT)
				case 11:
					return &FE_DIGITAL_PIN_D11_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D12_PORT_OUT)
				case 12:
					return &FE_DIGITAL_PIN_D12_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D13_PORT_OUT)
				case 13:
					return &FE_DIGITAL_PIN_D13_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D14_PORT_OUT)
				case 14:
					return &FE_DIGITAL_PIN_D14_PORT_OUT;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D15_PORT_OUT)
				case 15:
					return &FE_DIGITAL_PIN_D15_PORT_OUT;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	unsigned char FE_pinTo_DIGITAL_PIN_PORT_NUMBER(unsigned char element) {
		switch(element) {
			#if defined(FE_DIGITAL_PIN_D0_PORT_NUMBER)
				case 0:
					return FE_DIGITAL_PIN_D0_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D1_PORT_NUMBER)
				case 1:
					return FE_DIGITAL_PIN_D1_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D2_PORT_NUMBER)
				case 2:
					return FE_DIGITAL_PIN_D2_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D3_PORT_NUMBER)
				case 3:
					return FE_DIGITAL_PIN_D3_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D4_PORT_NUMBER)
				case 4:
					return FE_DIGITAL_PIN_D4_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D5_PORT_NUMBER)
				case 5:
					return FE_DIGITAL_PIN_D5_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D6_PORT_NUMBER)
				case 6:
					return FE_DIGITAL_PIN_D6_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D7_PORT_NUMBER)
				case 7:
					return FE_DIGITAL_PIN_D7_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D8_PORT_NUMBER)
				case 8:
					return FE_DIGITAL_PIN_D8_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D9_PORT_NUMBER)
				case 9:
					return FE_DIGITAL_PIN_D9_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D10_PORT_NUMBER)
				case 10:
					return FE_DIGITAL_PIN_D10_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D11_PORT_NUMBER)
				case 11:
					return FE_DIGITAL_PIN_D11_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D12_PORT_NUMBER)
				case 12:
					return FE_DIGITAL_PIN_D12_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D13_PORT_NUMBER)
				case 13:
					return FE_DIGITAL_PIN_D13_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D14_PORT_NUMBER)
				case 14:
					return FE_DIGITAL_PIN_D14_PORT_NUMBER;
				break;
			#endif
			#if defined(FE_DIGITAL_PIN_D15_PORT_NUMBER)
				case 15:
					return FE_DIGITAL_PIN_D15_PORT_NUMBER;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

	unsigned char FE_pinTo_ANALOG_PIN_MASK(unsigned char element) {
		switch(element) {
			#if defined(FE_ANALOG_PIN_D0_MASK)
				case 0:
					return FE_ANALOG_PIN_D0_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D1_MASK)
				case 1:
					return FE_ANALOG_PIN_D1_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D2_MASK)
				case 2:
					return FE_ANALOG_PIN_D2_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D3_MASK)
				case 3:
					return FE_ANALOG_PIN_D3_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D4_MASK)
				case 4:
					return FE_ANALOG_PIN_D4_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D5_MASK)
				case 5:
					return FE_ANALOG_PIN_D5_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D6_MASK)
				case 6:
					return FE_ANALOG_PIN_D6_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D7_MASK)
				case 7:
					return FE_ANALOG_PIN_D7_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D8_MASK)
				case 8:
					return FE_ANALOG_PIN_D8_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D9_MASK)
				case 9:
					return FE_ANALOG_PIN_D9_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D10_MASK)
				case 10:
					return FE_ANALOG_PIN_D10_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D11_MASK)
				case 11:
					return FE_ANALOG_PIN_D11_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D12_MASK)
				case 12:
					return FE_ANALOG_PIN_D12_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D13_MASK)
				case 13:
					return FE_ANALOG_PIN_D13_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D14_MASK)
				case 14:
					return FE_ANALOG_PIN_D14_MASK;
				break;
			#endif
			#if defined(FE_ANALOG_PIN_D15_MASK)
				case 15:
					return FE_ANALOG_PIN_D15_MASK;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

	unsigned char FE_pinTo_INTERRUPT_PIN_MASK(unsigned char element) {
		switch(element) {
			#if defined(FE_INTERRUPT_PIN_D0_MASK)
				case 0:
					return FE_INTERRUPT_PIN_D0_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D1_MASK)
				case 1:
					return FE_INTERRUPT_PIN_D1_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D2_MASK)
				case 2:
					return FE_INTERRUPT_PIN_D2_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D3_MASK)
				case 3:
					return FE_INTERRUPT_PIN_D3_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D4_MASK)
				case 4:
					return FE_INTERRUPT_PIN_D4_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D5_MASK)
				case 5:
					return FE_INTERRUPT_PIN_D5_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D6_MASK)
				case 6:
					return FE_INTERRUPT_PIN_D6_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D7_MASK)
				case 7:
					return FE_INTERRUPT_PIN_D7_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D8_MASK)
				case 8:
					return FE_INTERRUPT_PIN_D8_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D9_MASK)
				case 9:
					return FE_INTERRUPT_PIN_D9_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D10_MASK)
				case 10:
					return FE_INTERRUPT_PIN_D10_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D11_MASK)
				case 11:
					return FE_INTERRUPT_PIN_D11_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D12_MASK)
				case 12:
					return FE_INTERRUPT_PIN_D12_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D13_MASK)
				case 13:
					return FE_INTERRUPT_PIN_D13_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D14_MASK)
				case 14:
					return FE_INTERRUPT_PIN_D14_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D15_MASK)
				case 15:
					return FE_INTERRUPT_PIN_D15_MASK;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

	unsigned char FE_pinTo_INTERRUPT_PIN_GROUP_MASK(unsigned char element) {
		switch(element) {
			#if defined(FE_INTERRUPT_PIN_D0_GROUP_MASK)
				case 0:
					return FE_INTERRUPT_PIN_D0_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D1_GROUP_MASK)
				case 1:
					return FE_INTERRUPT_PIN_D1_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D2_GROUP_MASK)
				case 2:
					return FE_INTERRUPT_PIN_D2_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D3_GROUP_MASK)
				case 3:
					return FE_INTERRUPT_PIN_D3_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D4_GROUP_MASK)
				case 4:
					return FE_INTERRUPT_PIN_D4_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D5_GROUP_MASK)
				case 5:
					return FE_INTERRUPT_PIN_D5_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D6_GROUP_MASK)
				case 6:
					return FE_INTERRUPT_PIN_D6_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D7_GROUP_MASK)
				case 7:
					return FE_INTERRUPT_PIN_D7_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D8_GROUP_MASK)
				case 8:
					return FE_INTERRUPT_PIN_D8_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D9_GROUP_MASK)
				case 9:
					return FE_INTERRUPT_PIN_D9_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D10_GROUP_MASK)
				case 10:
					return FE_INTERRUPT_PIN_D10_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D11_GROUP_MASK)
				case 11:
					return FE_INTERRUPT_PIN_D11_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D12_GROUP_MASK)
				case 12:
					return FE_INTERRUPT_PIN_D12_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D13_GROUP_MASK)
				case 13:
					return FE_INTERRUPT_PIN_D13_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D14_GROUP_MASK)
				case 14:
					return FE_INTERRUPT_PIN_D14_GROUP_MASK;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D15_GROUP_MASK)
				case 15:
					return FE_INTERRUPT_PIN_D15_GROUP_MASK;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

	volatile unsigned char* FE_pinTo_INTERRUPT_PIN_GROUP(unsigned char element) {
		switch(element) {
			#if defined(FE_INTERRUPT_PIN_D0_GROUP)
				case 0:
					return &FE_INTERRUPT_PIN_D0_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D1_GROUP)
				case 1:
					return &FE_INTERRUPT_PIN_D1_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D2_GROUP)
				case 2:
					return &FE_INTERRUPT_PIN_D2_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D3_GROUP)
				case 3:
					return &FE_INTERRUPT_PIN_D3_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D4_GROUP)
				case 4:
					return &FE_INTERRUPT_PIN_D4_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D5_GROUP)
				case 5:
					return &FE_INTERRUPT_PIN_D5_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D6_GROUP)
				case 6:
					return &FE_INTERRUPT_PIN_D6_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D7_GROUP)
				case 7:
					return &FE_INTERRUPT_PIN_D7_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D8_GROUP)
				case 8:
					return &FE_INTERRUPT_PIN_D8_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D9_GROUP)
				case 9:
					return &FE_INTERRUPT_PIN_D9_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D10_GROUP)
				case 10:
					return &FE_INTERRUPT_PIN_D10_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D11_GROUP)
				case 11:
					return &FE_INTERRUPT_PIN_D11_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D12_GROUP)
				case 12:
					return &FE_INTERRUPT_PIN_D12_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D13_GROUP)
				case 13:
					return &FE_INTERRUPT_PIN_D13_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D14_GROUP)
				case 14:
					return &FE_INTERRUPT_PIN_D14_GROUP;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D15_GROUP)
				case 15:
					return &FE_INTERRUPT_PIN_D15_GROUP;
				break;
			#endif
				default:
					return NULL;
				break;
		}
	}

	unsigned char FE_pinTo_INTERRUPT_PIN_GROUP_NUMBER(unsigned char element) {
		switch(element) {
			#if defined(FE_INTERRUPT_PIN_D0_GROUP_NUMBER)
				case 0:
					return FE_INTERRUPT_PIN_D0_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D1_GROUP_NUMBER)
				case 1:
					return FE_INTERRUPT_PIN_D1_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D2_GROUP_NUMBER)
				case 2:
					return FE_INTERRUPT_PIN_D2_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D3_GROUP_NUMBER)
				case 3:
					return FE_INTERRUPT_PIN_D3_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D4_GROUP_NUMBER)
				case 4:
					return FE_INTERRUPT_PIN_D4_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D5_GROUP_NUMBER)
				case 5:
					return FE_INTERRUPT_PIN_D5_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D6_GROUP_NUMBER)
				case 6:
					return FE_INTERRUPT_PIN_D6_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D7_GROUP_NUMBER)
				case 7:
					return FE_INTERRUPT_PIN_D7_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D8_GROUP_NUMBER)
				case 8:
					return FE_INTERRUPT_PIN_D8_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D9_GROUP_NUMBER)
				case 9:
					return FE_INTERRUPT_PIN_D9_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D10_GROUP_NUMBER)
				case 10:
					return FE_INTERRUPT_PIN_D10_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D11_GROUP_NUMBER)
				case 11:
					return FE_INTERRUPT_PIN_D11_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D12_GROUP_NUMBER)
				case 12:
					return FE_INTERRUPT_PIN_D12_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D13_GROUP_NUMBER)
				case 13:
					return FE_INTERRUPT_PIN_D13_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D14_GROUP_NUMBER)
				case 14:
					return FE_INTERRUPT_PIN_D14_GROUP_NUMBER;
				break;
			#endif
			#if defined(FE_INTERRUPT_PIN_D15_GROUP_NUMBER)
				case 15:
					return FE_INTERRUPT_PIN_D15_GROUP_NUMBER;
				break;
			#endif
				default:
					return FE_NONE;
				break;
		}
	}

#endif