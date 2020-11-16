// The load resistance on the board
#define RLOAD 22.0
// Calibration resistance at atmospheric CO2 level
#define RZERO 879.13 
#include "MQ135.h" 
#include "WiFiEsp.h"
#include <ArduinoJson.h>
#ifndef HAVE_HWSERIAL1
#include "SoftwareSerial.h"

SoftwareSerial Serial1(2, 3); // RX, TX
#endif
#define DHT11_PIN A0
MQ135 gasSensor = MQ135(A6); 
int val; 
float max_ppm = 300;
int sensorPin = A7; 
int sensorValue = 0; 
char ssid[] = "WIFI_GRATIS";            // your network SSID (name)
char pass[] = "1122334455";        // your network password
int status = WL_IDLE_STATUS;     // the Wifi radio's status
char server[] = "10.0.0.23";

WiFiEspClient client;

void setup() { 
  Serial.begin(9600);
  // initialize serial for ESP module
  Serial1.begin(9600);
  pinMode(sensorPin, INPUT); 
  pinMode(11, OUTPUT); //led
  conectarWifi();
} 
 
void loop() { 
  val = analogRead(A6); 
  float ppm = gasSensor.getPPM(); 
  Serial.print ("ppm: "); 
  Serial.println (ppm); 
  
  if (client.connect(server, 3800)) {
      
    String content = "{\"equipamento\":2,\"leitura\":"+String(ppm)+"}";
 
    Serial.println("Connected to server");
      // Make a HTTP request
    client.println("POST /api/leituras HTTP/1.1");
    client.println("Host: 10.0.0.23:3800");
    client.println("Accept: */*");
    client.println("Content-Length: " +  String(content.length()));
    client.println("Content-Type: application/json");
    client.println();
    client.println(content);

    if (ppm >= max_ppm) {
        digitalWrite(11, HIGH);
    }else{
        digitalWrite(11, LOW);
    }
 }
  client.stop();
  delay(5000); 
} 


void conectarWifi(){
  // initialize ESP module
  WiFi.init(&Serial1);

  // check for the presence of the shield
  if (WiFi.status() == WL_NO_SHIELD) {
    Serial.println("WiFi shield not present");
    // don't continue
    while (true);
  }

  // attempt to connect to WiFi network
  while ( status != WL_CONNECTED) {
    Serial.print("Attempting to connect to WPA SSID: ");
    Serial.println(ssid);
    // Connect to WPA/WPA2 network
    status = WiFi.begin(ssid, pass);
  }

  // you're connected now, so print out the data
  Serial.println("You're connected to the network");
  
  
  printWifiStatus();
  
}


void printWifiStatus()
{
  // print the SSID of the network you're attached to
  Serial.print("SSID: ");
  Serial.println(WiFi.SSID());

  // print your WiFi shield's IP address
  IPAddress ip = WiFi.localIP();
  Serial.print("IP Address: ");
  Serial.println(ip);

  // print the received signal strength
  long rssi = WiFi.RSSI();
  Serial.print("Signal strength (RSSI):");
  Serial.print(rssi);
  Serial.println(" dBm");
}
