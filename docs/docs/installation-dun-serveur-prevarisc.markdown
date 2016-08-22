---
layout: default
title: Documentation
description: Guide d'installation et d'utilisation de Prevarisc
---

## Installation d'un serveur Prevarisc sous Debian

Pré-requis  :
* Installer VirtualBox [https://www.virtualbox.org/wiki/Downloads]();
* Avoir une machine virtuelle sous Debian >=7 ([https://www.debian.org/distrib/]()) (à la selection des logiciels à installer proposés par l'utilitaire d'installation de Debian, choisir "serveur SSH" ainsi que "Utilitaires usuels du système") ;
* La machine doit avoir sa propre adresse IP (différente de la machine hôte) ;

## Installation du serveur Web (apache, mysql, php)

### Installation des dépendances

* Ouvrir une invite de commande et taper ```su``` puis le mot de passe administrateur
* Taper : ```aptitude install apache2 mysql-server php5 php5-gd php5-ldap php5-mysql curl git```
* Saisir le mot de passe “root” pour MySQL

> Afin de tester si le serveur web répond bien, vous pouvez tenter un accès via un navigateur web sur http://ADRESSE_IP_DU_SERVEUR

### Activation des modules

* Taper : ```a2enmod rewrite```
* Taper : ```a2enmod expires```
* Taper : ```a2enmod include```

### Ajout d'un virtualhost

Votre serveur web est prêt à accueillir Prevarisc. Il faut maintenant configurer un VirtualHost afin de pouvoir d'une part y accéder via une URL bien définie, et d'autre part pour spécifier les valeurs de configuration.

* Ajouter un fichier vide dans ```/etc/apache2/sites-available``` ("prevarisc.conf" par exemple).
* Editer via ```nano /etc/apache2/sites-available/<VOTRE FICHIER>.conf```, et ajouter :
```
<VirtualHost *:80>
    ServerName prevarisc.sdisxx.fr
    DocumentRoot /var/www/prevarisc/public
    SetEnv [CLE DE CONFIGURATION] [VALEUR]
    <Directory /var/www/prevarisc/public>
        DirectoryIndex index.php
        AllowOverride all
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

Où xx est le numéro de département.

Vous pouvez ajouter autant de clés de configuration associées à votre domaine que nécessaire.

La liste complète des clés de configuration et des valeurs spécifiques associées :

Clé de configuration | Description | Valeur possible
-------------------- | ----------- | ---------------
PREVARISC_DB_ADAPTER | Adaptateur à utiliser lors de la connexion à la base de données | Pdo_Mysql (recommandé) Pdo_Ibm Pdo_Mssql Pdo_Oci Pdo_Pgsql Pdo_Sqlite
PREVARISC_DB_CHARSET | Encodage de la base de données | utf8
PREVARISC_DB_HOST | Adresse de la base de données | Adresse IP
PREVARISC_DB_USERNAME | Nom d'utilisateur à utiliser lors de la connexion à la base de données | Chaine de caractères
PREVARISC_DB_PASSWORD | Mot de passe de connexion à la base de données | Chaine de caractères
PREVARISC_DB_DBNAME | Nom de la base de données | Chaine de caractères
PREVARISC_CACHE_LIFETIME | Durée de vie du cache, actif si valeur > 0 | Valeur numérique (secondes)
PREVARISC_CACHE_ADAPTER | Adapter backend de cache du cache lié à la factory Zend_Cache, default "APC" | Chaine de caractères
PREVARISC_CACHE_HOST | Adresse IP du cache backend | Adresse IP
PREVARISC_CACHE_PORT | Port du cache backend | Valeur numérique
PREVARISC_CACHE_DIR | Répertoire des fichiers de cache si cache filesystem | Répertoire
PREVARISC_SECURITY_SALT | Chaine utilisée pour le cryptage des mots de passe utilisateur | Chaine alphanumérique de longueur 32 (exemple : 7aec3ab8e8d025c19e8fc8b6e0d75227 salt utilisé par défaut)
PREVARISC_CALENDAR_REFRESH_TIME | [FACULTATIF] L'intervale de temps entre chaque rafraichissement de la synchronisation des calendriers | durée en format ISO 8601 (exemple : "PT5M" utilisé par défaut pour un rafraichissement tous les 5 minutes)
PREVARISC_LDAP_ENABLED | [FACULTATIF] Activation de la connexion des utilisateurs via LDAP | 1 ou 0
PREVARISC_LDAP_HOST | [FACULTATIF] Adresse du serveur LDAP | Adresse IP
PREVARISC_LDAP_USERNAME | [FACULTATIF] Nom d'utilisateur à utiliser lors de la connexion au LDAP | Chaine de caractères
PREVARISC_LDAP_PORT | [FACULTATIF] Port de connexion au LDAP | Entier
PREVARISC_LDAP_PASSWORD | [FACULTATIF] Mot de passe de connexion au LDAP | Chaine de caractères
PREVARISC_LDAP_BASEDN | [FACULTATIF] Chaine de selection afin de trouver les utilisateurs dans le LDAP | Chaine de caractères (exemple : DC=sdisxx,DC=fr)
PREVARISC_LDAP_ACCOUNT_FORM | [FACULTATIF] Format des comptes LDAP à utiliser, par défaut domaine\login   | Entier voir class Zend_Ldap
PREVARISC_DEBUG_ENABLED | [FACULTATIF] Activation du mode debug | 1 ou 0
PREVARISC_PLUGIN_IGNKEY | [FACULTATIF] Clé IGN pour afficher la carte | Valeur alphanumérique
PREVARISC_PLUGIN_GOOGLEMAPKEY | [FACULTATIF] Clé Google Map pour afficher la carte (non recommandé) | Valeur alphanumérique
PREVARISC_THIRDPARTY_PLUGINS | [FACULTATIF] Liste des classes de Plugins tiers séparée par un ";" à charger au démarrage | Chaine de caractères (exemple : Plugin_MyPlugin;Plugin_YourPluginInc)
PREVARISC_PROXY_ENABLED | [FACULTATIF] Active le passage par un proxy lors de l'appel aux URL /proxy | 0 ou 1 pour l'activer
PREVARISC_PROXY_PROTOCOL | [FACULTATIF] Le protocol du proxy | Chaine de caractères (exemple : HTTP ou HTTPS)
PREVARISC_PROXY_HOST | [FACULTATIF] Le nom de domaine ou IP du proxy | Chaine de caractères (exemple : 192.168.12.3)
PREVARISC_PROXY_PORT | [FACULTATIF] Le port du proxy | Nombres (exemple : 8080)
PREVARISC_PROXY_USERNAME | [FACULTATIF] Le nom d'utilisateur du proxy si une authentification est nécessaire, laisser vide sinon | Chaine de caractères (exemple : myusername)
PREVARISC_PROXY_PASSWORD | [FACULTATIF] Le mot de passe du proxy si une authentification est nécessaire, laisser vide sinon | Chaine de caractères (exemple : mypassword)
PREVARISC_PROXY_REQUEST_FULLURI | [FACULTATIF] Effectuer les requêtes aux proxy en full URI et non relatives | Boolean : true ou false
PREVARISC_COMMISSION_COMMUNALE_TYPE | [FACULTATIF] Identifiant de l'id du type de commission communale, par défaut "2" | Entier
PREVARISC_LOCAL_SOMMEIL_TYPES | [FACULTATIF] Identifiant des types d'activité obligatoirement en local à sommeil séparaés par un ";", par défaut "7;11" | Liste d'entiers séparés par ";"
PREVARISC_DATASTORE_ADAPTER | [FACULTATIF] Permet de charger un plugin changeant le mode de stockage des pièces jointes établissement, dossier, et commissions | Classe PHP implémentant Plugin_Interface_DataStore
PREVARISC_REAL_DATA_PATH | [FACULTATIF] Répertoire de stockage des fichiers de prévarisc | Chaine de caractères
PREVARISC_DATA_PATH | [FACULTATIF] Préfixe des URL d'accès aux médias et pièces jointes | Chaine de caractères
PREVARISC_APPLICATION_PATH | [FACULTATIF] Répertoire racine de l'application Prévarisc | Chaine de caractères
PREVARISC_REVISION | [FACULTATIF] Forcer la révision de l'installation prévarisc, <commit_git> sinon   | Chaine de caractères
PREVARISC_BRANCH | [FACULTATIF] Forcer la branche de l'installation prévarisc, <branche_git> sinon   | Chaine de caractères
PREVARISC_CAS_ENABLED | [FACULTATIF] Permet d'activer l'authentification CAS | 0 ou 1 pour l'activer
PREVARISC_CAS_HOST | [FACULTATIF] Le hostname du serveur cas, ex: cas.example.com | Chaine de caractères
PREVARISC_CAS_PORT | [FACULTATIF] Le port du serveur cas, normalement 443 | Entier
PREVARISC_CAS_CONTEXT | [FACULTATIF] Le contexte, ou URI CAS, ex : 'cas' | Chaine de caractères
PREVARISC_CAS_NO_SERVER_VALIDATION | [FACULTATIF] Désactive la vérification du certificat du serveur CAS | Chaine de caractères
PREVARISC_CAS_VERSION | [FACULTATIF] Permet de préciser la version du protocol cas, 2.0 par défaut | 1.0, 2.0, 3.0
PREVARISC_NTLM_ENABLED | [FACULTATIF] Permet d'activer l'authentification NTLM | 0 ou 1 pour l'activer

Afin d'activer un VirtualHost dont la configuration est stockée dans sites-available, il faut utiliser a2ensite (Apache 2 Enable Site).

* Exécuter ```a2ensite <VOTRE FICHIER>.conf```
* Exécuter ```service apache2 restart```

### Installation de Prevarisc

En ligne de commande, rendez-vous dans le dossier ```/var/www```.

* Taper la commande suivante : ```git clone https://github.com/SDIS62/prevarisc``` (ceci télécharge les sources de Prevarisc dans le répertoire courant depuis le dépôt officiel GitHub)

Prevarisc utilise un fichier composer.json qui liste l’ensemble des librairies PHP nécessaires à son fonctionnement. Pour installer ces dépendances :

* Se placer dans le repertoire prevarisc
* ```curl -sS https://getcomposer.org/installer | php```
* ```php composer.phar install --no-dev -o```

Pour que Prevarisc puisse fonctionner convenablement, il est nécessaire de modifier les droits des fichiers.
L’ensemble du dossier /var/www/prevarisc/public doit avoir pour propriétaire www-data (nom d’utilisateur attribué par défaut au service apache).

* Se placer dans le repertoire prevarisc
* ```chown -R www-data:www-data public```
* ```chmod -R 555 .```
* ```chmod -R 755 public```

### Accéder à Prevarisc

Pour que vous puissiez accéder à Prevarisc, vous devez contacter le serveur par son nom.
Lorsque vous êtes équipé d’un serveur DNS, il suffit d’y insérer une nouvelle entrée afin qu’il connaisse la relation entre l’adresse IP et le nom de la machine.
Dans le cadre de cette documentation, nous allons simuler une résolution de nom grâce au fichier hosts de la machine locale hébergeant la VM.

Chemin du fichier :
* Pour Windows: C:\windows\system32\drivers\etc\hosts
* Pour Linux/Max: /etc/hosts

Pour que votre machine accède à votre serveur virtuel Prevarisc, il faut ajouter la ligne suivante dans le fichier hosts : ```<ADRESSE IP DU SERVEUR> prevarisc.sdisxx.fr```

Maintenant, lorsque vous taperez prevarisc.sdisxx.fr dans votre navigateur web, le serveur web sera interrogé et le VirtualHost précédemment configuré nous redirigera sur le site Prevarisc

### Déploiement de la base de données

Le site Prevarisc est maintenant accessible mais non fonctionnel car nous n’avons pas encore crée la base de données.
Cette étape va permettre la mise en place de la structure de la base de données (Tables et liaisons), ainsi que l’insertion des données de bases nécessaires au bon fonctionnement.

* Se placer dans le repertoire prevarisc
* ```mysql -u <username> -p```
* Enter password: ********
* ```CREATE DATABASE prevarisc;```
* ```exit```
* ```mysql -u <username> -p <databasename> < sql/init/prevarisc.sql```

### Hourra !

Vous pouvez maintenant vous connectez avec le login / mot de passe : root / root (à désactiver le plus rapidement possible dans la console d'administration).

### Mettre à jour Prevarisc

Pour mettre à jour prevarisc :

* Se placer dans le repertoire prevarisc
* ```git pull```
* Si vous voyez dans les fichiers mis à jour une "migration" sql, vous devez l'exécuter avec ```mysql -u <username> -p <databasename> < sql/migrations/NOM DE LA MIGRATION.sql```
