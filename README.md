# Allgemeines
Erweiterungen für den Synology Chat durch Slash Befehle z.B. durch ÖPNV Auskunft.

Der PHP Code ist noch nicht optimal da ich kein Programmierer bin.

Auf der DS im Web Ordner z.B. einen Unterordner erstellen wie "syno-chat" und dort die Erweiterungen speichern.

## HVV Auskunft
Für HVV Auskunft wäre als Beispiel ganze so:
![grafik](https://github.com/Danielbaerchen/Synology-Chat-Erweiterungen/assets/153910357/7e929970-961e-46f8-92ee-2ba0f919d3f2)

Name: HVV Fahrverbindung

Befehl: hvv

Anfrage-URL: http://127.0.0.1/syno-chat/HVV.php

Befehlsanweisung: Start ; Ziel ; Uhrzeit ; Datum

Beschreibung: Uhrzeit ist optional ab wann, sonst gilt jetzt, und Datum ist wenn leer = heute

Token ist nicht relevant

##VRR Auskunft
Für HVV Auskunft wäre als Beispiel ganze so:
![grafik](https://github.com/Danielbaerchen/Synology-Chat-Erweiterungen/assets/153910357/d1979204-225e-4c79-a86d-6f3fb0743f72)

Name: VRR Fahrverbindung

Befehl: vrr

Anfrage-URL: http://127.0.0.1/syno-chat/VRR.php

Befehlsanweisung: Start ; Ziel ; Uhrzeit

Beschreibung: Uhrzeit ist optional ab wann, sonst gilt jetzt, und Datum ist wenn leer = heute

Token ist nicht relevant
