#define TINY_GSM_MODEM_SIM800

#include <TinyGsmClient.h>
#include <PubSubClient.h>
#include <TinyGPSPlus.h>
#include "mbedtls/md.h"

// Kredensial perangkat. Tidak ikut ke git - salin secrets.example.h
// menjadi secrets.h lalu isi nilainya.
#include "secrets.h"

#define GPS_RX 16
#define GPS_TX 17

#define SIM800L_RX 25
#define SIM800L_TX 26

HardwareSerial GPS_Serial(1);
HardwareSerial SIM800L_Serial(2);

TinyGPSPlus gps;

TinyGsm modem(SIM800L_Serial);
TinyGsmClient gsmClient(modem);
PubSubClient mqtt(gsmClient);

const char APN[] = "internet";

const char MQTT_BROKER[] = "mqtt-gps.krevostudio.com";
const int MQTT_PORT = 1883;

const char MQTT_USERNAME[] = AFGPS_MQTT_USER;
const char MQTT_PASSWORD[] = AFGPS_MQTT_PASS;

const char MQTT_TOPIC[] = AFGPS_MQTT_TOPIC;

const char DEVICE_ID[] = AFGPS_DEVICE_ID;

const char MQTT_SECRET[] = AFGPS_MQTT_SECRET;

unsigned long lastPublish = 0;

const unsigned long PUBLISH_INTERVAL = 10000;

unsigned long messageCounter = 0;

const int BATTERY_PERCENTAGE = 85;

String hmacSha256Hex(const char *key, const String &message)
{
  byte hash[32];

  mbedtls_md_context_t ctx;

  mbedtls_md_init(&ctx);

  mbedtls_md_setup(
    &ctx,
    mbedtls_md_info_from_type(MBEDTLS_MD_SHA256),
    1
  );

  mbedtls_md_hmac_starts(
    &ctx,
    (const unsigned char *) key,
    strlen(key)
  );

  mbedtls_md_hmac_update(
    &ctx,
    (const unsigned char *) message.c_str(),
    message.length()
  );

  mbedtls_md_hmac_finish(&ctx, hash);

  mbedtls_md_free(&ctx);

  char hex[65];

  for (int i = 0; i < 32; i++)
  {
    sprintf(hex + i * 2, "%02x", hash[i]);
  }

  hex[64] = 0;

  return String(hex);
}

bool connectGPRS()
{
  Serial.println();
  Serial.println("========================================");
  Serial.println(" CONNECTING GPRS");
  Serial.println("========================================");

  Serial.print("Menunggu jaringan... ");

  if (!modem.waitForNetwork(60000L))
  {
    Serial.println("GAGAL");
    return false;
  }

  Serial.println("OK");

  Serial.print("Menghubungkan GPRS dengan APN: ");
  Serial.println(APN);

  if (!modem.gprsConnect(APN, "", ""))
  {
    Serial.println("GPRS GAGAL");
    return false;
  }

  Serial.println("GPRS BERHASIL");

  Serial.print("IP Address: ");
  Serial.println(modem.localIP());

  return true;
}

bool connectMQTT()
{
  Serial.println();
  Serial.println("========================================");
  Serial.println(" CONNECTING MQTT");
  Serial.println("========================================");

  mqtt.setServer(MQTT_BROKER, MQTT_PORT);

  // WAJIB. Payload bertanda tangan sekitar 300 byte, melewati buffer
  // bawaan PubSubClient yang cuma 256. Tanpa ini publish() mengembalikan
  // false tanpa pesan error apa pun.
  mqtt.setBufferSize(512);

  Serial.print("Broker : ");
  Serial.print(MQTT_BROKER);
  Serial.print(":");
  Serial.println(MQTT_PORT);

  String clientId = "ESP32-" +
                    String(DEVICE_ID) +
                    "-" +
                    String(random(0xffff), HEX);

  Serial.print("Client ID : ");
  Serial.println(clientId);

  Serial.print("Username  : ");
  Serial.println(MQTT_USERNAME);

  Serial.println("Menghubungkan MQTT...");

  if (mqtt.connect(clientId.c_str(), MQTT_USERNAME, MQTT_PASSWORD))
  {
    Serial.println("MQTT CONNECTED!");
    return true;
  }

  Serial.print("MQTT GAGAL, state = ");
  Serial.println(mqtt.state());

  return false;
}

