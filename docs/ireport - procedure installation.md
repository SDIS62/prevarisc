# iReport - Procédure d'installation #

## Téléchargement de ireport 5.6 et java 7 ##
Le téléchargement de ireport 5.6 se fait à partir de la page suivante : http://sourceforge.net/projects/ireport/files/iReport/iReport-5.6.0/
De plus, il faut télécharger le JDK java 7 à partir de la page suivante : http://www.oracle.com/technetwork/java/javase/downloads/jdk7-downloads-1880260.html

## Configuration avant le premier lancement de iReport ##
Après avoir installé ireport 5.6 et le JDK Java 7, il faut modifier le fichier « ireport.conf » dans le répertoire « C:\Program Files (x86)\Jaspersoft\iReport-5.6.0\etc\ » en mettant l’emplacement du jdk java :
Exemple : jdkhome = « C:\Program Files\Java\jdk1.7.0_79 »
Note : ne pas oublier d’enlever le # devant la ligne
Puis lancer "iReport-5.6.0" sans problème (sans ce paramètre, ireport ne se lance pas mais il n'y a pas de message d'erreur)

## Création de la connexion à la base de données ##
Lors du premier lancement, vous arriver sur cette page :
![](https://raw.githubusercontent.com/SDIS62/prevarisc/2.x/docs/images/page_accueil.JPG)

Cliquer sur l’icône pour créer une connexion à une base de données : ![](https://raw.githubusercontent.com/SDIS62/prevarisc/2.x/docs/images/icone_creation_connexion.JPG)

Choisir « Database JDBC Connection » :
![](https://raw.githubusercontent.com/SDIS62/prevarisc/2.x/docs/images/JDBC_connection.jpg)

Il faut saisir les informations nécessaires comme dans l’image ci-dessous en fonction de votre connexion :
![](https://raw.githubusercontent.com/SDIS62/prevarisc/2.x/docs/images/exemple_connection.jpg)

Vous pouvez garder le nom « prevarisc » comme nom de connexion car les rapports existants sont paramétrés sur celui-ci.
