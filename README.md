mpsit-app
=========

Birthday Thanker Facebook application

=========
Te-am pus ca developer la aplicatie: birthday-thanker
Am creat si 3 useri de test, la Roles poti sa-i vezi si sa te loghezi ca unul din ei.

Nu stiu daca o sa-ti mearga acum, ca FB imi zice ca am depasit nu stiu ce limita de query-uri. Nu puteam sa mai testez cum trebuie, dar am mai refactorizat si codul inainte de a ti-l trimite.
Daca chiar nu merge, poti sa te uiti putin prin el si sa faci ceva UI pe baza codului, sau poti sa incepi mai tarziu pana repar in prima jumate a zilei.

index.php ia urarile de zi de nastere si face un formular pentru multumirea tuturor.
Momentan iau doar ziua de nastere si-l pun pentru anul 2014: poate sa pui un dropdown de selectie a anului.
Se iau postarile din acea zi si se uita doar sa aiba cuvintele "multi" si "sanatate".

thanks.php trimite la toti cei selectati din lista de postari cate un comment (acelasi comment, doar pentru prototipare), si marcheaza si in DB.

TODO-uri de la mine:
	mesaje variate de multumire
	verificare mai buna a posturilor valide
	fix site

localhost.sql are baza de date, unde marchez ce postari au fost multumite.

Ai nevoie de SSL pentru a rula de pe localhost. Enable WAMP SSL guide:
http://forum.wampserver.com/read.php?2,32986
