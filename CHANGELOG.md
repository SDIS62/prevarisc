# Changelog

## 2.3

Evolutions :

* Génération des rapports : reprise des contacts de l'établissement si contacts du dossier absent
* Ajout de variables de configurations dans la page d'accueil de l'admin
* Ajout de l'affichage de l'extension du fichier dans la partie pièce jointe
* Ajout de couleurs sur les liens des popup (illisible avant)
* Ajout du bouton "Générer un document" sur tous les onglets de la partie dossier
* Suppression des actions de la barre latérale dans les dossiers et qui ne fonctionne pas et en doublon...
* Ajout d'un message d'avertissement sur une visite positive avant ouverture d'un ets en projet
* Amélioration visuelle de la liste des commissions et groupements d'un utilisateur
* Ajout d'un bouton pour ré-initialiser la recherche et uniformisation visuelle
* Ajout d'un mécanisme d'agrandissement du champ de recherche rapide au focus
* Refonte de la gestion des blocs en services
* Ajout de plusieurs blocs
* Ajout de la gestion des préférences utilisateur et de la gestion de l'ordre des blocs sur la HP
* Tri des documents par ordre alphabétique sur l'admin des modèles
* Ajout des natures de dossier Autorisation d’une ICPE, Certificats d'urbanisme (CU)
* Ajout du petit calendrier sur la partie dossier sur la date d'insertion du dossier
* Ajout de la possibilité de modifier manuellement la géolocalisation d'un établissement
* Ajout du conseil de remplir une présence de local à sommeil si aucune sélection de radio button n'a été faite
* Ajout du champ document d'urbanisme pour l'instruction du permis d'aménager

Corrections :

* Correction du titre de l'évenement ajouté au calendrier des comission dans l'ajout/modif d'un dossier en lien avec les titre des établissements modifiés
* Correction d'un problème d'overflow et align center sur l'auto-complete des nom des contacts existants sur les établissements
* Correction d'un problème de style sur les onglets sélectionnés dans dossiers et ets
* Correction d'un problème sur le filtre "classe" dans la recherche des établissements
* Correction d'un warning sur variable $this->afficherChamps non définit dans le template dossier/general sur les nouveaux dossiers
* Correction d'erreurs sur ajout et suppression d'un modèle de rapport avec quotes dans le fichier
* Correction de l'ajout d'un RDV dans le calendrier pour un établissement contenant des quotes
* Correction d'un problème de performance dus aux GROUP BY sur la recherche
* Correction d'une coquille sur la recherche par adresse des cellules et sites
* Correction d'une erreur lors de la consultation d'un groupement avec des préventionnistes affectés
* Correction d'un problème de superposition des libellés sur les documents consultés si le libellé est trop long
* Retrait de 2 requêtes en doublons sur l'édition utilisateur
* Correction d'un warning sur la partie calendrier des commissions
* Correction sur le nom vide d'un établissement sur le partial de résultat de recherche d'un dossier
* Retrait de code mort sur le service dashboard
* Lorsqu'on sait local à sommeil = oui pour un type PE, l'application propose local à sommeil = non alors que l'utilisateur a fait son choix

## 2.2

Evolutions :

* Ajout du champ "droit public" sur les établissements uniquement
* Mise sous cache APC du service User
* Application de la règlementation GE4§3 sur les alertes des ERP sans prochaine date de visite
* Ergonomie : bouton modifier un dossier repassé en haut à droite
* Retrait de la section recherche de courriers obsolètes
* Ajout de ce fichier changelog
* Tri par ordre alphabétique des modèles de rapports dans la partie dossier dans la selectbox
* Dashboard : ajout du lien vers l'ordre du jour de la date de commission sur le bloc d'ordre du jour
* Ajout du descriptif de l'erreur lorsqu'on arrive pas à créer un dossier via le SimpleDataStore
* Optimisations de performances sur les ACL
* Ajout de la variable d'environnement PREVARISC_PARKINGS_TYPES précisant les Ids des types d'activités liés aux parkings
* Déplacement et agrandissement de l'identifiant externe
* Ajout de la possibilité de pas avoir un copie de travail git et forcer une version applicative dans le footer
* Fixes #302 Passage des champs de stabilité au feu et isolement en minutes au lieu des heures
* En lien avec #306, retrait de l'ajout du champ LIBELLE_RUETYPE dans l'affichage des adresses

Corrections :

* Correction des alertes sur ERP défavorables non ouverts
* Correction d'une 404 sur le fichier application.combined.js
* Correction sur le nom des ressources (droits) du dashboard
* Déplacement de la récupération des modèles de rapports de la view vers le controlleur
* Correction des références à PRV_prevarisc_v2 dans les SQL de migration
* Correction d'un KO sur la recherche de contacts existants
* Corrections liées à la migration zend framework 1.2.9 dans le controlleurs de statistiques
* Correction d'un problème sur les ACL lors d'une 2e sollicitations de la méthode : disparition des ACL lors d'un 2e call à APC <= mise en variable static au 1er call
* Correction d'un problème de download sur les pièces jointes d'un établissement
* Correction d'un problème sur l'affichage des pièces jointes hors jpg dans les mises en avant sur la fiche établissement
* Impossible de créer une nouvelle date de commission à la création d'un dossier si celui-ci n'est pas lié à un établissement (pb de js)
* Correction de l'apparition du bouton "Modifier" sur la partie dossier qui était présent sur tous les onglets et pour chaque niveau de site, cellule...
* Correction sur documents consultés de dossier "$ is undefined" : report de l'ajout du js inline dans le controlleur au lieu du dossier
* Correction du libellé "Places de stationnement" qui ne revient pas à Public lors de la sélection d'une autre activité
* Corrections de warnings (undefined indexes) sur la génération des documents
* Corrections du bouton modifier le dossier qui apparaît à la création du dossier
* Correction de l'appel au service IGN de géolocalisation : envoi du code postal entier à la place des 2 premiers chiffres du code insee
* Correction : la recherche multi-critère incluant la recherche sur une commune rendait 0 résultats : la recherche imposait la présence d'une commune égale pour les sites et cellules liées
* Correction des simples quotes dans les groupements de communes qui cassent les listes des membres de commissions et services instructeurs

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
