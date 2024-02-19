<?php
/**
 * Die API, die Informationen über die Provider und NewsItems im JSON-Format ausgibt.
 * 
 * Erwartet URL-Parameter (GET) --> Siehe Kommentare zu den Hauptparametern.
 */

require 'db/dbo.php';

/**
 * Gruppiert die NewsItems (stdClass) nach Tagesdatum
 * @param array  Ein Array mit "stdClass"-Objekten (direkt aus der Datenbank)
 * @return array Ein Array mit "stdClass"-Objekten, gruppiert nach Tagen
 */
function groupNewsItemsByDayDate(array $newsItems) {
    
    /**
     * Erwartet Datum-String mit "YYYY-MM-DD HH:MM:SS" Format und gibt den "YYYY-MM-DD"-Teil zurück
     * @param string  Voller Datum-String
     * @return string Teil des Datum-String
     */
    function getDayDate(string $publishedString) {
        return substr($publishedString,0,10);
    }

    //Array mit NewsItems, gruppiert nach Tagen
    $dayGroups = array();

    //Nach Tagesdatum Gruppieren
    foreach($newsItems as $newsItem) {

        //Tages-Datum des aktuellen NewsItem
        $dayDate = getDayDate($newsItem->published);

        //Sollte der Datum-Array-Key nicht existieren -> Neues Array hinzufügen
        if(!isset($dayGroups[$dayDate])) {
            $dayGroups[$dayDate] = array();
        }

        //Das NewsItem dem Datum-Array-Key hinzufügen
        array_push($dayGroups[$dayDate],$newsItem);
    }

    return $dayGroups;
}

$apiResult = array();

