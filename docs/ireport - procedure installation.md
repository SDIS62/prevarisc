# iReport - Proc�dure d'installation #

## T�l�chargement de ireport 5.6 et java 7 ##
Le t�l�chargement de ireport 5.6 se fait � partir de la page suivante : http://sourceforge.net/projects/ireport/files/iReport/iReport-5.6.0/
De plus, il faut t�l�charger le JDK java 7 � partir de la page suivante : http://www.oracle.com/technetwork/java/javase/downloads/jdk7-downloads-1880260.html

## Configuration avant le premier lancement de iReport ##
Apr�s avoir install� ireport 5.6 et le JDK Java 7, il faut modifier le fichier � ireport.conf � dans le r�pertoire � C:\Program Files (x86)\Jaspersoft\iReport-5.6.0\etc\ � en mettant l�emplacement du jdk java :
Exemple : jdkhome = � C:\Program Files\Java\jdk1.7.0_79 �
Note : ne pas oublier d�enlever le # devant la ligne
Puis lancer "iReport-5.6.0" sans probl�me (sans ce param�tre, ireport ne se lance pas mais il n'y a pas de message d'erreur)

## Cr�ation de la connexion � la base de donn�es ##
Lors du premier lancement, vous arriver sur cette page :
![](https://github.com/SDIS62/prevarisc/tree/2.x/docs/images/page_accueil.jpg)

Cliquer sur l�ic�ne pour cr�er une connexion � une base de donn�es : ![](https://github.com/SDIS62/prevarisc/tree/2.x/docs/images/icone_creation_connexion.jpg)

Choisir � Database JDBC Connection � :
![](https://github.com/SDIS62/prevarisc/tree/2.x/docs/images/JDBC_Connection.jpg)

Il faut saisir les informations n�cessaires comme dans l�image ci-dessous en fonction de votre connexion :
![](https://github.com/SDIS62/prevarisc/tree/2.x/docs/images/Exemple_Connection.jpg)

Vous pouvez garder le nom � prevarisc � comme nom de connexion car les rapports existants sont param�tr�s sur celui-ci.