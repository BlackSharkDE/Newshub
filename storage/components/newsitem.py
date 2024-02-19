from dateutil import parser

class NewsItem:

    #-- Konstruktor --
    #@param Int    ID des Providers, von dem die News stammt
    #@param String Titel des Artikels
    #@param String Link zum Artikel
    #@param String Beschreibung der News
    #@param String Das Datum der News als String
    def __init__(self,providerid,title,link,description,published):
        self.providerid  = providerid
        self.title       = title
        self.link        = link
        self.description = description
        
        #Sollte kein richtiger Datum-String angegeben worden sein
        if published == "":
            self.published = None
        else:
            #Datum generieren/konvertieren => wird zu einheitlichem Datetime-Objekt
            
            #Zusätzliche Zeitzonen-Infos
            additionalTimezoneInfos = {
                "PDT": -7 * 3600
            }

            #Parsen und als String speichern
            self.published = parser.parse(published,tzinfos=additionalTimezoneInfos)
            self.published = self.published.strftime("%Y-%m-%d %H:%M:%S")
    
    #Stringrepräsentation
    def __repr__(self):
        r  = "\n-- NewsItem --\n"
        r += "providerid : " + str(self.providerid) + "\n"
        r += "title      : " + str(self.title) + "\n"
        r += "link       : " + str(self.link) + "\n"
        r += "description: " + str(self.description) + "\n"
        r += "published  : " + str(self.published) + "\n"
        return r
    
    #Definiere Gleichheit
    def __eq__(self,other):
        return self.providerid == other.providerid and \
                self.title == other.title and \
                self.link == other.link and \
                self.description == other.description and \
                self.published == other.published
    
    #-- UNBENUTZT --
    #Unterschied zwischen zwei NewsItem-Objekten (es wird bei Unterschiedenen der Wert vom zweiten Objekt als Unterschied eingetragen)
    #@param NewsItem Das erste Objekt
    #@param NewsItem Das zweite Objekt
    #@return NewsItem (welches nur die Unterschiede enthält), wenn mindestens ein Unterschied / None, wenn beide gleich
    def getDiff(self,other):
        
        #Ob ein Unterschied festgestellt wurde
        areDifferent = False

        #Differenzobjekt
        diffObj = NewsItem(0,"","","","")

        #providerid unterschiedlich
        if self.providerid != other.providerid:
            diffObj.providerid = other.providerid
            areDifferent = True
        
        #title unterschiedlich
        if self.title != other.title:
            diffObj.title = other.title
            areDifferent = True
        
        #link unterschiedlich
        if self.link != other.link:
            diffObj.link = other.link
            areDifferent = True
        
        #description unterschiedlich
        if self.description != other.description:
            diffObj.description = other.description
            areDifferent = True
        
        #published unterschiedlich
        if self.published != other.published:
            diffObj.published = other.published
            areDifferent = True
        
        #Wenn die Objekte unterschiedlich sind
        if areDifferent:
            return diffObj

        #Wenn die Objekte gleich sind
        return None

    #-- UNBENUTZT --
    #Prüft, ob zwei NewsItems "ähnlich" sind
    #@param NewsItem Das erste Objekt
    #@param NewsItem Das zweite Objekt
    #@return Bool True, wenn ja / False, wenn nein
    def similar(self,other):

        #Objekte sind gleich
        if self == other:
            return True

        #Der Link ist die beste, eindeutige Identifizierung, die einen Artikel ausmacht
        #--> Habe noch nie gesehen, dass selbst wenn ein Artikel nachträglich angepasst wurde, sich die URL ändert
        #--> Wenn sich die URL andauernd ändern würde, wäre dies eh schlecht für die News-Seiten, da Suchmaschinen diese auch andauernd updaten müssen (schlechtes SERP-Ranking)
        return self.providerid == other.providerid and self.link == other.link