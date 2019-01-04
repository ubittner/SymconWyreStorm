## WyreStorm

[![Version](https://img.shields.io/badge/Symcon_Version-5.0>-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
![Version](https://img.shields.io/badge/Modul_Version-1.00-blue.svg)
![Version](https://img.shields.io/badge/Modul_Build-1-blue.svg)
![Version](https://img.shields.io/badge/Code-PHP-blue.svg)
[![License](https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-green.svg)](https://creativecommons.org/licenses/by-nc-sa/4.0/)

Dies ist ein Gemeinschaftsprojekt von Normen Thiel und Ulrich Bittner und integriert [WyreStorm Presentation Switcher](https://wyrestorm.com/SW-0501-HDBT) in [IP-Symcon](https://www.symcon.de).

Es werden WyreStorm Presentation Switcher der Modelle SW-0501-HDBT, NHD-SW-0501, SW-1001-HDBT unterstützt.

Die Konfigurationsoberfläche zur Einstellung von Geräteparametern erreichen Sie über `http://IP-Adresse-des-Gerätes/settings`. Verwenden Sie als Passwort `admin`.

Für dieses Modul besteht kein Anspruch auf Fehlerfreiheit, Weiterentwicklung, sonstige Unterstützung oder Support.

Bevor das Modul installiert wird, sollte unbedingt ein Backup von IP-Symcon durchgeführt werden.

Der Entwickler haftet nicht für eventuell auftretende Datenverluste.

Der Nutzer stimmt den o.a. Bedingungen, sowie den Lizenzbedingungen ausdrücklich zu.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)
8. [GUIDs und Datenaustausch](#8-guids-und-datenaustausch)
9. [Changelog](#9-changelog)
10. [Lizenz](#10-lizenz)
11. [Author](#11-author)


### 1. Funktionsumfang

Es sind noch nicht alle Funktionen (Commands) in diesem Modul integriert. 

Im Moment wird nur eine uni-direktionale Telnet Kommunikation zum Gerät genutzt. Eventuelle Rückmeldungen vom Gerät werden momentan nicht verarbeitet.

Für weitere Funktionen wenden Sie sich bitte an den Entwickler.

#### Funktionen:  

 - Ein- / Ausschalten von Quellen die CEC-kompatibel sind.
 - Umschalten auf die gewünschte Quelle
 - Allgemeine Verringerung oder Erhöhung der Lautstärke über die Instanzkonfiguration
	  
### 2. Voraussetzungen

 - IP-Symcon ab Version 5.0, Web-Console

### 3. Software-Installation

Bei kommerzieller Nutzung (z.B. als Einrichter oder Integrator) wenden Sie sich bitte zunächst an den Entwickler.

Bei privater Nutzung:

Nachfolgend wird die Installation des Moduls anhand der neuen Web-Console ab der Version 5.0 beschrieben. Die Verwendung der (legacy) Verwaltungskonsole wird vom Entwickler nicht mehr berücksichtigt.

Folgende Instanzen stehen dann in IP-Symcon zur Verfügung:

- PresentationSwitcher Device (Geräte Instanz)
- PresentationSwitcher ClientSocket (I/O Instanz)

#### 3a. Modul hinzufügen

Im Objektbaum von IP-Symcon die Kern Instanzen aufrufen. 

Danach die [Modulverwaltung](https://www.symcon.de/service/dokumentation/modulreferenz/module-control/) aufrufen.

Sie sehen nun die bereits installierten Module.

Fügen Sie über das `+` Symbol (unten rechts) ein neues Modul hinzu.

Wählen Sie als URL:

`https://github.com/ubittner/SymconWyreStorm.git`  

Anschließend klicken Sie auf `OK`, um das WyreStorm PresentationSwitcher Modul zu installieren.

#### 3b. Instanz hinzufügen

Klicken Sie in der Objektbaumansicht unten links auf das `+` Symbol. 

Wählen Sie anschließend `Instanz` aus. 

Geben Sie im Schnellfiler das Wort "Presentation" ein oder wählen den Hersteller "WyreStorm" aus. 
Wählen Sie aus der Ihnen angezeigten Liste "PresentationSwitcher" aus und klicken Sie anschließend auf `OK`, um die Instanz zu installieren.

Die Instanz für den Client Socket zur Kommunikation via Telnet mit dem Gerät wird automatisch erstellt.

### 4. Einrichten der Instanzen in IP-Symcon

#### PresentationSwitcher
Vergeben Sie einen eindeutigen Gerätenamen und wählen das vorhandene Modell aus. Unter Quellen können Sie die Quellen angeben, die Sie verwenden möchten.	

#### PresentationSwitcher Client Socket
Geben Sie die IP Adresse des Gerätes und den Port 24 für die Kommunikation per Telnet an.
	
#### Konfiguration:

#### PresentationSwitcher:

| Eigenschaft                       | Typ     | Standardwert          | Funktion                                           |
| :-------------------------------: | :-----: | :-------------------: | :------------------------------------------------: |
| Gerätebezeichnung                 | string  | Presentation Switcher | Eindeutige Bezecihnung des verwendeten Gerätes     |
| Gerätemodell                      | string  | SW-0501-HDBT          | Wählen Sie das verwendete Gerätemodell aus         |
| Protokollierung von Empfangsdaten | boolean | false                 | Empfangsdaten werden im Log (nicht) protokolliert  |
| Power                             | boolean | false                 | De-/Aktiviert den Power Schalter im WebFront       |
| Quellenauswahl                    | boolean | true                  | De-/Aktiviert die Quellenauswahl im WebFront       |
| Quellen                           | string  |                       | Hinzufügen / löschen von Quellen                   |

__Schaltfläche__:

| Bezeichnung                       | Beschreibung
| :-------------------------------: | :-------------------------------------------------------------------: |
| Anleitung                         | Ruft die Dokumentation auf Github auf                                 |
| Manueller Login                   | Manueller Login wenn der Client Socket aktiv ist                      |
| Gerät neu starten                 | Startet das Gerät neu (reboot)                                        |
| Standard Quellen hinzufügen       | Fügt die für das Modell vorhandenen Quellen in die Quellenliste hinzu |
| Lautstärke verringern (-)         | Verringert die allgemeine Gerätelautstärke (inkrementell)             |
| Lautstärke erhöhen (+)            | Herhöht die allgemeine Gerätelautstärke (inkrementell)                |

#### PresentationSwitcher Client Socket:

| Eigenschaft                       | Typ     | Standardwert          | Funktion                                           |
| :-------------------------------: | :-----: | :-------------------: | :------------------------------------------------: |
| Host                              | string  |                       | IP-Adresse des verwendeten  Gerätes                |
| Port                              | string  | 0                     | Port für die Kommunikation, muss den Wert 24 haben |


### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen:

| Name                  | Typ       | Beschreibung                                      |
| :-------------------: | :-------: | :-----------------------------------------------: |
| Power                 | Boolean   | Schaltet die CEC kompatible Quelle ein/aus.       |
| Verfügbare Quellen    | Integer   | Schaltet auf die ausgewählte Quelle um            |

##### Profile:

Nachfolgende Profile werden automatisch angelegt und sofern die Instanz gelöscht wird auch wieder automatisch gelöscht.

| Name                          | Typ       | Beschreibung                              |
| :---------------------------: | :-------: | :---------------------------------------: |
| UBWSPS.InstanceID.Sources'    | Integer   | Enthält die verfügbaren Quellen           |

### 6. WebFront

Über das WebFront kann eine CEC kompatible Quelle ein- und ausgeschaltet werden.
Ebenfalls kann auf die angewählte Quelle umgeschaltet werden.

### 7. PHP-Befehlsreferenz

#### PresentationSwitcher:

`boolean UBWSPS_TogglePower(bool $State);`  
Schaltet die ausgewählte Quelle ein / aus, sofern sie CEC kompatibel ist. 
(true = An, false = Aus).  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_TogglePower(12345, true);`  

`integer UBWSPS_SelectSource(int $Source);`  
Schaltet auf die angegebene Quelle um. ($Source erwartet den Wert des zugewiesenen Profils).  
(1 = Wert 1, 2 = Wert 2 ...)  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_SelectSource(12345, 1);`  

#### Direkte Gerätekommunikation:

`UBWSPS_DeviceLogin();`  
Bei aktivem Client Socket wird ein Login abgesetzt, so dass anschließen eine Kommunikation erfolgen kann.  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_DeviceLogin(12345);`  

`UBWSPS_RebootDevice();`  
Führt einen Neustart (reboot) des Gerätes aus.  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_RebootDevice(12345);`   

`UBWSPS_PowerOffDevice();`  
Schaltet die CEC kompatible Quelle aus.  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_PowerOnDevice(12345);`  

`UBWSPS_PowerOffDevice();`  
Schaltet die CEC kompatible Quelle ein.  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_PowerOnDevice(12345);`  

`UBWSPS_SelectDeviceSource(string $SourceName);`  
Schaltet auf die angegebene Quelle um. Es ist der interne Quellenname des Gerätes anzugeben.  
(HDMI1, HDMI2, HDMI3, HDMI4, HDMI5, HDMI6, DP, VGA1, VGA2, HDBT)  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_SelectDeviceSource(12345, 'HDMI1');`  

`UBWSPS_DecreaseDeviceVolumeIncremental();`  
Verringert die allgemeine Lautstäke inkrementell (-1)  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_DecreaseDeviceVolumeIncremental(12345);`  

`UBWSPS_IncreaseDeviceVolumeIncremental();`  
Erhöht die allgemeine Lautstäke inkrementell (+1)  
Die Funktion liefert keinerlei Rückgabewert.  
`UBWSPS_IncreaseDeviceVolumeIncremental(12345);`  

###  8. GUIDs und Datenaustausch

#### PresentationSwitcher:

GUID: `	{805959F9-1FDA-BF94-30CB-D6044B30771A}` 

Der Datenaustausch erfolgt automatisch über einen Client Socket.

### 9. Changelog

| Version     | Build | Datum      | Beschreibung                   |
|:----------: | :---: | :--------: | :----------------------------: |
| 1.00        | 1     | 03.01.2019 | Version 1.00 für IP-Symcon 5.0 |

### 10. Lizenz

[CC BY-NC-SA 4.0](https://creativecommons.org/licenses/by-nc-sa/4.0/)

### 11. Author

Ulrich Bittner