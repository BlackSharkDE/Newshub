####################################################################################################################################################################
# feedget.py
#
# Python-Version: 3.8.3
####################################################################################################################################################################
# -- Konfiguration --

#Datenbankverbindung
dbUser = ""   #User (String)
dbPass = ""   #Passwort (String)
dbHost = ""   #Host (String)
dbPort = 3306 #Port (Integer)

####################################################################################################################################################################
# -- Sonstige Funktionen --

#Fragt alle Provider aus der Datenbank ab
#@param Tuple Ein Tuple, das die MariaDB-Verbindungparameter beinhaltet (dbUser,dbPass,dbHost,dbPort)
#@return List Eine Liste mit Provider-Objekten
def getAllProvider(mariadbConnection):

    #Liste mit Provider-Objekten
    providerList = list()

    #Verbindung zur Datenbank erstellen
    conn = mariadb.connect(
        user     = mariadbConnection[0],
        password = mariadbConnection[1],
        host     = mariadbConnection[2],
        port     = mariadbConnection[3],
        database = "newshub"
    )

    #Cursor für Datenbankaufgaben
    cur = conn.cursor()

    #Alle Provider aus Datenbank abfragen
    try:
        statement = "SELECT `providerid`,`name`,`feedurl`,`lastupdate`,`updateinterval`,`nextupdate` FROM `newshub`.`provider` WHERE `newshub`.`provider`.`maintain` = True"
        cur.execute(statement,)

        for x in cur:
            #print(x) #DEBUG
            providerList.append(Provider(x[0],x[1],x[2],x[3],x[4],x[5]))

    except mariadb.Error as e:
        print(f"(main) DB-error: {e}")
    
    #Verbindung zur Datenbank schließen
    cur.close()
    conn.close()

    return providerList

#Findet den Provider, der am zeitnahesten geupdated werden muss
#@param List      Liste mit Provider-Objekten
#@return Provider Ein Provider-Objekt
def findTimeliestUpdate(providerList):
    nearestUpdateProvider = providerList[0]

    for provider in providerList:
        if provider.nextUpdate < nearestUpdateProvider.nextUpdate:
            nearestUpdateProvider = provider

    return nearestUpdateProvider

####################################################################################################################################################################
# -- Hauptroutine --

from components import WorkerThread, Provider

import datetime
import time
import mariadb

logo = '''
                           .__         ___.    
  ____   ______  _  _______|  |__  __ _\_ |__  
 /    \_/ __ \ \/ \/ /  ___/  |  \|  |  \ __ \ 
|   |  \  ___/\     /\___ \|   Y  \  |  / \_\ \\
|___|  /\___  >\/\_//____  >___|  /____/|___  /
     \/     \/           \/     \/          \/ 

               -- feedget.py --
'''
print(logo)

#Verbindung-Tuple für die Datenbankverbindungen
connectionTuple = (dbUser,dbPass,dbHost,dbPort)

#-- Endlosschleife --
while True:

    #Alle Provider aus der Datenbank laden
    allProvider = getAllProvider(connectionTuple)
    #print(str(allProvider)) #DEBUG

    if len(allProvider) > 0:
        #-- Threads erstellen und starten --

        #Berechne Mittelwert der Provider (zur Aufteilung für die 2 Threads)
        m = int(len(allProvider) / 2)

        t1 = WorkerThread(allProvider[0:m],connectionTuple)
        t1.start()

        t2 = WorkerThread(allProvider[m:],connectionTuple)
        t2.start()

        #Warte auf Beendigung der WorkerThreads
        t1.join()
        t2.join()

    #Finde den Provider, der am ehesten als nächstes geupdated muss (maximale Pause bis nächsten Durchlauf)
    timelyProviderToUpdate = findTimeliestUpdate(allProvider)
    
    #Wie lange das Skript maximal pausieren kann
    scriptPauseInSeconds = timelyProviderToUpdate.getSecondsTillNextUpdate()

    #Pausiere das Skript
    print("\n********************************************************************************")
    print("Script pause (seconds): " + str(scriptPauseInSeconds))
    print("\nProvider with timely fetch: \n" + str(timelyProviderToUpdate))
    print("********************************************************************************\n")
    time.sleep(scriptPauseInSeconds)