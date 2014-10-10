# Changelog

## 2.2

Evolution :

* Ajout du champ "droit public" sur les établissements uniquement
* Mise sous cache APC du service User
* Application de la règlementation GE4§3 sur les alertes des ERP sans prochaine date de visite
* Ergonomie : bouton modifier un dossier repassé en haut à droite
* Retrait de la section recherche de courriers obsolètes
* Ajout de ce fichier changelog

Corrections :
* Correction des alertes sur ERP défavorables non ouverts

## 2.1

Evolutions :

* Refonte de la recherche sur les dossiers
* Ajout des précisions sur le type de la commission dans établissement, dossier et calendrier
* Prise en compte du GE4§3 sur la date de prochaine visite dans la fiche établissement
* Ajout de la date exacte de dernière visite sur la fiche établissement
* Agrandissement du champ "numéro de plan" d'intervention pour y accueillir un chemin type Windows vers un montage ou un répertoire partagé
* Convertion du champ SHON/SHOB en Surface de plancher
* Ajout d'un droit pour modifier le statut d'un établissement
* Ajout d'un droit pour changer l'avis de la commission
* Ajout du champ risques naturel et technologiques sur le descriptif technique
* Ajout de la gestion des zones, IOP, manifestations temporaires et campings
* Refonte des genres de courriers pour se baser sur des genres issues du SDIS29, plus exploitable en l'état
* Ajout de la compatibilité avec un accès HTTPS (corrections)
* Possibilité de déporter le repository des fichiers sur un nas (cifs, nfs) via la variable REAL_DATA_PATH (prépare le chantier de gestion des pièces jointes par md5)
* Ajout d'une ressource zend (handler) permettant de changer le répertoire et le mode l'organisation des fichiers (cf chantier de passage en md5 chez nous) ==> chantier uniquement effectué sur les fichiers à forte volumétrie, fichiers courriers, et rapport à faire plus tard
* Ajout du type de la commission dans le titre du calendrier et la gestion des odj
* Ajout d'un controlleur intermédiaire de download des fichiers avec possibilité de forcer un nom plus sympatique pour l'utilisateur
* Ajout de champs de fusion supplémentaires dans les rapports + utilisation d'une fonction de try-catch pour éviter les erreurs de génération
* Ajout d'une gestion par droit pour le dashboard plutot qu'en "dur" sur des fonctions... plus flexible !
* Ajout de la possibilité de charger des plugins tiers
* Ajout d'API pour une version mobile de Prévarisc

Corrections :

* Les sites ne peuvent pas être établissements enfants (et donc ne peuvent pas avoir de père)
* Correction de l'apparition de "undefined" dans la liste des avis commission d'un dossier sur certains genres
* Correction de la non suppression du cache établissement lors de la modification du descriptif technique
* Correction de la non suppression du cache ACL lors de la modification des ACL
* Correction d'une anomalie de performance sur la recherche
* Corrections multipes sur les SQL, notamment sur le schéma des zones
* Corrections sur la gestion du droit d'accès en modification à "ID_STATUT"
* Correction de l'interface de recherche des dossiers
* Séparateurs de répertoires en dur (cf compatibilité Windows)
* Chemin de répertoire incorrect dans CalendrierDesCommissionsController à la suppression d'une date de commission
* Correction de la compatibilité avec la mise à jour de Zend Framework 1.2.9 (actuellement 1.2.7) sur les escaping de noms de tables
* Corrections sur l'init SQL pour permettre une fresh install
* Créations ressources spécialisées sur zone et habitation
