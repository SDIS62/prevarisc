# Changelog

## 2.4

Evolutions :
* Mise à jour de packery en 1.3.0
* Ajout d'un message d'avertissement à la suppression de PJ
* Ajout des icones sur la liste des pièces jointes
* Uniformisation de la gestion des pièces jointes avec les dossiers : ajout de la possibilité de modifier un contact
* Ajout d'un paramètre account form au niveau de la connexion LDAP pour éviter d'avoir à retaper le domaine à chaque fois
* Affichage de la commune du site et de l'ID ERPv2 pour la popup d'information d'un établissement
* Bloc dossiers du préventionnistes : retrait des dossiers périodiques : trop de dossiers affichés en début d'année sinon
* Recherche par ID ERPv2 dans la barre de recherche rapide
* Ajout d'un retour d'erreur si le controlleur proxy échoue
* Amélioration de l'IHM de génération de rapports
* Ajout du champ civilité et grade pour les rapports de dossiers
* Ajout de la possibilité de désélectionner un élément dans la recherche des dossiers
* Possibilité de recherche un dossier "contenant" (et non pas exactement égal à) un numéro d'AT ou PC
* Possibilité de mettre un ID ERPv2 ou N° de PC dans les champs d'objet ou de libellé sans # en préfixe
* Mise à jour des dépendances composer
* Agrandissement du champ "Nom" des rubriques ICPE pour pouvoir s'adapter à tous les cas comme "Stations-service : installations, ouvertes ou non au public, où les carburants sont transférés de réservoirs de stockage fixes dans les réservoirs à carburant de véhicules à moteur, de bateaux ou d’aéronefs"
* Ajout de l'étude Demande d'organisation de manifestation temporaire
* Ajout de l'heure de visite dans les CR générés
* Pour les courriers ajout d'un indicateur de réponse / non réponse plutot que l'avis
* Tri du bloc des dossiers non verrouillés par date de visite desc ou date de création desc si le 1er est null
* Tri du bloc des courriers sans réponse par date de création desc
* Optimisations importantes de performances côté ACL sur les sites qui possèdent des fils nombreux avec des adresses, et avec un nombre de groupements géographiques importants : implémentation d'un lazyloader de ressources
* Ajout du numéro d'établissement dans le libéllé côté dossier
* Possibilité de rechercher directement par numéro d'établissement sans le # pour le 29
* Régularisation sur l'ajout des nouveaux genres et des migrations de données
* Ajout du champ numWinPrev pour les CR de commissions
* Si clé IGN entrée, remplacement du geocoding par IGN avec carte
* Ajout de click sur la carte IGN pour placer le POI précisément
* Ajout du chargement en ajax asynchrone du tableau de bord : induit des lenteurs importantes lorsqu'ils y a trop d'éléments
* Ajout du grade dans la génération des PV et ODJ pour les préventionnistes attachés au dossier
* Le bloc des établissements sans prochaines visites périodiques programmées tient maintenant compte les dossiers de type avant ouverture
* Ajout de la possibilité de replier les blocs du tableau de bord
* Ajout du grade dans la génération des PV et ODJ pour les préventionnistes attachés au dossier

Corrections :
* Impossibilité de créer des dossiers en décembre : mauvais validateur de date
* Sur firefox, sur le tableau de bord, impossible de scroller sur les blocs sans déplacer le bloc avec
* Ajout d'une image manquante sur tipsy
* Perf : remplacement des commentaires HTML par des commentaires PHP dans les partials de result search
* Perf : affichage des éléments du dashboard seulement ordonnés
* Recherche géographique : un texte saisi dans la ville ne suffit pas à sélectionner la commune
* Correction d'un problem d'update composer KO sur apigen en version dev-master impossible car il ne trouve pas une dépendance
* Correction des tailles des champs max sur le formulaire de modification de contact
* Correction sur le bloc des VP non effectuées pour 2015
* Correction du conseil sur la périodicité d'un établissement
* Correction d'un undefined index "infosEtab" dans gestionodj
* Correction d'un undefined index "ID_DATECOMMISSION" dans le tableau de bord, bloc des prochaines commissions + lien mort
* Correction sur des plurieurs dans les durées affichées sur les courriers
* Correction (patch) sur le mauvais conseil sur les commissions affectés aux établissements (le fonctionnement est à revoir avec des ids)
* Retrait de la condition empêchant de générer des convocations pour des établissements qui ne sont pas des ERP, cellules ou IGH
* Correction d'une régression sur la génération de dossiers
* Correction de libellés côté fiche contact
* Correction de la sauvegarde des préférences utilisateur
* Correction sur la date de visite sur la modification d'un dossier qui "sautait" à chaque modification
* Correction d'un erreur javascript sans incidence sur la double inclusion de dossiergeneral.js
* Correction de l'utilisation de la variable PREVARISC_REAL_DATA_PATH sous Windows Serveur
* Correction d'un problème sur la recherche de courriers sans établissements rattachés
* Correction sur le bloc "dossiers suivi du préventionniste" qui ne doivent pas forcément être verrouillés pour disparaître de son bloc
* Correction importante lors du déplacement/modification d'une date de commission/visite sur la calendrier, non répercuté sur la liste des dossiers d'un établissement
* Correction de la possibilité d'ajouter une PJ sur un dossier même verrouillé
* Augmentation de la limite du nombre d'utilisateurs connectés affichés dans le BO (avec +300 maires, on ne voit pas tous les utilisateurs)
* Correction du bloc "dossiers suivi du préventionniste" qui affichait trop de dossiers avec le retrait du verrouillage. On n'affiche plus que les courriers et études.
* Correction générale sur les blocs du dashboard : les dossiers sont enregistrés avec un avis commission = 0 !!! et non null
* Correction sur les convocations pour les maires : partie dossiersInfos KO
* Correction de l'avis toujours indisponible lorsque le dossier est créé, et conséquence sur la possibilité de différer l'avis
* Correction sur la génération des ODJ et convocation : la balise nomPereEtab reprenait le nom du précédent établissement père

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
* Ajout du champ document d'urbanisme pour l'instruction du permis d'aménager et de démolir
* Retrait de la prolongation de visite du GE4§3 sur 2 VP positives de suite, rendu obsolète
* Ajout du libellé du type de commission dans la liste déroulante du type de commission pour un établissement
* Modification de la nomenclature du nom des rapports générés
* Retrait des variables globales d'exemples de risques naturel : configuration inutile...
* Mise en surbrillance de l'onglet de la commission dans le calendrier des commissions


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
* Modification du libellé de la box de génération d'un rapport : "fermer" au lieu de "annuler"
* Correction du bouton générer un rapport qui ne fonctionnait pas sur toutes les pages de dossier
* Correction d'un problème d'initialisation des blocs lorsqu'un bloc manque dans les préférences utilisateur
* Correction sur le controlleur proxy
* Correction sur le bloc des ets défavorables sur la commune dans le cas où l'utilisateur n'a pas de commune

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
