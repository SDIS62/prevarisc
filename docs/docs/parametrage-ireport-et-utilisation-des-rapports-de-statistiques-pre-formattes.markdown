---
layout: default
title: Documentation
description: Guide d'installation et d'utilisation de Prevarisc
---

# Paramètrage iReport et utilisation des rapports de statistiques pré-formattés

## Téléchargement de ireport 5.6 et java 7
Le téléchargement de ireport 5.6 se fait à partir de la page suivante : [http://sourceforge.net/projects/ireport/files/iReport/iReport-5.6.0/]()
De plus, il faut télécharger le JDK java 7 à partir de la page suivante : [http://www.oracle.com/technetwork/java/javase/downloads/jdk7-downloads-1880260.html]()

## Configuration avant le premier lancement de iReport
Après avoir installé ireport 5.6 et le JDK Java 7, il faut modifier le fichier « ireport.conf » dans le répertoire ```C:\Program Files (x86)\Jaspersoft\iReport-5.6.0\etc\``` en mettant l’emplacement du jdk java (exemple : ```jdkhome = C:\Program Files\Java\jdk1.7.0_79```).

> Note : ne pas oublier d’enlever le # devant la ligne.

Lancer "iReport-5.6.0" (sans ce paramètre, ireport ne se lance pas mais il n'y a pas de message d'erreur).

## Connexion à la base de données Prevarisc
Lors du premier lancement, vous arriver sur cette page :
<img src='{{ "/assets/img/page_accueil.JPG" | prepend: site.baseurl }}' style="width: 300px;" />

Cliquer sur l’icône pour créer une connexion à une base de données :
<img src='{{ "/assets/img/icone_creation_connexion.JPG" | prepend: site.baseurl }}' style="width: 50px;" />

Choisir « Database JDBC Connection » :
<img src='{{ "/assets/img/JDBC_connection.JPG" | prepend: site.baseurl }}' style="width: 200px;" />

Il faut saisir les informations nécessaires comme dans l’image ci-dessous en fonction de votre connexion :
<img src='{{ "/assets/img/exemple_connection.JPG" | prepend: site.baseurl }}' style="width: 400px;" />

Vous pouvez garder le nom « prevarisc » comme nom de connexion car les rapports existants sont paramétrés sur celui-ci.
