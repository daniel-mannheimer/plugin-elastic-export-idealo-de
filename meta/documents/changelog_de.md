# Release Notes für Elastic Export idealo.de

## v1.0.9 (2017-04-26)

### Behoben
- Der Bestand wird nun korrekt ausgewertet.

## v1.0.8 (2017-04-18)

### Behoben
- Die Logs werden nun korrekt übersetzt.
- Die Array-Definition der Result Fields sind nun für den KeyMutator korrekt angegeben.
- Der Bestand wird nun korrekt berechnet.
- Die Zahlungsarten werden nun korrekt ermittelt.

## v1.0.7 (2017-04-12)

### Behoben
- Die try-catch-Anweisung zum Abfangen von Fehlern funktioniert nun wie vorgesehen.
- Das Formatplugin basiert nun nur noch auf Elastic Search.
- Die Performance wurde verbessert.
- Die Werte für die Spalte fulfillmentType werden jetzt korrekt ausgewertet.

## v1.0.6 (2017-03-30)

### Hinzugefügt
- Es wurde ein neuer Mutator hinzugefügt, welcher verhindern soll das auf nicht existente Arraykeys zugegeriffen werden.

## v1.0.5 (2017-03-28)

### Geändert
- Der Prozess wurde an einigen Stellen angepasst, um die Stabilität zu erhöhen.

## v1.0.4 (2017-03-22)

### Hinzugefügt
- Logs

### Geändert
- Der Prozess wurde an einigen Stellen angepasst, um die Performance zu erhöhen.

## v1.0.3 (2017-03-22)

### Behoben
- Es wird nun ein anderes Feld genutzt für Plugins die elastic search benutzen, um die Bild-URLs auszulesen.

## v1.0.2 (2017-03-13)

### Hinzugefügt
- Marketplace Namen hinzugefügt

### Geändert
- Plugin Icons aktualisiert

## v1.0.1 (2017-03-03)

### Geändert
- Es wird nun für jede übertragene Variante eine SKU generiert.
- Die ResultFields wurden angepasst, sodass der imageMutator bei der Auftragsherkunft "ALLE" nicht mehr greift.

## v1.0.0 (2017-02-20)

### Hinzugefügt
- Initiale Plugin-Dateien hinzugefügt
