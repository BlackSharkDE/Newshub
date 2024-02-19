############################################################################################################################################################################
# Unit-Tests für "components/provider.py"
#
# Usage: python -m unittest provider.py
############################################################################################################################################################################

#Python-Testing
import unittest

import jfiles
p = jfiles.getParentFolder(jfiles.getScriptDirectory(__file__))
jfiles.addToSysPath(p)

from components import Provider

import datetime

class TestProvider(unittest.TestCase):

    def test_setNextUpdate(self):
        p1 = Provider(1,"Provider_1","http://www.domain.de/news.xml",1,10)
        self.assertEqual(p1.nextUpdate,datetime.datetime.now() + datetime.timedelta(minutes=10))
    
    def test_getSecondsTillUpdate(self):
        p1 = Provider(1,"Provider_1","http://www.domain.de/news.xml",1,3)
        self.assertEqual(p1.getSecondsTillUpdate(),180)
    
    def test_getFeedtypeAsString(self):

        p1 = Provider(1,"Provider_1","http://www.domain.de/atom_feed.xml",0,42)
        self.assertEqual(p1.getFeedtypeAsString(),"Atom")

        p2 = Provider(2,"Provider_1","http://www.domain.de/rss_feed.xml",1,42)
        self.assertEqual(p2.getFeedtypeAsString(),"RSS")