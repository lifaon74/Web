#define ARDUINO_YUN
//#define DEBUG

#include <SPI.h>
#include <Bridge.h>
#include <HttpClient.h>

HttpClient client;
String url, data;


bool wifi_available() {
	Process wifiCheck;
	bool _wifi_available = false;
	
	wifiCheck.runShellCommand("/usr/bin/pretty-wifi-info.lua");
	
	while(!wifiCheck.available());
	
	/*
		no connection :
			Current WiFi configuration
			Interface name: radio0.network1
			Active for: 0 minutes
			MAC address: 00:00:00:00:00:00
			RX/TX: 0/0 KBs
			
		wifi :
			Current WiFi configuration
			SSID: ThingBook_Network
			Mode: Client
			Signal: 71%
			Encryption method: None
			Interface name: wlan0
			Active for: 0 minutes
			IP address: 10.86.24.131/255.255.255.0
			MAC address: 90:A2:DA:F0:01:BA
			RX/TX: 8/12 KBs
	*/
	
	for(unsigned char i = 0; i < 27; i++) {
		if(wifiCheck.available()) {
			wifiCheck.read();
		}
	}
	
	if(wifiCheck.available()) {
		unsigned char _char = wifiCheck.read();
		switch(_char) {
			case 'S':
				_wifi_available = true;
			break;
		}
	}
	
	while(wifiCheck.available() > 0) {
		wifiCheck.read();
	}
	
	return _wifi_available;
}


void register_object() {
	data = "action=register";
	client.post(url, data);
	
	unsigned char i = 0;
	while(client.available()) {
		unsigned char _char = client.read();
		
		if(i == 0) {
			switch(_char) {
				case '0':
					#if defined(DEBUG)
						Serial.println("device registered");
					#endif	
				break;
				case '1':
					#if defined(DEBUG)
						Serial.println("device failed to register");
					#endif
				break;
				default:
					#if defined(DEBUG)
						Serial.println("program error in register_object");
					#endif
				break;
			}
			
		}
		
		i++;
	}
}

void request_for_a_new_relationship() {
	data = "action=ask_relationship";
	client.post(url, data);
	
	unsigned char i = 0;
	while(client.available()) {
		unsigned char _char = client.read();
		
		if(i == 0) {
			switch(_char) {
				case '0':
					#if defined(DEBUG)
						Serial.println("request a \"friend\" relationship");
					#endif
				break;
				case '1':
				default:
					#if defined(DEBUG)
						Serial.println("program error in request_for_a_new_relationship");
					#endif
				break;
			}
		}
	}
}

bool is_friend() {
	data = "action=is_friend";
	client.post(url, data);
	
	unsigned char i = 0;
	while(client.available()) {
		unsigned char _char = client.read();
		
		if(i == 0) {
			switch(_char) {
				case '0':
					#if defined(DEBUG)
						Serial.println("is not friend");
					#endif
					return false;
				break;
				case '1':
					#if defined(DEBUG)
						Serial.println("is friend");
					#endif
					return true;
				default:
					#if defined(DEBUG)
						Serial.println("program error in is_friend");
					#endif
					return false;
				break;
			}
			
		}
	}
	
	return false;
}

bool switch_is_on() {
	data = "action=switch_is_on";
	client.post(url, data);
	
	unsigned char i = 0;
	while(client.available()) {
		unsigned char _char = client.read();
		
		if(i == 0) {
			switch(_char) {
				case '0':
					#if defined(DEBUG)
						Serial.println("switch is off");
					#endif
					return false;
				break;
				case '1':
					#if defined(DEBUG)
						Serial.println("switch is on");
					#endif
					return true;
				default:
					#if defined(DEBUG)
						Serial.println("program error in switch_is_on");
					#endif
					return false;
				break;
			}
		}
	}
	
	return false;
}


void setup() {
	
}


void loop() {
	delay(1000);
	
	Serial.begin(115200);
	
	pinMode(2, OUTPUT);
	pinMode(3, OUTPUT);
	
		#if defined(DEBUG)
			Serial.print("waiting for bridge...");
		#endif
	
	Bridge.begin();
	
		#if defined(DEBUG)
			Serial.println("OK");
		#endif
	
		#if defined(DEBUG)
			Serial.print("waiting for wifi...");
		#endif
	
	while(!wifi_available());
	
		#if defined(DEBUG)
			Serial.println("OK");
		#endif
	
	url = "http://78.244.106.44/Web/UNIGE/Master/exemples/arduino/index.php";
	
	#if defined(DEBUG)
		Serial.println("init sequence : ");
	#endif
	
	
	#if defined(DEBUG)
		Serial.print("wait for serial input...");
		while(!Serial.available());
		while(Serial.available()) {
			Serial.read();
		}
		Serial.println("OK");
	#endif
	
	
	register_object();
	
	bool isFriend = is_friend();
	if(!isFriend) {
		request_for_a_new_relationship();
	}
	
	while(!isFriend) {
		delay(2000);
		isFriend = is_friend();
	}
	
	while(true) {
		if(switch_is_on()) {
			digitalWrite(2, HIGH);
		} else {
			digitalWrite(2, LOW);
		}
		
		delay(100);
	}
	
}