if(isset($_GET["providernews"]) && !isset($_GET["providerinfo"]) && !isset($_GET["search"]) && !isset($_GET["frontend"])) {
    /**
     * -- Ausgabe von NewsItems eines Providers ("providernews") --
     * 
     * Die GET-Parameter "itemcount", "group" und "page" sind optional.
     * 
     * Die Parameter "itemcount" und "group" können beliebig kombiniert werden. Einzig "page" und "itemcount" können nicht kombiniert werden.
     * 
     * Beispiele:
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers
     * - http://localhost/newshub/api/rest.php?providernews=Ein%20Name%20eines%20Providers
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers&itemcount=42
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers&group
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers&itemcount=42&group
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers&page=2
     * - http://localhost/newshub/api/rest.php?providernews=Ein Name eines Providers&page=2&group
     */

    //-- GET-Parameter speichern --
    
    //Name des Provider
    $providerName = strval($_GET["providernews"]);
    
    //Optionaler Parameter "itemcount"
    //--> Wie viele Items maximal ausgegeben werden sollen (von neuestem Item ausgehend)
    $itemcount = 0; //Standardmäßig kein Limit
    if(isset($_GET["itemcount"])) {
        $itemcount = intval($_GET["itemcount"]);

        //Ungültige Werte abfangen --> Bei ungültigem Wert 30 Items (aufgrund des Widget)
        if($itemcount < 1) {
            $itemcount = 30;
        }
    }

    //Optionaler Parameter "group"
    //--> Ob die NewsItems nach dem Tages-Datum gruppiert werden sollen
    $groupByDate = False; //Standardmäßig nein
    if(isset($_GET["group"])) {
        $groupByDate = True;
    }

    //Optionaler Parameter "page"
    $page = 0; //Standardmäßig keine Seite
    if(isset($_GET["page"])) {
        $page = intval($_GET["page"]);

        //Ungültige Werte abfangen --> Bei ungültigem Wert auf 1 setzen
        if($page < 1) {
            $page = 1;
        }

        //Maximale Anzahl an Items pro Seite (fest und muss gleich mit Frontend sein)
        $pageItemsPerPage = 100;

        //Berechne, bei welchem Item man sein müsste
        $pageItemOffset = ($page * $pageItemsPerPage) - $pageItemsPerPage;
    }

    //-- NewsItems aus der Datenbank besorgen --

    //Provider-ID herausfinden
    $providerId = $dbConnection->queryDB("SELECT `providerid` FROM `newshub`.`provider` WHERE `visible` = True AND `name` = ?",array($providerName));
    if(count($providerId) > 0) {
        $providerId = $providerId[0]->providerid;
        
        $sqlString = "SELECT `title`,`link`,`description`,`published` FROM `newshub`.`newsitems` WHERE `providerid` = ? ORDER BY `published` DESC";

        //"LIMIT" mit Prepared Statements funktioniert in PDO nicht... daher das Limit (kann / darf eh nur ein Integer sein) mit String-Concatenation
        if($itemcount > 0 && $page == 0) {
            //-- itemcount anwenden --
            $sqlString = $sqlString . " LIMIT " . $itemcount;
        } else if($itemcount == 0 && $page > 0) {
            //-- page anwenden --
            $sqlString = $sqlString . " LIMIT " . $pageItemsPerPage . " OFFSET " . $pageItemOffset;
        }

        //NewsItems abfragen
        $newsItems = $dbConnection->queryDB($sqlString,array($providerId));
        
        //Gegebenenfalls die NewsItems gruppieren
        if($groupByDate && count($newsItems) > 0) {
            $newsItems = groupNewsItemsByDayDate($newsItems);
        }

        $apiResult = $newsItems;
    }

} else if(!isset($_GET["providernews"]) && isset($_GET["providerinfo"]) && !isset($_GET["search"]) && !isset($_GET["frontend"])) {
    /**
     * -- Ausgabe von Providerinformationen ("providerinfo") --
     * 
     * Beispiele:
     * - http://localhost/newshub/api/rest.php?providerinfo=Ein Name eines Providers
     * - http://localhost/newshub/api/rest.php?providerinfo=Ein%20Name%20eines%20Providers
     */

    //GET-Parameter speichern
    $providerName = strval($_GET["providerinfo"]);

    //Informationen des Providers besorgen
    $pI = getProviderInfoByName($providerName);
    
    //Ergebnis muss 8 Einträge enthalten
    if(count($pI) === 8) {
        $apiResult = $pI;
    }

} else if (!isset($_GET["providernews"]) && !isset($_GET["providerinfo"]) && isset($_GET["search"]) && !isset($_GET["frontend"])) {
    /**
     * -- Suchfunktion ("search") --
     * 
     * Es kann nur entweder der GET-Parameter "news" oder "provider" aktiv sein (beide zusammen führen zu keinem Ergebnis).
     * 
     * Beispiele:
     * - http://localhost/newshub/api/rest.php?search=Suchstring          => NewsItems und Provider
     * - http://localhost/newshub/api/rest.php?search=Suchstring&news     => Nur NewsItems
     * - http://localhost/newshub/api/rest.php?search=Suchstring&provider => Nur Provider
     */

    //GET-Parameter speichern
    $searchTerm = strval($_GET["search"]);
    $searchTerm = trim($searchTerm); //Leerzeichen am Anfang und Ende entfernen

    //Bei Such-String bei den Leerzeichen splitten
    $searchTerms = explode(" ",$searchTerm);

    //Terme bauen ("%" + term + "%")
    $searchTerms = array_map(fn($x) => '%' . $x . '%', $searchTerms);
    #var_dump($searchTerms);

    /**
     * Reiht für den Where-Abschnitt der Queries den Spaltennamen so oft wie Suchterme vorhanden sind aneinander
     * @param string  Name der Spalte in der Datenbank
     * @return string Aneinander gereihter Spaltenname
     */
    function getLikeForColumn($columnName) {
        global $searchTerms;

        $likeExpressionsForColumn = "(";

        for($i = 0; $i < count($searchTerms); $i++) {
            if($i == 0) {
                //Beim erstem Item entfällt das "AND"
                $likeExpressionsForColumn .= "`" . $columnName . "` LIKE ?";
            } else {
                //Weitere Items mit "AND" anknüpfen
                $likeExpressionsForColumn .= " AND `" . $columnName . "` LIKE ?";
            }
        }

        $likeExpressionsForColumn .= ")";

        #var_dump($likeExpressionsForColumn);
        return $likeExpressionsForColumn;
    }

    //Suchstring für "LIKE"-Operation vorbereiten
    $searchTerm = '%' . $searchTerm . '%';

    $apiResult["newsitems"] = array();
    if(!isset($_GET["provider"])) { //Wenn der GET-Parameter "provider" nicht gesetzt ist
        //Suche nach NewsItems mit dem Suchstring in "title" oder "description" oder "published"
        $newsItems = $dbConnection->queryDB(
            "SELECT `title`,`link`,`description`,`published`
            FROM `newshub`.`newsitems`
            INNER JOIN `newshub`.`provider` ON `newshub`.`provider`.`providerid` = `newshub`.`newsitems`.`providerid`
            WHERE `newshub`.`provider`.`visible` = True
            AND (" . getLikeForColumn("title") . " OR " . getLikeForColumn("description") . " OR " . getLikeForColumn("published") . ")
            ORDER BY `published` DESC",
            array_merge($searchTerms,$searchTerms,$searchTerms)
        );
        $apiResult["newsitems"] = $newsItems;
    }

    $apiResult["provider"] = array();
    if(!isset($_GET["news"])) { //Wenn der GET-Parameter "news" nicht gesetzt ist
        //Suche nach Provider mit dem Suchstring in "name" oder "slogan"
        $provider = $dbConnection->queryDB(
            "SELECT `providerid`,`name`,`websiteurl`,`feedurl`,`slogan` FROM `newshub`.`provider` WHERE `visible` = True
            AND (". getLikeForColumn("name") . " OR " . getLikeForColumn("slogan") . ")",
            array_merge($searchTerms,$searchTerms)
        );
        $apiResult["provider"] = $provider;
    }

} else if(!isset($_GET["providernews"]) && !isset($_GET["providerinfo"]) && !isset($_GET["search"]) && isset($_GET["frontend"])) {
    /**
     * -- Funktionen/Daten für das Frontend ("frontend") --
     * 
     * Die GET-Parameter "newscount", "newestnews", "providercount", "mostnews" und "randomprovider" sind optional und können beliebig kombiniert werden.
     * Um jedoch eine nützliche Antwort zu erhalten, muss mindestens ein Parameter aktiv sein.
     * 
     * Beispiele:
     * - http://localhost/newshub/api/rest.php?frontend&newscount
     * - http://localhost/newshub/api/rest.php?frontend&newscount&mostnews
     * - http://localhost/newshub/api/rest.php?frontend&providercount&newestnews
     */

    //Wie viele NewsItems (insgesamt, auch die von ausgeblendeten Providern) in der Datenbank vorhanden sind
    if(isset($_GET["newscount"])) {
        $nc = $dbConnection->queryDB("SELECT count(`title`) FROM `newshub`.`newsitems`");
        if(count($nc) === 1) {
            $nc = $nc[0];
            $apiResult["newscount"] = intval(array_values(get_object_vars($nc))[0]);
        }
    }

    //Neueste NewsItems (maximal 4)
    if(isset($_GET["newestnews"])) {
        $nn = $dbConnection->queryDB("SELECT `title`,`link`,`description`,`published` FROM `newshub`.`newsitems` ORDER BY `published` DESC LIMIT 4");
        if(count($nn) > 0) {
            $apiResult["newestnews"] = $nn;
        }
    }

    //Wie viele Provider
    if(isset($_GET["providercount"])) {
        $pc = $dbConnection->queryDB("SELECT count(`name`) FROM `newshub`.`provider`");
        if(count($pc) === 1) {
            $pc = $pc[0];
            $apiResult["providercount"] = intval(array_values(get_object_vars($pc))[0]);
        }
    }

    //Welcher Provider die meisten NewsItems besitzt
    if(isset($_GET["mostnews"])) {
        $mn = $dbConnection->queryDB("SELECT `providerid`, count(`title`) AS `newscount` FROM `newshub`.`newsitems` GROUP BY `providerid` ORDER BY `newscount` DESC");
        if(count($mn) > 0) {
            $mn = $mn[0];
            $apiResult["mostnews"] = getProviderInfoById($mn->providerid);
        }
    }

    //Ein zufälliger Provider
    if(isset($_GET["randomprovider"])) {
        $rp = $dbConnection->queryDB("SELECT `providerid` FROM `newshub`.`provider` WHERE `visible` = True ORDER BY `providerid`");
        $rpc = count($rp);
        if($rpc > 0) {
            $randomIndex = random_int(0,$rpc - 1);
            $randomProviderId = intval(array_values(get_object_vars($rp[$randomIndex]))[0]);
            $apiResult["randomprovider"] = getProviderInfoById($randomProviderId);
        }
    }
    
    //Alle Provider
    if(isset($_GET["allprovider"])) {
        $ap = $dbConnection->queryDB("SELECT `providerid` FROM `newshub`.`provider` WHERE `visible` = True ORDER BY `providerid`");
        if(count($ap) > 0) {
            $apiResult["allprovider"] = array();
            foreach($ap as $p) {
                array_push($apiResult["allprovider"],getProviderInfoById($p->providerid));
            }
        }
    }
}

//Es wird immer JSON ausgegeben
header("Content-type: application/json");
echo(json_encode($apiResult));