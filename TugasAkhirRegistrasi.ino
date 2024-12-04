#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>

// Konfigurasi pin RFID
#define RST_PIN 22 // Pin RST pada RC522
#define SS_PIN 21  // Pin SS pada RC522

MFRC522 rfid(SS_PIN, RST_PIN);

// Konfigurasi LCD I2C
LiquidCrystal_I2C lcd(0x27, 16, 2); // Alamat I2C 0x27, ukuran LCD 16x2
// Konfigurasi LCD I2C
#define SDA_PIN 4 // Ganti dengan pin SDA yang Anda pilih
#define SCL_PIN 2 // Ganti dengan pin SCL yang Anda pilih

// Konfigurasi WiFi
const char* ssid = "Mi 11 Lite";       // Nama WiFi
const char* password = "12345678";    // Password WiFi

// URL server PHP
const char* serverURL = "http://192.168.6.165/registrasi/rfid_card.php"; // Ganti sesuai dengan IP server PHP
const char* apiKey = "123456789"; // API Key yang harus sesuai dengan server

void setup() {
  Serial.begin(115200);
  SPI.begin();             // Inisialisasi SPI
  rfid.PCD_Init();         // Inisialisasi RFID
  lcd.begin(16, 2);        // Inisialisasi LCD
  lcd.backlight();         // Hidupkan backlight LCD
  lcd.setCursor(0, 0);
  lcd.print("Sistem Absensi");

  // Hubungkan ke WiFi
  connectToWiFi();
}

void loop() {
  // Periksa apakah ada kartu yang terdeteksi
  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    return;
  }

  // Ambil UID kartu RFID
  String uid = getUID();

  // Tampilkan UID pada Serial Monitor
  Serial.println("UID: " + uid);

  // Kirim UID ke server untuk diproses
  String response = sendToServer(uid);

  // Tampilkan respons di LCD
  lcd.clear();
  lcd.setCursor(0, 0);
  if (response == "UID berhasil disimpan") {
    lcd.print("UID Tersimpan");
  } else if (response == "API Key tidak sama") {
    lcd.print("API Key Salah");
  } else {
    lcd.print("Gagal Kirim UID");
  }
  delay(3000); // Tunggu 3 detik sebelum loop berikutnya

  // Matikan deteksi kartu setelah selesai
  rfid.PICC_HaltA();
}

// Fungsi untuk mengambil UID
String getUID() {
  String uid = "";
  for (byte i = 0; i < rfid.uid.size; i++) {
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase(); // Ubah ke huruf kapital
  return uid;
}

// Fungsi untuk menghubungkan ke WiFi
void connectToWiFi() {
  WiFi.begin(ssid, password);
  lcd.setCursor(0, 1);
  lcd.print("Menghubungkan...");
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Menghubungkan ke WiFi...");
  }
  Serial.println("Terhubung ke WiFi");
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("WiFi Terhubung");
}

// Fungsi untuk mengirim UID ke server
String sendToServer(String uid) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Kirim UID ke server
    String postData = "api_key=" + String(apiKey) + "&uid=" + uid;
    int httpResponseCode = http.POST(postData);

    String response = "";
    if (httpResponseCode > 0) {
      response = http.getString();
      Serial.println("Respons dari server: " + response);
    } else {
      Serial.println("Error mengirim data: " + String(httpResponseCode));
    }

    http.end();
    return response;
  } else {
    Serial.println("WiFi tidak terhubung");
    return "no-connection";
  }
}
