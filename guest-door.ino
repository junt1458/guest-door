#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         9
#define SS_PIN          10

MFRC522 mfrc522(SS_PIN, RST_PIN);

void setup() {
  pinMode(5, OUTPUT);
  pinMode(6, OUTPUT);
  pinMode(7, INPUT);
  pinMode(8, INPUT);
  Serial.begin(9600);
  while (!Serial);
  SPI.begin();
  mfrc522.PCD_Init();
  delay(4);
}

boolean closing = false;
int wait = 0;
int input;
String in_str;
void loop() {
  input = Serial.read();
  in_str = "";
  while (input != -1) {
    if(char(input) == '\n') {
      input = -1;
    } else {
      in_str += String(char(input));
      input = Serial.read();
    }
  }

  if(in_str == "ON") {
    digitalWrite(5, HIGH);
    digitalWrite(6, LOW);
    closing = false;
  } else if(in_str == "ON_R") {
    digitalWrite(5, LOW);
    digitalWrite(6, HIGH);
    closing = true;
  } else if(in_str == "OFF") {
    digitalWrite(5, LOW);
    digitalWrite(6, LOW);
  }

  if(closing) {
    //扉閉確認用スイッチ(D7)の入力確認
    if(digitalRead(7) == HIGH) {
      digitalWrite(5, LOW);
      digitalWrite(6, LOW);
    }
  } else {
    //扉開確認用スイッチ(D8)の入力確認
    if(digitalRead(8) == HIGH) {
      digitalWrite(5, LOW);
      digitalWrite(6, LOW);
    }
  }

  if(wait == 10) {
    if (!mfrc522.PICC_IsNewCardPresent()) return;
    if (!mfrc522.PICC_ReadCardSerial()) return;
    
    String ubuf = "";
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
      ubuf =  String(mfrc522.uid.uidByte[i], HEX);
      if(ubuf.length() == 1){
        ubuf = "0" + ubuf;
      }
      uid += ubuf;
    }
    uid.toUpperCase();
    mfrc522.PICC_HaltA();
    
    Serial.print("READ_" + uid);
    wait = 0;
  }
  delay(100);
  wait++;
}
