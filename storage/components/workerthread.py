from .newsitem import NewsItem
from .provider import Provider

import threading
import mariadb
import requests
import xml.etree.ElementTree as ET #Für XML-Überprüfung

import re
import emoji

import atomicfeed

####################################################################################################################################################################
# DEBUG #

#Ob die "needsUpdate()"-Prüfung umgangen werden soll
debug_forceProvidersUpdate = False

####################################################################################################################################################################

class WorkerThread(threading.Thread):

    #-- Konstruktor --
    #@param List  Eine Liste mit Provider-Objekten, die abgearbeitet werden sollen
    #@param Tuple Ein Tuple, das die MariaDB-Verbindungparameter beinhaltet (dbUser,dbPass,dbHost,dbPort)
    def __init__(self,providerList,mariadbConnection):
        threading.Thread.__init__(self) #Parent-Konstruktor (Thread)
        
        #Liste zum Abarbeiten
        self.providerList = providerList

        #Eigene Datenbankverbindung für den Thread erstellen (weniger Bottlenecks)
        self.dbConnection = mariadb.connect(
            user     = mariadbConnection[0],
            password = mariadbConnection[1],
            host     = mariadbConnection[2],
            port     = mariadbConnection[3],
            database = "newshub"
        )

        #Cursor für Datenbankaufgaben
        self.dbCursor = self.dbConnection.cursor()

        #Wie viele NewsItems eines Providers in der Datenbank gesichert wurden
        self.savedItemCount = 0 

    ####################################################################################################################################################################

    #Speichert ein NewsItem in die Datenbank (updated, wenn bereits vorhanden). (void - Funktion)
    #@param NewsItem Ein NewsItem-Objekt
    def saveNewNewsItem(self,newsItemObject):
        try:
            statement = """
            INSERT INTO `newshub`.`newsitems` (`providerid`,`title`,`link`,`description`,`published`) VALUES (?,?,?,?,?)
            ON DUPLICATE KEY UPDATE
            `title` = ?,
            `description` = ?,
            `published` = ?
            """
            self.dbCursor.execute(statement,
            (
                newsItemObject.providerid,newsItemObject.title,newsItemObject.link,newsItemObject.description,newsItemObject.published,
                newsItemObject.title,newsItemObject.description,newsItemObject.published
            ))
            self.dbConnection.commit()
            self.savedItemCount += 1
        except mariadb.Error as e:
            print(f"(saveNewNewsItem) DB-error: {e}")
    
    #Updated die Werte "lastupdate" und "nextupdate" eines Providers in der Datenbank. (void - Funktion)
    #@param Int      ID des Providers
    #@param datetime Wann das letzte Update durchgeführt wurde
    #@param datetime Wann das nächste Update geplant ist
    def updateProviderTimestamps(self,providerid,lastUpdateDatetime,nextUpdateDatetime):
        try:
            statement = "UPDATE `newshub`.`provider` SET `lastupdate` = ?, `nextupdate` = ? WHERE `providerid` = ?"
            self.dbCursor.execute(statement,(lastUpdateDatetime,nextUpdateDatetime,providerid))
            self.dbConnection.commit()
        except mariadb.Error as e:
            print(f"(updateProviderTimestamps) DB-error: {e}")

    #Verarbeitet den AtomicFeed und speichert/updated die NewsItems in der Datenbank. (void - Funktion)
    #@param AtomicFeed Ein AtomicFeed-Objekt
    #@param Int        Provider-ID
    #@param String     Name des Providers
    def processFeed(self,atfe,providername,providerid):

        #Gehe die Liste Rückwärts ab (von alt nach neu)
        for i in range(atfe.itemListLen - 1,-1,-1):
            
            #Zwischenspeichern für Bereinigungen
            itemTitle = atfe.getItemTitle(i)
            itemDescr = atfe.getItemDescription(i)

            #Ungewolltes aus Titel und Beschreibung entfernen
            itemTitle = WorkerThread.removeHtmlTags(itemTitle)
            itemTitle = WorkerThread.removeEmojis(itemTitle)
            itemDescr = WorkerThread.removeHtmlTags(itemDescr)
            itemDescr = WorkerThread.removeEmojis(itemDescr)

            #Neues NewsItem erstellen
            ni = NewsItem(providerid,itemTitle,atfe.getItemLink(i),itemDescr,atfe.getItemPublished(i))
            
            #NewsItem speichern/updaten
            self.saveNewNewsItem(ni)

        print(providername + ": Processed (saved/updated) " + str(self.savedItemCount) + " " + atfe.type + " newsitems")
        self.savedItemCount = 0 #Zurücksetzen für nächsten Provider

    ####################################################################################################################################################################

    #Entfernt HTML-Tags aus einem String
    #@param String  Ein String mit HTML-Tags
    #@return String Der String ohne HTML-Tags
    def removeHtmlTags(stringWithHtml):
        return re.sub("<[^>]*>","",stringWithHtml)

    #Entfernt lästige Emoji-Zeichen aus dem String
    #@param String  Ein Strim mit Emoji-Zeichen
    #@return String String ohne Emojis
    def removeEmojis(stringWithEmojis):
        return emoji.replace_emoji(stringWithEmojis, replace="_")

    #Prüft, ob das heruntergeladene XML (Feed-Daten) valides XML sind
    #@param string Der XML-String
    #@return bool  True, wenn ja / False, wenn nein
    def validXML(xmlString):
        try:
            x = ET.fromstring(xmlString)
        except Exception as e: #Alle Exceptions abfangen
            #print(e) #DEBUG
            return False

        return True

    ####################################################################################################################################################################

    #Thread-Methode
    def run(self):

        #Jeden Provider anschauen
        for provider in self.providerList:

            #Wenn der Provider geupdatet werden muss
            if debug_forceProvidersUpdate or provider.needsUpdate():

                #Feed-Download
                print(provider.name + ': Downloading "' + provider.feedurl + '" ...')
                feedContent = None #Initialisieren mit None, falls requests komplett fehlschlägt
                downloadFailedReason = "" #Sollte der Download fehlschlagen, wird hier der Grund gespeichert
                try:
                    feedContent = requests.get(provider.feedurl,timeout=30)
                    feedContent.raise_for_status() #Wenn HTTP-Fehler auftauchen, dafür eine Exception (HTTPError) auslösen
                except requests.exceptions.Timeout:
                    downloadFailedReason = " | Timeout"
                except requests.exceptions.TooManyRedirects:
                    downloadFailedReason = " | Bad URL"
                except requests.exceptions.HTTPError as e:
                    downloadFailedReason = " | HTTP-Error"
                except requests.exceptions.RequestException as e:
                    downloadFailedReason = " | Something unknown went wrong"
                #print(feedContent)

                if feedContent is not None and feedContent.status_code == 200:

                    #Wenn kein Encoding gefunden wurde, kann requests mittels chardet das encoding erraten
                    if feedContent.encoding is None:
                        print("No Encoding found for provider " + provider.name)
                        feedContent.encoding = feedContent.apparent_encoding

                    #Decode - Fehler bei Zeichen werden ignoriert (diese werden also weggelassen)
                    feedContent = str(feedContent.content,encoding=feedContent.encoding,errors="ignore")
                    #print(feedContent) #DEBUG

                    if WorkerThread.validXML(feedContent):
                        #-- Valides XML erkannt --

                        #AtomicFeed erstellen
                        af = atomicfeed.AtomicFeed(feedContent)
                        #print(af) #DEBUG

                        #Verarbeiten des AtomicFeed
                        self.processFeed(af,provider.name,provider.providerid)
                    else:
                        print(provider.name + ': Could not read feed (invalid xml data)')
                else:
                    #Wenn ein HTTP-Status-Code vorhanden ist, diesen anhängen
                    downloadFailedReason += " | HTTP-Status: " + (str(feedContent.status_code) if hasattr(feedContent,"status_code") else "UNKNOWN")

                    print(provider.name + ': Could not download feed "' + provider.feedurl + '"' + downloadFailedReason)
                
                #Den Zeitpunkt des erfolgten Updates im Objekt vermerken
                provider.setLastUpdate()

                #Nächsten Updatezeitpunkt des Providers berechnen
                provider.setNextUpdate()

                #Die Zeitstempel des Providers updaten
                self.updateProviderTimestamps(provider.providerid,provider.lastupdate,provider.nextUpdate)
                
                #print(provider) #DEBUG
        
        #Verbindung zur Datenbank schließen
        self.dbCursor.close()
        self.dbConnection.close()