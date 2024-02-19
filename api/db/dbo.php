<?php

require __DIR__ . '/php_pdointerface/src.php';

//Datenbankverbindungsparameter
$dbHost = "";
$dbUser = "";
$dbPass = "";

//Objekt für Datenbankkommunikation
$dbConnection = new PDOInterface(true,$dbUser,$dbPass);
$dbConnection->setMySQLConnection($dbHost,"newshub");

//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------

/**
 * Fragt die Informationen zu einem Provider ab --> Der Provider muss beim Attribut "visible" = True sein um gefunden zu werden
 * @param \      Ein Attribut, anhand dessen der Provider identifiziert wird --> Möglich nur int ("providerid") oder string ("name")
 * @return array Bei Erfolg ein Array mit 8 Einträgen: "providerid", "name", "websiteurl", "feedurl", "slogan", "lastupdate", "nextupdate", "newscount"
 *               => bei Misslingen ein leeres Array
 */
function getProviderInfo($attr) {
    global $dbConnection; //Datenbankverbindungsobjekt

    //Query-String für die Datenbank
    $queryString = "";

    if(is_string($attr)) {
        //$attr ist ein string
        $queryString = "SELECT `providerid`, `name`,`websiteurl`,`feedurl`,`slogan`,`lastupdate`,`nextupdate` FROM `newshub`.`provider` WHERE `visible` = True AND `name` = ?";
    } else if(is_int($attr)) {
        //$attr ist ein int
        $queryString = "SELECT `providerid`, `name`,`websiteurl`,`feedurl`,`slogan`,`lastupdate`,`nextupdate` FROM `newshub`.`provider` WHERE `visible` = True AND `providerid` = ?";
    }

    //Bei gültigem Query-String
    if(strlen($queryString) > 0) {

        //-- Informationen abfragen --
        $providerInfo = $dbConnection->queryDB($queryString,array($attr));

        //Ergebnis darf nur einen Eintrag enthalten
        if(count($providerInfo) === 1) {
            
            //Ersten und einzigen Eintrag auswählen
            $providerInfo = get_object_vars($providerInfo[0]); //Resultiert in Array

            //-- Herausfinden, wie viele News der Provider besitzt --
            $newscount = $dbConnection->queryDB("SELECT count(`title`) FROM `newshub`.`newsitems` WHERE `providerid` = ?",array($providerInfo["providerid"]));
            
            //Ergebnis darf nur einen Eintrag enthalten
            if(count($newscount) === 1) {
                $newscount = $newscount[0];
                $newscount = intval(array_values(get_object_vars($newscount))[0]);
                //var_dump($newscount);
                $providerInfo["newscount"] = $newscount;
            } else {
                $providerInfo["newscount"] = 0;
            }

            //"providerid"-Eintrag wird durch "get_object_vars" nicht zu einem Integer
            $providerInfo["providerid"] = intval($providerInfo["providerid"]);

            return $providerInfo;
        }
    }

    //Leeres Array zurückgeben
    return array();
}

/**
 * Gibt die Informationen eines Providers anhand seines Namen zurück
 * @param string Name eines Providers
 * @return \     Siehe "getProviderInfo"-Funktion
 */
function getProviderInfoByName(string $nameOfProvider) {
    return getProviderInfo($nameOfProvider);
}

/**
 * Gibt die Informationen eines Providers anhand seiner ID zurück
 * @param int ID des Providers
 * @return \  Siehe "getProviderInfo"-Funktion
 */
function getProviderInfoById(int $idOfProvider) {
    return getProviderInfo($idOfProvider);
}