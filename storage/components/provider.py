import datetime

class Provider:

    #-- Konstruktor --
    #@param Int      ID des Providers (direkt aus Datenbank)
    #@param String   Name des Feeds (direkt aus Datenbank)
    #@param String   URL des Feeds (direkt aus Datenbank)
    #@param datetime Wann das letzte Update passiert ist
    #@param Int      Updateintervall in Minuten
    #@param datetime Wann das nächste Update passieren soll
    def __init__(self,providerid,name,feedurl,lastupdate,updateinterval,nextUpdate):
        self.providerid     = providerid
        self.name           = name
        self.feedurl        = feedurl
        self.lastupdate     = lastupdate
        self.updateinterval = updateinterval
        self.nextUpdate     = nextUpdate
    
    #Stringrepräsentation
    def __repr__(self):
        r  = "\n-- Provider --\n"
        r += "providerid    : " + str(self.providerid) + "\n"
        r += "name          : " + str(self.name) + "\n"
        r += "feedurl       : " + str(self.feedurl) + "\n"
        r += "lastupdate    : " + str(self.lastupdate) + "\n"
        r += "updateinterval: " + str(self.updateinterval) + "\n"
        r += "nextUpdate    : " + str(self.nextUpdate) + "\n"
        return r
    
    #Aktuelles Datum ohne den Microsekunden-Anteil
    #@retrun datetime
    def getDatetimeWithoutMicroseconds():
        return datetime.datetime.now().replace(microsecond=0)

    #Setzt das Attribut "nextUpdate". (void - Funktion)
    def setNextUpdate(self):
        #Datum letztes Update + updateinterval (minuten) = datetime der nächsten Ausführung
        self.nextUpdate = self.lastupdate + datetime.timedelta(minutes=self.updateinterval)
    
    #Setzt das Attribut "lastupdate" (auf Zeitpunkt des Methodenaufrufs). (void - Funktion)
    def setLastUpdate(self):
        self.lastupdate = Provider.getDatetimeWithoutMicroseconds()

    #Berechnet Zeitunterschied zwischen jetzt und dem nächsten Update in Sekunden
    #@return int Sekunden
    def getSecondsTillNextUpdate(self):
        #Wird durch Subtraktion wird ein "timedelta"-Objekt erstellt, das die Sekunden enthält
        secondsDiff = (self.nextUpdate - Provider.getDatetimeWithoutMicroseconds()).seconds
        
        #Maximale Sekunden sind der "updateInterval" (Minuten) in Sekunden
        maximumDiff = (self.updateinterval * 60)

        #Verhindere zu hohe Differenz bis zum nächstem Update
        if secondsDiff >= maximumDiff:
            secondsDiff = maximumDiff

        return secondsDiff
    
    #Prüft, ob der Provider geupdated werden muss
    #@return Bool True, wenn ja / False, wenn nein
    def needsUpdate(self):
        if datetime.datetime.now() >= self.nextUpdate:
            return True
        return False