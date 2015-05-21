unsigned char arduinoPinToMicrocontrollerPin(unsigned char element) {
	switch(element) {
		case 2:
			return 31;
		break;
		case 3:
			return 0;
		break;
		case 4:
			return 1;
		break;
		case 5:
			return 8;
		break;
		case 6:
			return 9;
		break;
		case 7:
			return 10;
		break;
		case 8:
			return 11;
		break;
		case 9:
			return 12;
		break;
		case 10:
			return 13;
		break;
		case 11:
			return 14;
		break;
		case 12:
			return 15;
		break;
		case 13:
			return 16;
		break;
		default:
			return element;
		break;
	}
}

