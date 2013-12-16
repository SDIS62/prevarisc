# Documentation d'installation

## Installation et configuration

Pour télécharger Prevarisc : [Lien vers le téléchargement de la version 1.2.0 [STABLE]](https://github.com/SDIS62/prevarisc/tree/v1.2.0) ou [Lien vers le téléchargement de la version la plus récente [INSTABLE]](https://github.com/SDIS62/prevarisc/archive/master.zip).

### Le besoin d'un serveur web

#### Installation

Prevarisc est une application web, vous devez l'héberger sur un serveur web supportant PHP >= 5.3 et MySQL. Vous pouvez donc soit :
* Héberger Prevarisc chez un tiers (OVH, Gandi etc ...) ;
* Heberger Prevarisc soi-même (voici des bon tutoriels pour installer et configurer votre serveur : [Installer un serveur](http://www.siteduzero.com/informatique/tutoriels/apprenez-a-installer-un-serveur-web-sous-debian) et [le sécuriser](http://www.siteduzero.com/informatique/tutoriels/securiser-son-serveur-linux)).

Les modules apache à activer : rewrite, deflate, expires ;
Les extensions PHP à activer : ldap, gd2, exif

#### Hôte virtuel

Pour créer votre hôte virtuel, vous avez besoin de connaître l'emplacement de votre fichier httpd.conf. Certains emplacements communs:

* /etc/httpd/httpd.conf (Fedora, RHEL et autres)
* /etc/apache2/httpd.conf (Debian, Ubuntu et autres)
* /usr/local/zend/etc/httpd.conf (Zend Server sur des machines * nix)
* C:\Program Files\Zend\Apache2\conf (Zend Server sur les machines Windows)

Dans votre fichier httpd.conf (ou httpd-vhosts.conf sur certains systèmes), vous devrez faire deux choses. Tout d'abord, vous devez vous assurer que le NameVirtualHost est définie, typiquement, vous affectez-lui une valeur de "*: 80". Deuxièmement, définir un hôte virtuel:
```
<VirtualHost *:80>
    ServerName prevarisc.sdisXX.fr
    DocumentRoot /path/to/prevarisc/public
 
    SetEnv APPLICATION_ENV "production"
 
    <Directory /path/to/prevarisc/public>
        DirectoryIndex index.php
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```

La configuration DocumentRoot doit spécifier le sous-répertoire "public" de prevarisc.

Enfin, vous aurez besoin d'ajouter une entrée dans votre fichier hosts (ou votre DNS) correspondant à la valeur que vous placez dans la directive ServerName. Sur * nix-like, le fichier se trouve  /etc/hosts, sous Windows, vous pourrez le trouver généralement dans C:\WINDOWS\system32\drivers\ec Quel que soit le système, l'entrée ressemble à ce qui suit:
```
127.0.0.1 prevarisc.sdisXX.fr
```

### Configuration de Prevarisc

#### Dépendances

Dans le dossier de prevarisc, executer les commandes suivantes :
```
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

Cette manipulation installe les dépendances automatiquement.

#### Accès à la base de données

Vous devez configurer Prevarisc afin qu'il ait accès à la base de données (entre autres). La configuration par défaut est placé dans application/configs/application.ini, ce fichier contient quelques directives de base pour la configuration de votre environnement. Voici comment le configurer :
```
; application/configs/application.ini

(...)
resources.db.params.host = #HOST (ex : localhost)#
resources.db.params.username = #USERNAME (ex: root)#
resources.db.params.password = #PASSWORD (le mot de passe d'accès à la base de données)#
resources.db.params.dbname = #DBNAME (la table contenant les données de prevarisc, ex : prevarisc par défaut) #
(...)
```

#### Installer la base de données de Prevarisc

Depuis un PC windows, installer le logiciel [MySQL WorkBench](http://www.mysql.fr/products/workbench/).
Vous pouvez maintenant ouvrir le fichier "docs/MCD Prevarisc avec Mysql Workbench.mwb".

> Pourquoi utiliser ce logiciel ?
> Cela nous permettra de simplifier les mises à jour de la base via une simple synchonisation.

Ce fichier représente le MCD complet de Prevarisc. Pour synchoniser votre serveur de base de données avec ce MCD, vous devez :
* Menu Database > Synchronize model
* Configurer la connexion vers votre base de données
* Selectionner "prevarisc" et "next" jusqu'a l'intallation de la base de données

Pour ajouter les valeurs par défauts :
* Menu Database > Forward engineer
* Configurer la connexion vers votre base de données
* Cocher "Generate INSERT statements for tables"
* "next" jusqu'a l'insertion des données

#### Accès aux documents

Enfin, pour permettre à Prevarisc d'écrire dans les dossiers "documents", "pièces jointes" et autres, vous devez spécifier les droits des fichiers comme ceci :

```
(en se plaçant dans le dossier "prevarisc/")

$ chown –R www-data:www-data * 
$ chmod –R 555 *
$ chmod –R 755 public/
```

A ce point vous devez être capable d'accéder à Prevarisc ! Le premier compte utilisateur est celui-ci : root / root (à désactiver le plus rapidement possible pour des raisons de sécurité).
