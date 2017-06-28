
# User Guide für das Elastic Export idealo.de Plugin

<div class="container-toc"></div>

## 1 Bei idealo.de registrieren

idealo.de ist ein deutsches Preisportal und bietet Preisvergleiche mit Angeboten und Preisen, Testberichten sowie Preisbenachrichtigungen und Produktvergleichen. Weitere Informationen zu idealo.de finden Sie auf der Handbuchseite [idealo Direktkauf einrichten](https://www.plentymarkets.eu/handbuch/multi-channel/idealo/). Um das Plugin für idealo.de einzurichten, registrieren Sie sich zunächst als Händler.

## 2 Das Format IdealoDE-Plugin in plentymarkets einrichten

Um dieses Format nutzen zu können, benötigen Sie das Plugin Elastic Export.

Auf der Handbuchseite [Daten exportieren](https://www.plentymarkets.eu/handbuch/datenaustausch/daten-exportieren/#4) werden allgemein die einzelnen Formateinstellungen beschrieben.

In der folgenden Tabelle finden Sie spezifische Hinweise zu den Einstellungen, Formateinstellungen und empfohlenen Artikelfiltern für das Format **IdealoDE-Plugin**. 

<table>
    <tr>
        <th>
            Einstellung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td class="th" colspan="2">
            Einstellungen
        </td>
    </tr>
    <tr>
        <td>
            Format
        </td>
        <td>
            Das Format <b>IdealoDE-Plugin</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Bereitstellung
        </td>
        <td>
            Die Bereitstellung <b>URL</b> wählen.
        </td>        
    </tr>
    <tr>
        <td>
            Dateiname
        </td>
        <td>
            Der Dateiname muss auf <b>.csv</b> oder <b>.txt</b> enden, damit Idealo die Datei erfolgreich importieren kann.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Artikelfilter
        </td>
    </tr>
    <tr>
        <td>
            Aktiv
        </td>
        <td>
            <b>Aktiv</b> auswählen.
        </td>        
    </tr>
    <tr>
        <td>
            Märkte
        </td>
        <td>
            <b>Idealo</b> auswählen.
        </td>        
    </tr>
    <tr>
        <td class="th" colspan="2">
            Formateinstellungen
        </td>
    </tr>
    <tr>
        <td>
            Auftragsherkunft
        </td>
        <td>
            Die Auftragsherkunft <b>Idealo</b> auswählen.
        </td>        
    </tr>
    <tr>
        <td>
            Angebotspreis
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>
    <tr>
        <td>
            Versandkosten
        </td>
        <td>
            Im Gegensatz zu anderen Formaten spielt hier die Auswahl einer Zahlungsart keine Rolle. Es werden alle Zahlungsarten der gewählten Konfiguration exportiert.<br>
            Wenn keine Konfiguration gewählt wurde, werden alle Konfigurationen und alle Zahlungsarten exportiert.<br>
            Wenn <b>Pauschale Versandkosten übertragen</b> gewählt wurde, wird nur Vorkasse als Zahlungsart übermittelt.
        </td>        
    </tr>
    <tr>
        <td>
            MwSt.-Hinweis
        </td>
        <td>
            Diese Option ist für dieses Format nicht relevant.
        </td>        
    </tr>

</table>


## 3 Übersicht der verfügbaren Spalten

<table>
    <tr>
        <th>
            Spaltenbezeichnung
        </th>
        <th>
            Erläuterung
        </th>
    </tr>
    <tr>
        <td>
            article_id
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Die <b>SKU</b> des Artikels. Bei Artikeln, die für Idealo Direktkauf freigegeben wurden, wird hier eine SKU für Idealo Direktkauf ausgegeben.
        </td>        
    </tr>
    <tr>
        <td>
            deeplink
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der <b>URL-Pfad</b> des Artikels abhängig vom gewählten <b>Mandanten</b> in den Formateinstellungen.
        </td>        
    </tr>
    <tr>
        <td>
            name
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Artikelname</b>.
        </td>        
    </tr>
    <tr>
        <td>
            short_description
        </td>
        <td>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Vorschautext</b>.
        </td>        
    </tr>
    <tr>
        <td>
            description
        </td>
        <td>
            <b>Beschränkung:</b> max. 1000 Zeichen<br>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Beschreibung</b>.
        </td>        
    </tr>
    <tr>
        <td>
            article_no
        </td>
        <td>
            <b>Inhalt:</b> Die <b>Variantennr.</b> unter <b>Artikel » Artikel bearbeiten » Artikel öffnen » Variante öffnen » Einstellungen » Grundeinstellungen</b>.
        </td>        
    </tr>
    <tr>
        <td>
            producer
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Name des Herstellers</b> des Artikels. Der <b>Externe Name</b> unter <b>Einstellungen » Artikel » Hersteller</b> wird bevorzugt, wenn vorhanden.
        </td>        
    </tr>
    <tr>
        <td>
            model
        </td>
        <td>
            <b>Inhalt:</b> Das <b>Modell</b> unter <b>Artikel » Artikel bearbeiten » Artikel öffnen » Variante öffnen » Einstellungen » Grundeinstellungen</b>.
        </td>        
    </tr>
    <tr>
        <td>
            availability
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der <b>Name der Artikelverfügbarkeit</b> unter <b>Einstellungen » Artikel » Artikelverfügbarkeit</b> oder die Übersetzung gemäß der Formateinstellung <b>Artikelverfügbarkeit überschreiben</b>.
        </td>        
    </tr>
    <tr>
        <td>
            ean
        </td>
        <td>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Barcode</b>.
        </td>        
    </tr>
    <tr>
        <td>
            isbn
        </td>
        <td>
            <b>Inhalt:</b> Entsprechend der Formateinstellung <b>Barcode</b>.
        </td>        
    </tr>
    <tr>
        <td>
            fedas
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Amazon-Produkttyp</b> unter <b>Artikel » Artikel bearbeiten » Artikel öffnen » Multi-Channel » Amazon</b>.
        </td>        
    </tr>
    <tr>
        <td>
            warranty
        </td>
        <td>
            <b>Inhalt:</b> Kann aktuell nicht gepflegt werden.
        </td>        
    </tr>
    <tr>
        <td>
            price
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> Der <b>Verkaufspreis</b> der Variante.
        </td>        
    </tr>
    <tr>
        <td>
            price_old
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Verkaufspreis</b> vom Typ <b>UVP</b> der Variante, wenn in den Formateinstellungen aktiviert.
        </td>        
    </tr>
    <tr>
        <td>
            weight
        </td>
        <td>
            <b>Inhalt:</b> <b>Gewicht brutto</b> in Gramm unter <b>Artikel » Artikel bearbeiten » Artikel öffnen » Variante öffnen » Einstellungen » Maße</b>.
        </td>        
    </tr>
    <tr>
        <td>
            category1-6
        </td>
        <td>
            <b>Inhalt:</b> Der <b>Name der Kategorieebene</b> des <b>Kategoriepfads der Standard-Kategorie</b> für den, in den Formateinstellungen definierten <b>Mandanten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            category_concat
        </td>
        <td>
            <b>Inhalt:</b> <b>Kategoriepfads der Standard-Kategorie</b> für den, in den Formateinstellungen definierten <b>Mandanten</b>.
        </td>        
    </tr>
    <tr>
        <td>
            image_url_preview
        </td>
        <td>
            <b>Inhalt:</b> URL zu einer, für die Vorschau skalierten Version des Bildes gemäß der Formateinstellungen <b>Bild</b>.
        </td>        
    </tr>
    <tr>
        <td>
            image_url
        </td>
        <td>
            <b>Pflichtfeld</b><br>
            <b>Inhalt:</b> URL zu dem Bild gemäß der Formateinstellungen <b>Bild</b>.
        </td>        
    </tr>
    <tr>
        <td>
            base_price
        </td>
        <td>
            <b>Inhalt:</b> Die <b>Grundpreisinformation</b> im Format "Preis / Einheit". (Beispiel: 10,00 EUR / Kilogramm)
        </td>        
    </tr>
    <tr>
        <td>
            free_text_field
        </td>
        <td>
            <b>Inhalt:</b> Der Wert genau eines <b>Merkmals</b> vom Typ <b>Text</b> oder <b>Auswahl</b> mit der Verknüpfung zu <b>Idealo</b>.
        </td>        
    </tr>
    <tr>
        <td>
            checkoutApproved
        </td>
        <td>
            <b>Inhalt:</b> Wird true gesetzt, wenn entweder die Auftragsherkunft <b>Idealo Direktkauf</b> unter <b>Artikel » Artikel bearbeiten » Artikel öffnen » Variante öffnen » Verfügbarkeit » Märkte</b> aktiviert wurde oder ein Merkmal vom Typ <b>Kein</b> und der Verknüpfung zum IdealoDK-Merkmal CheckoutApproved gesetzt wurde.
        </td>        
    </tr>
    <tr>
        <td>
            itemsInStock
        </td>
        <td>
            <b>Vorraussetzung:</b> checkoutApproved ist true.<br>
            <b>Inhalt:</b> Der <b>Nettowarenbestand der Variante</b>. Bei Artikeln, die nicht auf den Nettowarenbestand beschränkt sind, wird <b>999</b> übertragen.
        </td>        
    </tr>
    <tr>
        <td>
            fulfillmentType
        </td>
        <td>
            <b>Vorraussetzung:</b> checkoutApproved ist true.<br>
            <b>Inhalt:</b> Merkmal vom Typ <b>Kein</b> und der Verknüpfung zum IdealoDK-Merkmal <b>FulfillmentType » Spedition</b> oder <b>FulfillmentType » Paketdienst</b>.
        </td>        
    </tr>
    <tr>
        <td>
            twoManHandlingPrice
        </td>
        <td>
            <b>Vorraussetzung:</b> checkoutApproved ist true und fulfillmentType ist Spedition.<br>
            <b>Inhalt:</b> Merkmal vom Typ <b>Text</b>, <b>Auswahl</b> oder <b>Kommazahl</b> und der Verknüpfung zum IdealoDK-Merkmal <b>twoManHandlingPrice</b>.
        </td>        
    </tr>
    <tr>
        <td>
            disposalPrice
        </td>
        <td>
            <b>Vorraussetzung:</b> checkoutApproved ist true und fulfillmentType ist Spedition.<br>
            <b>Inhalt:</b> Merkmal vom Typ <b>Text</b>, <b>Auswahl</b> oder <b>Kommazahl</b> und der Verknüpfung zum IdealoDK-Merkmal <b>disposalPrice</b>.
        </td>        
    </tr>
    <tr>
        <td>
            Zahlungsarten
        </td>
        <td>
            <b>Pflichtfeld</b> (Mindestens eine Zahlungsart)<br>
            <b>Inhalt:</b> Es werden die Zahlungsarten gemäß der Formateinstellung <b>Versandkosten</b> in je einer eigenen Spalte übermittelt.
        </td>        
    </tr>
</table>

## Lizenz

Das gesamte Projekt unterliegt der GNU AFFERO GENERAL PUBLIC LICENSE – weitere Informationen finden Sie in der [LICENSE.md](https://github.com/plentymarkets/plugin-elastic-export-idealo-de/blob/master/LICENSE.md).
