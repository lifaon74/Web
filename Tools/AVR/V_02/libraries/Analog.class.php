<?php 

/*


	#define FE_analog8bits() ADMUX |= B00100000
	#define FE_analog10bits() ADMUX &= B11011111

	#if defined(__AVR_ATmega328P__)
		#define FE_analogPin(pinMask) ADMUX = (ADMUX & B11110000) | pinMask
	#elif defined(__AVR_ATmega32U4__)
		#define FE_analogPin(pinMask)	\
			ADMUX = (ADMUX & B11100000) | (pinMask & B00011111); \
			ADCSRB = (ADCSRB & B11011111) | (pinMask & B00100000)
	#else
		// must be completed
		#define FE_analogPin(pinMask) ADMUX = (ADMUX & B11110000) | pinMask
	#endif
	

	
	*/
	
	
class AnalogClass {
	protected $constructor;
	
	public function __construct($constructor) {
		$this->constructor	= $constructor;
		
		$this->cppCode	= "";
		$this->cppCode .= "unsigned int _analogRead(unsigned char pinMask) {" . EOL() .
			TAB() . $this->constructor->board->microcontroller->setAnalogPin("pinMask") . ";" . EOL() .
			TAB() . $this->constructor->board->microcontroller->startAnalogConversion() . ";" . EOL() .
			TAB() . "while(" . $this->constructor->board->microcontroller->getAnalogConversionStatus() . ");" . EOL() .
			TAB() . "return " . $this->constructor->board->microcontroller->analogRead(10) . ";" . EOL() .
		"}" . EOL();
		
		$this->constructor->createInclude("Analog", $this->cppCode);
	}
	
	
	public function reference($reference) {
		$reference = $this->constructor->formatConstant($reference);
		
		switch(true) {
			case ($reference === $this->constructor->DEFAULT):
				$reference_string = "DEFAULT";
			break;
			case ($reference === $this->constructor->INTERNAL):
				$reference_string = "INTERNAL";
			break;
			case ($reference === $this->constructor->EXTERNAL):
				$reference_string = "EXTERNAL";
			break;
			default:
				$reference_string = $reference;
		}
		
		return $this->constructor->board->microcontroller->setAnalogReference($this->constructor->board->callFunction('analogReferenceToAnalogReferenceMask', $reference_string));
	}
	
	public function prescaler($prescaler) {
		$prescaler = $this->constructor->formatConstant($prescaler);
		return $this->constructor->board->microcontroller->setAnalogPrescaler($this->constructor->board->callFunction('analogPrescalerToAnalogPrescalerMask', $prescaler));
	}
	
	
	public function pin($pin) {
		$pin = $this->constructor->formatConstant($pin);
		return $this->constructor->board->microcontroller->setAnalogPin($this->constructor->board->callFunction('pinToAnalogPinMask', $pin));
	}
	
	public function startConversion() {
		return $this->constructor->board->microcontroller->startAnalogConversion();
	}
	
	public function conversionIsIncomplete() {
		return $this->constructor->board->microcontroller->getAnalogConversionStatus();
	}
	
	public function read8bits() {
		return $this->constructor->board->microcontroller->analogRead(8);
	}
	
	public function read10bits() {
		return $this->constructor->board->microcontroller->analogRead(10);
	}
	
	
	public function read($pin) {
		$pin = $this->constructor->formatConstant($pin);
		return "_analogRead(". $this->constructor->board->callFunction('pinToAnalogPinMask', $pin) . ");";
	}
	
	

}

?>