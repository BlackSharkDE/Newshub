#Erstellen der Datenbank
CREATE SCHEMA `newshub` DEFAULT CHARACTER SET utf8;

#Erstelle Table für Newsquellen
CREATE TABLE `newshub`.`provider` (
    `providerid` INT UNSIGNED NOT NULL AUTO_INCREMENT,         #ID des Providers
    `name` VARCHAR(255) NOT NULL UNIQUE,                       #Name des Providers
    `websiteurl` VARCHAR(512) NOT NULL UNIQUE,                 #Website-URL (normale Website)
    `feedurl` VARCHAR(512) NOT NULL UNIQUE,                    #URL zum News-Feed (Atom / RSS-Link)
    `slogan` VARCHAR(512),                                     #Slogan/Erkennsatz (OPTIONAL)
    `visible` BOOL NOT NULL DEFAULT 1,                         #Ob der Feed sichtbar ist (im Frontend)
    `maintain` BOOL NOT NULL DEFAULT 1,                        #Ob der Feed weiterhin verwaltet werden soll (also geupdated)
    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, #Wann der Provider zuletzt (erfolgreich) geupdated wurde
    `updateinterval` INT UNSIGNED NOT NULL DEFAULT 1440,       #Intervall, in dem der Provider geupdated werden soll in Minuten (Standard = 1440 Minuten = 1 Tag)
    `nextupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, #Wann der Provider das nächste mal (planmäßig) geupdated werden soll
    PRIMARY KEY (`providerId`)
);

#Erstelle Table für News-Items
CREATE TABLE `newshub`.`newsitems` (
    `providerid` INT UNSIGNED NOT NULL,   #ID des Providers, von dem die News ist
    `title` VARCHAR(255) NOT NULL,        #Titel der News
    `link`  VARCHAR(512) NOT NULL UNIQUE, #Link zu dem Artikel
    `description` TEXT NOT NULL,          #Beschreibung des Artikels (gefiltert, sodass nur Text und kein HTML)
    `published` DATETIME NOT NULL,        #Wann der Artikel veröffentlicht wurde
    FOREIGN KEY (`providerid`) REFERENCES `newshub`.`provider`(`providerid`) ON UPDATE CASCADE ON DELETE CASCADE
);