# Documentation d'installation #

## Etape 1 : installation d'un serveur web Debian ##

Deux options s'offrent à vous :
* soit une installation définitive sur un serveur dédié (voir étape 1a)
* soit une installation de test sur un poste de travail (ordinateur de bureau ou portable) (voir étape 1b)

### Etape 1a : installation d'un serveur Debian ###

* Télécharger la version stable de Debian (classiquement version i386 avec interface xfce)
* Installer le serveur de préférence en anglais

### Etape 1b : installation d'un serveur Debian sur Oracle VM VirtualBox ###

* Télécharger Oracle VM VirtualBox et son pack d'extensions (https://www.virtualbox.org/wiki/Downloads)
* Télécharger la version stable de Debian (classiquement version i386 avec interface xfce)
* Installer Oracle VM VirtualBox et exécuter le programme
* Aller dans le menu Fichiers->Paramètres puis Extension et ajouter l’Extension Pack
* Dans Oracle VM VirtualBox, cliquer sur Nouvelle, saisir un nom de machine (ex : Debian) et cliquer plusieurs fois sur “Suivant”. Votre machine apparait “Eteinte” dans la partie gauche.
* Effectuer un clic droit sur Debian, choisir Démarrer, cliquer sur le dossier avec une flèche verte, sélectionner le fichier debian-X.X.X-i386-xfce-CD-1.iso et cliquer sur “Démarrer”

## Etape 2 : installation du serveur web ##

* Ouvrir une invite de commande et taper “su” puis le mot de passe administrateur
* Taper : ```aptitude install apache2 mysql-server php5 php5-gd php5-ldap php5-mysql php-apc mysql-workbench curl git p7zip-full```
* A la demande, faire un clic droit sur le CD en bas pour remonter l’image debian-X.X.X-i386-xfce-CD-1.iso et appuyer sur “Enter”
* Saisir le mot de passe “root” pour MySQL
* Taper : ```a2enmod rewrite```
* Taper : ```a2enmod expires```
* Taper : ```a2enmod include```

Votre serveur web est prêt à accueillir Prevarisc. Il faut maintenant configurer un VirtualHost afin de pouvoir d'une part y accéder via une URL bien définie, et d'autre part pour spécifier les valeurs de configuration.

* Taper : ```nano /etc/apache2/apache2.conf```, insérer à la fin le code suivant :
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
PREVARISC_MAIL_ENABLED | [FACULTATIF] Activation de la gestion du système d'alerte par mail | 1 ou 0
PREVARISC_MAIL_TRANSPORT | [FACULTATIF] Système de transport des mails | "smtp" ou "Sendmail"
PREVARISC_MAIL_HOST | [FACULTATIF] IP du serveur mails | Chaîne de charactères
PREVARISC_MAIL_USERNAME | [FACULTATIF] Nom d'utilisateur ayant les droits de connexion au serveur Mail (peut-être vide si aucune authentification nécessaire) | Chaîne de caractères
PREVARISC_MAIL_PASSWORD | [FACULTATIF] Mot de passe de l'utilisateur ayant les droits de connexion au serveur Mail | Chaîne de caractères
SetEnv PREVARISC_MAIL_PORT | [FACULTATIF] Le port du serveur mail | Nombres (exmple : "25")
PREVARISC_MAIL_SENDER | [FACULTATIF] Adresse email apparaissant sur les mails envoyés par l'application | Chaîne de caractères (format email, par exemple : "no-reply@sdis.fr")
PREVARISC_MAIL_SENDER_NAME | [FACULTATIF] Nom associé à l'email définit précédemment et apparaissant sur les mails envoyés par l'application | Chaîne de caractères (exemple : "Prévarisc")
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
PREVARISC_PLUGIN_GEOCONCEPT_URL | [FACULTATIF] URL du serveur Geoconcept couche WMTS | Chaine de caractères
PREVARISC_PLUGIN_GEOCONCEPT_LAYER | [FACULTATIF] Nom de la couche WMTS de base | Chaine de caractères
PREVARISC_PLUGIN_GEOCONCEPT_APP_ID | [FACULTATIF] App id si couches derrière authentification | Chaine de caractères
PREVARISC_PLUGIN_GEOCONCEPT_TOKEN | [FACULTATIF] Token si couches derrière authentification| Chaine de caractères
PREVARISC_PLUGIN_GEOCONCEPT_GEOCODER | [FACULTATIF] URL du géocoder | Chaine de caractères

* Taper :```/etc/init.d/apache2 restart```

