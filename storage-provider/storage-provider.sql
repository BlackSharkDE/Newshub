INSERT INTO `newshub`.`provider` (`name`,`websiteurl`,`feedurl`,`slogan`,`updateinterval`)
VALUES
    #-- Allgemein --
    ('Spiegel Online','https://www.spiegel.de/','https://www.spiegel.de/schlagzeilen/index.rss','Deutschlands führende Nachrichtenseite. Alles Wichtige aus Politik, Wirtschaft, Sport, Kultur, Wissenschaft, Technik und mehr.',120),
    ('Zeit Online', 'https://www.zeit.de/index','http://newsfeed.zeit.de/all','Nachrichten, Hintergründe und Debatten',120),
    ('NDR (Schleswig Holstein)','https://www.ndr.de/sh','https://www.ndr.de/nachrichten/schleswig-holstein/index-rss.xml','Nachrichten aus Schleswig-Holstein',240),

    #-- Finanzen/Wirtschaft --
    ('Handelsblatt', 'https://www.handelsblatt.com/','https://www.handelsblatt.com/contentexport/feed/schlagzeilen','Das Handelsblatt ist die größte Wirtschafts- und Finanzzeitung in deutscher Sprache.',240),

    #-- Technik --
    ('golem.de', 'https://www.golem.de/', 'https://rss.golem.de/rss.php?feed=RSS2.0','IT-News für Profis',120),
    ('heise online','https://www.heise.de/','https://www.heise.de/rss/heise.rdf','IT-News, Nachrichten und Hintergründe',120),

    #-- Gaming --
    ('GameStar.de', 'https://www.gamestar.de/', 'https://www.gamestar.de/news/rss/news.rss','Das Nr. 1 Magazin für PC-Spieler',120),
    ('Star Wars: Movie Duels','https://www.moddb.com/mods/movie-duels','https://rss.moddb.com/mods/movie-duels/downloads/feed/rss.xml','Star Wars: Jedi Academy mod | Released 2018',43800), #Monatlich
    ('Battlefield 2: Heat of Battle','https://www.moddb.com/mods/heat-of-battle2','https://rss.moddb.com/mods/heat-of-battle2/downloads/feed/rss.xml','Rush-Modus und Grafik-Verbesserungen für BF2',43800),
    ('Arma 3 DEV Hub','https://dev.arma3.com/','http://arma3.com/loadDev?feed=rss','Updates regarding the ongoing development of Arma 3',43800),
    ("SCS Software's blog",'https://blog.scssoft.com/','http://feeds.feedburner.com/ScsSoftwaresBlog?format=xml','SCS Software - developers of vehicle simulation games',43800), #Keine descriptions der News

    #-- Sonstige Feeds --
    ('Der Postillon','https://www.der-postillon.com/','http://feeds.feedburner.com/blogspot/rkEL?format=xml','Ehrliche Nachrichten - unabhängig, schnell, seit 1845',240),
    ('scinexx','https://www.scinexx.de/','http://feeds.feedburner.com/scinexx?format=xml','Wissensmagazin mit News aus Wissenschaft und Forschung',240)
;