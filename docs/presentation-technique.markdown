---
layout: default
title: Documentation
description: Guide d'installation et d'utilisation de Prevarisc
---

## Présentation technique

### Contexte

Prevarisc est une application web open source, sous licence CeCILL-B (voir la licence [ici](https://github.com/SDIS62/prevarisc/blob/2.x/LICENSE.md)) maintenue par le service Recherche et Développement du SDIS 62 en collaboration avec les services prevention / prévision du SDIS 62, les [collaborateurs externes du projet](https://github.com/SDIS62/prevarisc/graphs/contributors), et le ministère de l'intérieur.
Le logiciel de gestion de versions utilisé est Git, et le service web utilisé pour héberger le code source est Github. L'adresse du dépôt officiel de Prevarisc est [https://github.com/SDIS62/prevarisc]().

### Socle technique

Prevarisc est développé en PHP >= 5.5.

La gestion des dépendances est effectuée avec [Composer](https://getcomposer.org). Du fait, les dépendances requises par l'application sont recensées dans le fichier [composer.json](https://github.com/SDIS62/prevarisc/blob/2.x/composer.json). Voici un résumé :

* Zend Framework 1 : Base technique sur laquelle l'application s'appuie pour l'ensemble du routage, des vues, des injections de dépendances, etc ..
* odtPHP : Librairie permettant de générer des fichiers .odt
* gd_resize : Librairie permettant de manipuler des images (avatars, etc ..)
* SDIS62 Toolbox : Boite à outils du SDIS62 pour le développement
* Sebastian Git : Permet de récupérer les informations de la version actuellement utilisée
* vObject : Permet de synchoniser des événements pour une intégration dans un calendrier

### Fonctionalités générales

* Gestion de Site, ERP / Cellule, Habitation, IGH, EIC, Camping, Manifestation temporaire, IOP, Zone.
* Gestion de dossier d'étude, visite de commission, groupe de visite, réunion, courrier / courriel, intervention, arrêté.
* Gestion des commissions (membres types, compétences, documents types) avec un calendrier permettant de le planning des passages en salle, en visite etc ..
* La génération des compte-rendus, procès verbaux, convocations aux commissions etc ... se fait un .odt (le format ODT est le format de documents par défaut de la suite bureautique OpenOffice).
* La géolocalisation des établissements utilise au choix : OpenStreetMap Nominatim, Géoportail.
* La cartographie utilise au choix les services : Google Map, Géoportail. L'ajout de couches supplémentaires est possible au format WMS.
* L'authentification à l'application utilise au choix : La base de données interne, LDAP, CAS, NTLM.
* Paramétrage complet de Prevarisc : Tableau des périodicités, textes applicables, prescriptions types, documents, communes / groupements de communes.

### Développement

Pour pouvoir collaborer sur le projet, vous pouvez utiliser Git et Github afin de forker le projet depuis le dépôt principal et nous soumettre vos développements via une Pull Request (cette dernière doit répondre à notre [convention de contribution](https://github.com/SDIS62/prevarisc/blob/2.x/.github/CONTRIBUTING.md)).