## Etape 3 : installation de Prevarisc ##

* Depuis le navigateur Iceweasel, télécharger le fichier compressé de prevarisc depuis “https://github.com/SDIS62/prevarisc” dans votre dossier “Downloads”
* Taper : ```mv /home/$USER/Downloads/prevarisc-master.zip /var/www/``` ou $USER est votre nom d’utilisateur de la session
* Taper : ```7z x prevarisc-master.zip```
* Taper : ```mv prevarisc-master prevarisc```
* Taper : ```chmod 755 prevarisc```
* Taper : ```cd prevarisc```
* Taper : ```curl https://getcomposer.org/installer | php```
* Taper : ```php composer.phar install --prefer-source```
* Taper : ```chown –R www-data:www-data *```
* Taper : ```chmod –R 555 *```
* Taper : ```chmod –R 755 public/```

## Etape 4 : installation de la base de données ##

* Ouvrir MySQL WorkBench dans le menu Development
* Cliquer sur New Server Instance, cliquer sur Next, saisir le mot de passe “root”, choisir MySQL installation type “Debian”, Continue, Finish
* Cliquer sur Manage Import/Export puis sur Data Import/Restore
* Mot de passe : “root”
* Choisir Import from Self-Contained file et sélectionner le fichier prevarisc.sql dans le dossier prevarisc/docs/db
* Cliquer sur “Start Import” et fermer l’onglet quand l’opération est terminée
* Fermer MySQL Workbench

## Etape 5 : connexion d’un client Firefox ##

* Ouvrir le gestionnaire de machine Oracle VM VirtualBox
* Faire un clic droit sur Debian et choisir Configuration …
* Cliquer sur Réseau et choisir un mode d’accès réseau “Accès par pont”
* Ouvrir une invite de commande sur Debian, taper : ```su``` et le mot de passe, puis ```ifconfig``` et noter l’adresse IP eth0 (inet addr)
* Fermer l’invite de commande
* Sur le poste Windows, ouvrir le fichier C:\windows\system32\drivers\etc\hosts et ajouter la ligne avec l’adresse IP du serveur Debian suivi de “prevarisc.sdis??.fr” (ou sinon, faire un enregistrement DNS)
* Ouvrir Firefox et saisir http://prevarisc.sdis??.fr
* A ce point vous devez être capable d'accéder à Prevarisc ! Le premier compte utilisateur est ```root```, mot de passe ```root``` (à désactiver le plus rapidement possible pour des raisons de sécurité).


## Etape 6 (optionnelle) : installation d'un cache mysql (php>5.4) ##

Pour les installations avec php > 5.4, APC n'est plus supporté. Il faut installer un cache alternatif, mysql par exemple :
* download du module php memcache
* Taper : ```tar -xvzf memcache-2.2.7.tgz```
* Taper : ```cd memcache-2.2.7```
* Taper : ```phpize```
* Taper : ```./configure```
* Taper : ```make```
* Taper : ```sudo make install```
* Vérifier que memcache est bien installé ```php -m | grep memcache``` sinon ajout de : ```extension=memcache.so``` dans le php.ini
* Jouer le sql d'activation du cache, taper : ```mysql -u root -p prevarisc < sql/init/enable_mysql_cache.sql```
* Taper : ```sudo /etc/init.d/mysql restart```
* Modifier le vhost apache pour modifier les variables PREVARISC_CACHE_ADAPTER en "Cache_MySQLMemcached" voir PREVARISC_CACHE_HOST si le backend est installé sur une machine distante.
* Taper : ```sudo /etc/init.d/httpd restart```

# Mise à jour de la base de données de Prevarisc #

Depuis un PC windows, installer le logiciel [MySQL WorkBench](http://www.mysql.fr/products/workbench/).
Vous pouvez maintenant ouvrir le fichier "docs/MCD Prevarisc avec Mysql Workbench.mwb".

> Pourquoi utiliser ce logiciel ?
> Cela nous permettra de simplifier les mises à jour de la base via une simple synchronisation.

Ce fichier représente le MCD complet de Prevarisc. Pour synchoniser votre serveur de base de données avec ce MCD, vous devez :
* Menu Database > Synchronize model
* Configurer la connexion vers votre base de données
* Selectionner "prevarisc" et "next" jusqu'a l'intallation de la base de données

Pour ajouter les valeurs par défauts :
* Menu Database > Forward engineer
* Configurer la connexion vers votre base de données
* Cocher "Generate INSERT statements for tables"
* "next" jusqu'a l'insertion des données
