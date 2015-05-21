unsigned int _analogRead(unsigned char pinMask) {
	ADMUX = (ADMUX & B11110000) | pinMask;;
	ADCSRA |= B01000000;;
	while(((bool) (ADCSRA & B01000000)));
	return (ADCL | (ADCH << 8));
}