// Epoch UTC dari jam GPS.
//
// Menggantikan millis() untuk message_id. millis() kembali ke nol setiap
// alat reboot lalu menabrak message_id yang sudah tersimpan, dan server
// membuang payload itu sebagai duplikat tanpa pesan apa pun.
unsigned long getGPSEpoch()
{
  if (!gps.date.isValid() || !gps.time.isValid())
  {
    return 0;
  }

  if (gps.date.year() < 2024)
  {
    return 0;
  }

  long year = gps.date.year();
  int month = gps.date.month();
  int day = gps.date.day();

  year -= (month <= 2);

  long era = (year >= 0 ? year : year - 399) / 400;

  unsigned yoe = (unsigned) (year - era * 400);

  unsigned doy = (153 * (month + (month > 2 ? -3 : 9)) + 2) / 5 + day - 1;

  unsigned doe = yoe * 365 + yoe / 4 - yoe / 100 + doy;

  long days = era * 146097L + (long) doe - 719468L;

  return (unsigned long) (
    days * 86400L +
    gps.time.hour() * 3600L +
    gps.time.minute() * 60L +
    gps.time.second()
  );
}

unsigned long createMessageId()
{
  messageCounter++;

  unsigned long messageId = getGPSEpoch();

  if (messageId == 0)
  {
    messageId = messageCounter;
  }

  return messageId;
}

String getGPSDateTime()
{
  if (!gps.date.isValid() || !gps.time.isValid())
  {
    return "";
  }

  char datetime[30];

  snprintf(
    datetime,
    sizeof(datetime),
    "%04d-%02d-%02dT%02d:%02d:%02dZ",
    gps.date.year(),
    gps.date.month(),
    gps.date.day(),
    gps.time.hour(),
    gps.time.minute(),
    gps.time.second()
  );

  return String(datetime);
}

void publishGPS()
{
  if (!gps.location.isValid())
  {
    Serial.println();
    Serial.println("========================================");
    Serial.println(" GPS BELUM VALID");
    Serial.println("========================================");

    Serial.println("Menunggu GPS mendapatkan lokasi...");

    return;
  }

  String receivedAt = getGPSDateTime();

  if (receivedAt == "")
  {
    Serial.println();
    Serial.println("========================================");
    Serial.println(" WAKTU GPS BELUM VALID");
    Serial.println("========================================");

    Serial.println("Lokasi GPS sudah valid.");
    Serial.println("Tetapi tanggal/waktu GPS belum tersedia.");
    Serial.println("Menunggu waktu GPS...");

    return;
  }

  double latitude = gps.location.lat();
  double longitude = gps.location.lng();

  double speed = 0.0;

  if (gps.speed.isValid())
  {
    speed = gps.speed.kmph();
  }

  if (speed < 0) speed = 0;
  if (speed > 300) speed = 300;

  double heading = 0.0;

  if (gps.course.isValid())
  {
    heading = gps.course.deg();
  }

  if (heading < 0) heading = 0;
  if (heading > 360) heading = 360;

  unsigned int satellite = 0;

  if (gps.satellites.isValid())
  {
    satellite = gps.satellites.value();
  }

  if (satellite > 50) satellite = 50;

  unsigned long messageId = createMessageId();

  // Tiap nilai diubah jadi String SATU KALI. String yang sama dipakai
  // untuk menghitung tanda tangan DAN untuk menyusun JSON, sehingga
  // keduanya mustahil berbeda.
  String sMessageId  = String(messageId);
  String sLatitude   = String(latitude, 6);
  String sLongitude  = String(longitude, 6);
  String sSpeed      = String(speed, 2);
  String sHeading    = String(heading, 2);
  String sBattery    = String(BATTERY_PERCENTAGE);
  String sSatellite  = String(satellite);

  String canonical = "message_id=" + sMessageId +
                     "&lat=" + sLatitude +
                     "&lng=" + sLongitude +
                     "&speed=" + sSpeed +
                     "&heading=" + sHeading +
                     "&battery=" + sBattery +
                     "&satellite=" + sSatellite +
                     "&received_at=" + receivedAt;

  String signature = hmacSha256Hex(MQTT_SECRET, canonical);

  // Semua angka dikirim DALAM TANDA KUTIP.
  //
  // Server menyusun ulang string kanonik dari nilai hasil json_decode.
  // Kalau dikirim sebagai angka, 0.00 menjadi 0 dan -6.862260 menjadi
  // -6.86226, sehingga tanda tangannya tidak akan pernah cocok.
  String payload = "{";

  payload += "\"device_id\":\"";
  payload += DEVICE_ID;
  payload += "\",";

  payload += "\"message_id\":\"";
  payload += sMessageId;
  payload += "\",";

  payload += "\"lat\":\"";
  payload += sLatitude;
  payload += "\",";

  payload += "\"lng\":\"";
  payload += sLongitude;
  payload += "\",";

  payload += "\"speed\":\"";
  payload += sSpeed;
  payload += "\",";

  payload += "\"heading\":\"";
  payload += sHeading;
  payload += "\",";

  payload += "\"battery\":\"";
  payload += sBattery;
  payload += "\",";

  payload += "\"satellite\":\"";
  payload += sSatellite;
  payload += "\",";

  payload += "\"received_at\":\"";
  payload += receivedAt;
  payload += "\",";

  payload += "\"signature\":\"";
  payload += signature;
  payload += "\"";

  payload += "}";

  Serial.println();
  Serial.println("========================================");
  Serial.println(" MQTT GPS MESSAGE");
  Serial.println("========================================");

  Serial.print("Message ID : ");
  Serial.println(messageId);

  Serial.print("Device ID  : ");
  Serial.println(DEVICE_ID);

  Serial.print("Latitude   : ");
  Serial.println(sLatitude);

  Serial.print("Longitude  : ");
  Serial.println(sLongitude);

  Serial.print("Speed      : ");
  Serial.print(sSpeed);
  Serial.println(" km/h");

  Serial.print("Heading    : ");
  Serial.println(sHeading);

  Serial.print("Battery    : ");
  Serial.print(sBattery);
  Serial.println("%");

  Serial.print("Satellite  : ");
  Serial.println(sSatellite);

  Serial.print("Received At: ");
  Serial.println(receivedAt);

  Serial.println("----------------------------------------");

  Serial.println("CANONICAL:");
  Serial.println(canonical);

  Serial.println("SIGNATURE:");
  Serial.println(signature);

  Serial.println("----------------------------------------");

  Serial.println("JSON:");
  Serial.println(payload);

  Serial.print("Panjang payload: ");
  Serial.print(payload.length());
  Serial.println(" byte");

  Serial.println("----------------------------------------");

  bool success = mqtt.publish(
    MQTT_TOPIC,
    payload.c_str()
  );

  if (success)
  {
    Serial.println("MQTT PUBLISH BERHASIL!");
  }
  else
  {
    Serial.println("MQTT PUBLISH GAGAL!");
  }

  Serial.println("========================================");
}

