#define TINY_GSM_MODEM_SIM800

#include <TinyGsmClient.h>
#include <PubSubClient.h>
#include <TinyGPSPlus.h>

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

const char MQTT_BROKER[] = "broker.emqx.io";
const int MQTT_PORT = 1883;

const char MQTT_TOPIC[] = "gps/testing/fakih";

const char DEVICE_ID[] = "GPS-AF-0001";

unsigned long lastPublish = 0;

const unsigned long PUBLISH_INTERVAL = 1000;

unsigned long messageCounter = 0;

const int BATTERY_PERCENTAGE = 85;

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

  Serial.println("Menghubungkan MQTT...");

  if (mqtt.connect(clientId.c_str()))
  {
    Serial.println("MQTT CONNECTED!");
    return true;
  }

  Serial.print("MQTT GAGAL, state = ");
  Serial.println(mqtt.state());

  return false;
}

unsigned long createMessageId()
{
  messageCounter++;

  unsigned long messageId = millis();

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

  double heading = 0.0;

  if (gps.course.isValid())
  {
    heading = gps.course.deg();
  }

  unsigned int satellite = 0;

  if (gps.satellites.isValid())
  {
    satellite = gps.satellites.value();
  }

  unsigned long messageId = createMessageId();

  String payload = "{";

  payload += "\"device_id\":\"";
  payload += DEVICE_ID;
  payload += "\",";

  payload += "\"message_id\":";
  payload += String(messageId);
  payload += ",";

  payload += "\"lat\":";
  payload += String(latitude, 6);
  payload += ",";

  payload += "\"lng\":";
  payload += String(longitude, 6);
  payload += ",";

  payload += "\"speed\":";
  payload += String(speed, 2);
  payload += ",";

  payload += "\"heading\":";
  payload += String(heading, 2);
  payload += ",";

  payload += "\"battery\":";
  payload += String(BATTERY_PERCENTAGE);
  payload += ",";

  payload += "\"satellite\":";
  payload += String(satellite);
  payload += ",";

  payload += "\"received_at\":\"";
  payload += receivedAt;
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
  Serial.println(latitude, 6);

  Serial.print("Longitude  : ");
  Serial.println(longitude, 6);

  Serial.print("Speed      : ");
  Serial.print(speed, 2);
  Serial.println(" km/h");

  Serial.print("Heading    : ");
  Serial.println(heading, 2);

  Serial.print("Battery    : ");
  Serial.print(BATTERY_PERCENTAGE);
  Serial.println("%");

  Serial.print("Satellite  : ");
  Serial.println(satellite);

  Serial.print("Received At: ");
  Serial.println(receivedAt);

  Serial.println("----------------------------------------");

  Serial.println("JSON:");
  Serial.println(payload);

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
  Serial.println(" ESP32 GPS TRACKER");
  Serial.println(" SIM800L + MQTT + GPS");
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
