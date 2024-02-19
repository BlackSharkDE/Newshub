<?php
/**
 * Live-Ansicht eines Feeds eines Providers (XML). --> Downloadet die Datei hinter der in der Datenbank hinterlegten URL und zeigt diese an.
 * 
 * Erwartet den URL-Parameter (GET) "providername" mit dem Namen (String) eines Providers
 * 
 * Beispiele:
 * - http://localhost/newshub/api/liveview.php?providername=Ein Name eines Providers
 * - http://localhost/newshub/api/liveview.php?providername=Ein%20Name%20eines%20Providers
 */

require 'db/dbo.php';

if(isset($_GET["providername"])) {
    
    //GET-Parameter einlesen
    $providerName = strval($_GET["providername"]);
    
    //Muss ein String sein
    if(is_string($providerName)) {
        
        //Informationen des Providers besorgen
        $pI = getProviderInfoByName($providerName);
        //var_dump($pI); //DEBUG

        //Ergebnis muss 7 Einträge enthalten
        if(count($pI) === 7) {
            //Attribut für die Feed-URL auswählen
            $feedurl = $pI["feedurl"];

            //Ausgabe
            header("Content-type: application/xml");
            echo(file_get_contents($feedurl,"r"));
            
        } else{
            echo('providername "' . $providerName . '" unbekannt');
        }

    } else {
        echo("providername ist kein String");
    }

} else {
    echo("providername Parameter fehlt");
}