void setup()
{
  Serial.begin(115200);

  delay(3000);

  Serial.println();
  Serial.println("========================================");
  Serial.println(" ESP32 GPS TRACKER - PRODUKSI");
  Serial.println(" SIM800L + MQTT + GPS + HMAC");
  Serial.println("========================================");

  randomSeed(micros());

  Serial.println();
  Serial.println("Starting GPS...");

  GPS_Serial.begin(
    9600,
    SERIAL_8N1,
    GPS_RX,
    GPS_TX
  );

  Serial.println("GPS Serial OK");

  Serial.println();
  Serial.println("Starting SIM800L...");

  SIM800L_Serial.begin(
    9600,
    SERIAL_8N1,
    SIM800L_RX,
    SIM800L_TX
  );

  delay(3000);

  Serial.println("SIM800L Serial OK");

  Serial.println();
  Serial.println("Testing modem...");

  if (!modem.testAT())
  {
    Serial.println("Modem tidak merespons!");
    Serial.println("Pasang baterai dan nyalakan saklarnya - SIM800L");
    Serial.println("tidak hidup kalau alat hanya diberi daya lewat USB.");
  }
  else
  {
    Serial.println("Modem OK!");
  }

  if (!connectGPRS())
  {
    Serial.println();
    Serial.println("========================================");
    Serial.println(" GPRS GAGAL");
    Serial.println("========================================");

    return;
  }

  if (!connectMQTT())
  {
    Serial.println();
    Serial.println("========================================");
    Serial.println(" MQTT GAGAL");
    Serial.println("========================================");

    return;
  }

  Serial.println();
  Serial.println("========================================");
  Serial.println(" SYSTEM READY");
  Serial.println("========================================");

  Serial.println("GPS       : READY");
  Serial.println("SIM800L   : READY");
  Serial.println("GPRS      : CONNECTED");
  Serial.println("MQTT      : CONNECTED");

  Serial.print("Broker    : ");
  Serial.println(MQTT_BROKER);

  Serial.print("Port      : ");
  Serial.println(MQTT_PORT);

  Serial.print("Topic     : ");
  Serial.println(MQTT_TOPIC);

  Serial.print("Device ID : ");
  Serial.println(DEVICE_ID);

  Serial.println("----------------------------------------");
  Serial.println("Menunggu GPS fix...");
}

void loop()
{
  while (GPS_Serial.available())
  {
    gps.encode(
      GPS_Serial.read()
    );
  }

  if (!mqtt.connected())
  {
    Serial.println();
    Serial.println("========================================");
    Serial.println(" MQTT TERPUTUS");
    Serial.println("========================================");

    if (!modem.isGprsConnected())
    {
      Serial.println("GPRS terputus.");
      Serial.println("Mencoba reconnect GPRS...");

      if (!connectGPRS())
      {
        Serial.println("Reconnect GPRS gagal.");

        delay(5000);

        return;
      }
    }

    if (!connectMQTT())
    {
      Serial.println("Reconnect MQTT gagal.");

      delay(5000);

      return;
    }

    Serial.println("MQTT berhasil reconnect!");
  }

  mqtt.loop();

  if (millis() - lastPublish >= PUBLISH_INTERVAL)
  {
    lastPublish = millis();

    publishGPS();
  }

  delay(10);
}
