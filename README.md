![Prevarisc](https://raw.github.com/SDIS62/prevarisc/master/public/images/logo_prevarisc_big.jpg)

# [SDIS 62 : Prevarisc](http://sdis62.github.com/prevarisc/) - Application web de gestion du service prévention

## Présentation

Gérez plus simplement votre service prévention!
Prevarisc est un logiciel national open-source gérant l'ensemble de l'activité du service prévention au sein d'un SDIS.

* Géo-localisation : Les établissements sont géolocalisés automatiquement à la saisie et sont visibles sur une carte GeoPortail.
* LDAP : Prevarisc permet d'interagir  avec votre annuaire d'entreprise. Plus besoin de créer de double compte, utilisez simplement l'existant !
* Génération automatique de documents : En un clic, générez des documents d'études et/ou de visites.
* Gestion des commissions : Consultez les calendriers des commissions, gérez le passage des dossiers en salle.
* Extractions et statistiques : Exploitez la base de données de prevarisc pour sortir des statistiques aidant notamment à remplir infosdis.
* Propulsé par la communauté : Prevarisc est en constante évolution ! Simple utilisateur ou contributeur, participez à son amélioration.

## Suivi du projet

* [Travail en cours / jalons](https://github.com/SDIS62/prevarisc/issues/milestones) ;
* [Suivi des bugs et corrections](https://github.com/SDIS62/prevarisc/issues).

## Installation et configuration

Pour télécharger Prevarisc : [Lien vers le téléchargement de la version 1.0.0 [STABLE]](https://github.com/SDIS62/prevarisc/tree/v1.0.0) ou [Lien vers le téléchargement de la version la plus récente [INSTABLE]](https://github.com/SDIS62/prevarisc/archive/master.zip).

### Le besoin d'un serveur web

#### Installation

Prevarisc est une application web, vous devez l'héberger sur un serveur web supportant PHP >= 5.3 et MySQL. Vous pouvez donc soit :
* Héberger Prevarisc chez un tiers (OVH, Gandi etc ...) ;
* Heberger Prevarisc soi-même (voici des bon tutoriels pour installer et configurer votre serveur : [Installer un serveur](http://www.siteduzero.com/informatique/tutoriels/apprenez-a-installer-un-serveur-web-sous-debian) et [le sécuriser](http://www.siteduzero.com/informatique/tutoriels/securiser-son-serveur-linux)).

Les modules apache à activer : rewrite
Les extensions PHP à activer : ldap, gd2

#### Zend Framework

Sur votre serveur web vous devez configurer Zend framework, tout d'abord vous devez télécharger et extraire la version 1.X (1.12.3 à l'heure où j’écris ces lignes) sur http://framework.zend.com/downloads/latest. Et ensuite placer le dossier Zend dans le dossier library/ de Prevarisc (ou dans l'include_path de PHP).

#### Hôte virtuel

Pour créer votre hôte virtuel, vous avez besoin de connaître l'emplacement de votre fichier httpd.conf, et peut-être où les fichiers de configuration se trouvent les autres. Certains emplacements communs:

/etc/httpd/httpd.conf (Fedora, RHEL et autres)
/etc/apache2/httpd.conf (Debian, Ubuntu et autres)
/usr/local/zend/etc/httpd.conf (Zend Server sur des machines * nix)
C:\Program Files\Zend\Apache2\conf (Zend Server sur les machines Windows)

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

Vous devez extraire la table de prevarisc dans votre serveur de base de données. Le fichier contenant les données de prevarisc se trouve ici : [Vers le dossier extras](https://github.com/SDIS62/prevarisc/tree/master/extras).

A ce point vous devez être capable d'accéder à Prevarisc ! Le premier compte utilisateur est celui-ci : root / )8oB3AUK3E

## Guide utilisateur

A venir ! Mais ne vous inquiétez pas, le logiciel est très ergonomique ! ;-)