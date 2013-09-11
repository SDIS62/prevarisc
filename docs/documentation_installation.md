# [SDIS 62 : Prevarisc](http://sdis62.github.com/prevarisc/) - Application web de gestion du service prévention

> Pour mettre à jour Prevarisc :
> * Vous devez tout d'abord mettre à jour la structure de votre base de données via la [nouvelle structure référente](https://github.com/SDIS62/prevarisc/raw/master/extras/prevarisc.gz)
> * Puis mettre à jour les fichiers sources
>
> Ps : La compatibilité est assurée pour toutes les versions 1.*.*. Il est important de faire une sauvegarde de la base de données et des fichiers avant toute opération de mise à jour.

## Installation et configuration

Pour télécharger Prevarisc : [Lien vers le téléchargement de la version 1.0.0 [STABLE]](https://github.com/SDIS62/prevarisc/tree/v1.0.0) ou [Lien vers le téléchargement de la version la plus récente [INSTABLE]](https://github.com/SDIS62/prevarisc/archive/master.zip).

### Le besoin d'un serveur web

#### Installation

Prevarisc est une application web, vous devez l'héberger sur un serveur web supportant PHP >= 5.3 et MySQL. Vous pouvez donc soit :
* Héberger Prevarisc chez un tiers (OVH, Gandi etc ...) ;
* Heberger Prevarisc soi-même (voici des bon tutoriels pour installer et configurer votre serveur : [Installer un serveur](http://www.siteduzero.com/informatique/tutoriels/apprenez-a-installer-un-serveur-web-sous-debian) et [le sécuriser](http://www.siteduzero.com/informatique/tutoriels/securiser-son-serveur-linux)).

Les modules apache à activer : rewrite, deflate, expires ;
Les extensions PHP à activer : ldap, gd2, exif

#### Zend Framework

Sur votre serveur web vous devez configurer Zend framework, tout d'abord vous devez télécharger et extraire la version 1.X (1.12.3 à l'heure où j’écris ces lignes) sur http://framework.zend.com/downloads/latest. Et ensuite placer le dossier Zend dans le dossier library/ de Prevarisc (ou dans l'include_path de PHP).

#### Hôte virtuel

Pour créer votre hôte virtuel, vous avez besoin de connaître l'emplacement de votre fichier httpd.conf, et peut-être où les fichiers de configuration se trouvent les autres. Certains emplacements communs:

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

### Configurer votre serveur pour donner l'accès à Prevarisc

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

Vous devez extraire la table de prevarisc dans votre serveur de base de données. Le fichier contenant les données de prevarisc se trouve ici : [Vers le fichier de la base de données](https://github.com/SDIS62/prevarisc/raw/master/extras/prevarisc.gz).

Enfin, pour permettre à Prevarisc d'écrire dans les dossiers "documents", "pièces jointes" et autres, vous devez spécifier les droits des fichiers comme ceci :

```
(en se plaçant dans le dossier "prevarisc/")

$ chown –R www-data:www-data * 
$ chmod –R 555 *
$ chmod –R 755 public/
```

A ce point vous devez être capable d'accéder à Prevarisc ! Le premier compte utilisateur est celui-ci : root / root (à désactiver le plus rapidement possible pour des raisons de sécurité).