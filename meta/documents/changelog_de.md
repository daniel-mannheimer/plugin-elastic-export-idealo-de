# Release Notes für Elastic Export idealo.de

## v1.0.17 (2017-09-18)

### Behoben
- Der UVP wird nun nicht mehr in der Spalte **price** exportiert, wenn dieser niedriger als der Verkaufspreis ist.
- Es wird jetzt das erste Varianten- oder Artikelbild exportiert, wenn die entsprechende Formateinstellung gewählt wurde.

## v1.0.16 (2017-07-18)

  ### Geändert
 - Das Plugin Elastic Export ist nun Voraussetzung zur Nutzung des Plugin-Formats.

## v1.0.15 (2017-07-13)

### Behoben
- Es wurde ein Fehler behoben, bei dem bei der Preisermittlung das Zielland nicht berücksichtigt wurde.

## v1.0.14 (2017-06-14)

### Geändert
- Es wurden kleinere Format-Anpassungen am User Guide vorgenommen.

## v1.0.13 (2017-06-13)

### Geändert
- Die Beschreibung des Plugins wurde erweitert.
- Merkmale vom Typ "Kommazahl" und "ganze Zahl" können jetzt für dieses Format genutzt werden.
- Die Markierung für Idealo Direktkauf in der Spalte "CheckoutApproved" wird nun ebenfalls auf "true" gesetzt, wenn die Variante für die Auftragsherkunft "Idealo Direktkauf" verfügbar geschaltet wurde.

## v1.0.12 (2017-05-12)

### Behoben
- Es wurde ein Fehler behoben, bei dem Varianten nicht in der richtigen Reihenfolge gelisted wurde.
- Es wurde ein Fehler behoben, der dazu geführt hat, dass das Exportformat Texte in der falschen Sprache exportierte.

## v1.0.11 (2017-05-05)

### Behoben
- Es wurde ein Fehler behoben, der dazu geführt hat, dass das Exportformat teilweise nicht geladen werden konnte.

## v1.0.10 (2017-05-02)

### Geändert
- Die Bestandsfilterlogik wurde in das Elastic Export-Plugin ausgelagert.

## v1.0.9 (2017-04-27)

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
