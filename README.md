# wahlhelfer.app Backend

Dies ist das Backend zu [wahlhelfer.app](https://wahlhelfer.app), das Repo zum Frontend findest du unter [gruessung/wahlhelfer_frontend](https://github.com/gruessung/wahlhelfer_frontend).

# Worum geht es?
wahlhelfer.app ist eine Plattform, um Wahlhelfenden in den Wahllokalen den Abgleich zwischen Wählerverzeichnissen zu erleichtern.
Vorteile:
* kein manueller Abgleich notwendig
* kein manuelles Zählen
* keine doppelte Wahl möglich mit Kopie des Wahlscheines
* Live Ansicht für Wahlleiter

Beim CheckIn (Ausgabe der Wahlunterlagen) im Wahllokal wird die Nummer des Wählenden aus dem Wählerverzeichnis erfasst. Diese erscheint in der Liste "offene Nummern" für den CheckOut (Abgabe der Wahlunterlagen).
Beim CheckOut wird die Nummer ebenfalls geloggt und der Wahlvorgang ist beendet.

Sollte der Wähler mit einer Kopie des Wahlscheines erneut das Wahllokal betreten warnt das System bei erneuter Eingabe der Wahlnummer.

# Datenschutzfreundlich
Die App wahlhelfer.app erfasst keinerlei personenbezogenen Daten. Für die Erstellung einer Wahlsession wird nur ein Passwort (mindestens 10 Stellen) und die Anzahl der Wahlberechtigten gesamt bzw. pro Seite benötigt.

# Self Hosting
Du kannst Frontend und Backend ganz einfach selbst hosten.
Das Backend kannst du ganz einfach auf jeden beliebigen PHP Webhosting betreiben, welches mindestestens PHP >=8.2 unterstützt. 
Passe dir die `.env` File nach deinem Belieben an:
* DATABASE_URL
* APP_ENV

Datenbank anlegen:
```
bin/console doctrine:migrations:migrate
```