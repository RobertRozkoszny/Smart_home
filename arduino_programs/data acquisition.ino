#include "DHT.h"

DHT dht;        // Inicjalizacja czujnika DHT11

// Przypisanie pinow do których sa podlaczone wyprowadzenia odpowiednich czujników
  int dht_PIN= 5;       //pin cyfrowy
  int foto_PIN = 5;     // pin analogowy 
  int MQ2_PIN_ANALOGOWY = 4;
  int MQ2_PIN_CYFROWY = 3;
  int PIR_PIN = 2;    
  int oswietlenie_PIN = 6;

  int gaz;
  float wilgotnosc;
  float temperatura;
  int foto_dane;
  int ruch;
  String outputString = "";        // String przechowujący dane przychodzące
  char data[50];                   //Bufor używany do komunikacji
  bool stringComplete = false;     //Flaga zakończenia wiadomosci
  
  void odczyt();
  void zapis();
  void  wykrycie_zbocza_PIR();
  void mq2_alarm();
  
void setup()
{
 
   //delay(3*dht.getMinimumSamplingPeriod());
  pinMode(PIR_PIN, INPUT);   //ustawienie pinów Arduino jako wejście
  pinMode(foto_PIN, INPUT);
  pinMode(oswietlenie_PIN, OUTPUT);
  dht.setup(dht_PIN);
  outputString.reserve(100);
  attachInterrupt(digitalPinToInterrupt(PIR_PIN), wykrycie_zbocza_PIR, CHANGE); // Przerwanie. Gdy na pinie 2 wystąpi  zmiana stanu 
                                                                                //   zostanie wykonana funkcja wykrycie_zbocza_PIR
 // attachInterrupt(digitalPinToInterrupt(MQ2_PIN_CYFROWY), mq2_alarm, LOW); 
  Serial.begin(9600);
   
}


       ///// /////////     MAIN      ////////////////////
void loop()
{
  //Czekanie na odebraną wiadomość.
  if (stringComplete) {
      if(data[0]=='t'){
      odczyt();
      wykrycie_zbocza_PIR();
      }
      if(data[0]=='w'){
        zapis();
      }
      if(data[0]=='s'){
        wykrycie_zbocza_PIR();
      }

    //Czyszczenie bufora oraz zerowanie flagi zakończenia przesyłu wiadomości
    for( int x = 0; x < sizeof(data);  ++x ){
        data[x] = (char)0;
        stringComplete = false;
    }
  }
} 

  // Przerwanie od UART
void serialEvent(){
 int i=0;
  do{
    // get the new byte:
         char inChar = (char)Serial.read();
         //Zapis do bufora danychzapis
          data[i] = inChar;
       
     i++;
     delay(10);
     }while((Serial.available()) );
  
     stringComplete = true;
}

void odczyt(){
  
  do{
   wilgotnosc =dht.getHumidity();
   temperatura =dht.getTemperature();                                 
   foto_dane =analogRead(foto_PIN); 
   gaz= analogRead(MQ2_PIN_ANALOGOWY);
   gaz=map(gaz, 0, 1023,0 ,100);
      }while(dht.getStatusString() == "TIMEOUT");

outputString += "t";
outputString += temperatura;
outputString += ';';      
outputString += wilgotnosc;
outputString += ';';
outputString += gaz;
outputString += ';';
Serial.println(outputString);

           outputString = "";
}

void zapis(){
oswietlenie_PIN==data[1];
}

void  wykrycie_zbocza_PIR(){
 
ruch = digitalRead(PIR_PIN);
foto_dane =analogRead(foto_PIN); 

outputString += "r";
outputString += ruch;
outputString += ';';
outputString += foto_dane;
outputString += ';';
Serial.println(outputString);

           outputString = ""; 
}

void mq2_alarm(){
  //Wykryto alarm czujnika dymu.
outputString += "a";
  Serial.println(outputString);
           outputString = "";
  }
