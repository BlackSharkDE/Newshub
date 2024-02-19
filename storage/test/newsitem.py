############################################################################################################################################################################
# Unit-Tests für "components/newsitem.py"
#
# Usage: python -m unittest newsitem.py
############################################################################################################################################################################

#Python-Testing
import unittest

import jfiles
p = jfiles.getParentFolder(jfiles.getScriptDirectory(__file__))
jfiles.addToSysPath(p)

from components import NewsItem

class TestNewsItem(unittest.TestCase):

    def test_similar(self):

        #Prüfe gleiche
        ni_1 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein Newsitem","2021-10-12 16:05:05")
        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein Newsitem","2021-10-12 16:05:05")
        self.assertEqual(ni_1.similar(ni_2),True)

        #Prüfe verschiedenste Attribute
        ni_2 = NewsItem(1,"Das hier ist ein NewsItem","http://localhost/test.rss","Das ist ein Newsitem","2021-10-12 16:05:05") #Nur title anders
        self.assertEqual(ni_1.similar(ni_2),True)

        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Dies ist ein Newsitem","2021-10-12 16:05:05") #Nur description anders
        self.assertEqual(ni_1.similar(ni_2),True)

        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein Newsitem","2021-10-12 16:06:00") #Nur published anders
        self.assertEqual(ni_1.similar(ni_2),True)

        ni_2 = NewsItem(1,"Das hier ist ein NewsItem","http://localhost/test.rss","Dies ist ein Newsitem","2021-10-12 16:06:00") #title, description und published anders
        self.assertEqual(ni_1.similar(ni_2),True)

        ni_2 = NewsItem(2,"Ein NewsItem","http://localhost/test.rss","Das ist ein Newsitem","2021-10-12 16:05:05") #providerid anders
        self.assertEqual(ni_1.similar(ni_2),False)

        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test1.rss","Das ist ein Newsitem","2021-10-12 16:05:05") #link anders
        self.assertEqual(ni_1.similar(ni_2),False)

        ni_2 = NewsItem(2,"Ein NewsItem","http://localhost/test1.rss","Das ist ein Newsitem","2021-10-12 16:05:05") #providerid und link anders
        self.assertEqual(ni_1.similar(ni_2),False)
    
    def test_getDiff(self):
        
        ni_1 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein NewsItem","2021-10-12 16:05:05")
        ni_2 = NewsItem(2,"Ein NewsItem","http://localhost/test.rss","Das ist ein NewsItem","2021-10-12 16:05:05") #providerid unterschiedlich
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,ni_2.providerid)
        self.assertEqual(diff.title,"")
        self.assertEqual(diff.link,"")
        self.assertEqual(diff.description,"")
        self.assertEqual(diff.published,None)
        
        ni_2 = NewsItem(1,"Ein ewsItem","http://localhost/test.rss","Das ist ein NewsItem","2021-10-12 16:05:05") #title unterschiedlich
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,ni_2.title)
        self.assertEqual(diff.link,"")
        self.assertEqual(diff.description,"")
        self.assertEqual(diff.published,None)

        ni_2 =  NewsItem(1,"Ein NewsItem","http://localhost/test1.rss","Das ist ein NewsItem","2021-10-12 16:05:05") #link unterschiedlich
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,"")
        self.assertEqual(diff.link,ni_2.link)
        self.assertEqual(diff.description,"")
        self.assertEqual(diff.published,None)

        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein ewsItem","2021-10-12 16:05:05") #description unterschiedlich
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,"")
        self.assertEqual(diff.link,"")
        self.assertEqual(diff.description,ni_2.description)
        self.assertEqual(diff.published,None)

        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein NewsItem","2022-10-12 16:05:05") #published unterschiedlich
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,"")
        self.assertEqual(diff.link,"")
        self.assertEqual(diff.description,"")
        self.assertEqual(diff.published,ni_2.published)

        #title und published unterschiedlich
        ni_2 = NewsItem(1,"Ein ewsItem","http://localhost/test.rss","Das ist ein NewsItem","2022-10-12 16:05:05")
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,ni_2.title)
        self.assertEqual(diff.link,"")
        self.assertEqual(diff.description,"")
        self.assertEqual(diff.published,ni_2.published)

        #title und link und description und published unterschiedlich
        ni_2 = NewsItem(1,"Ein ewsItem","http://localhost/test1.rss","Das ist ein ewsItem","2022-10-12 16:05:05")
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff.providerid,0)
        self.assertEqual(diff.title,ni_2.title)
        self.assertEqual(diff.link,ni_2.link)
        self.assertEqual(diff.description,ni_2.description)
        self.assertEqual(diff.published,ni_2.published)

        #Beide gleich
        ni_2 = NewsItem(1,"Ein NewsItem","http://localhost/test.rss","Das ist ein NewsItem","2021-10-12 16:05:05")
        diff = ni_1.getDiff(ni_2)
        self.assertEqual(diff,None)