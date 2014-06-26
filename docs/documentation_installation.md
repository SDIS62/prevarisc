# Documentation d'installation #

## Etape 1 : installation d'un serveur web Debian ##

Deux options s'offre à vous :
* soit une installation définitive sur un serveur dédié (voir étape 1a)
* soit une installation de test sur un poste de travail (ordinateur de bureau ou portable) (voir étape 1b)

### Etape 1a : installation d'un serveur Debian ###

* Télécharger la version stable de Debian (classiquement version i386 avec interface xfce)
* Installer le serveur de préférence en anglais

### Etape 1b : installation d'un serveur Debian sur Oracle VM VirtualBox ###

* Télécharger Oracle VM VirtualBox et son pack d'extension (https://www.virtualbox.org/wiki/Downloads)
* Télécharger la version stable de Debian (classiquement version i386 avec interface xfce)
* Installer Oracle VM VirtualBox et exécuter le programme
* Aller dans le menu Fichiers->Paramètres puis Extension et ajouter l’Extension Pack
* Dans Oracle VM VirtualBox, cliquer sur Nouvelle, saisir un nom de machine (ex : Debian) et cliquer plusieurs fois sur “Suivant”. Votre machine apparait “Eteinte” dans la partie gauche.
* Effectuer un clic droit sur Debian, choisir Démarrer, cliquer sur le dossier avec une flèche verte, sélectionner le fichier debian-X.X.X-i386-xfce-CD-1.iso et cliquer sur “Démarrer”

## Etape 2 : installation du serveur web ##

* Ouvrir une invite de commande et taper “su” puis le mot de passe administrateur
* Taper : ```aptitude install apache2 mysql-server php5 php5-gd php5-ldap php5-mysql php-apc mysql-workbench curl git p7zip-full zendframework zendframework-bin```
* A la demande, faire un clic droit sur le CD en bas pour remonter l’image debian-X.X.X-i386-xfce-CD-1.iso et appuyer sur “Enter”
* Saisir le mot de passe “root” pour MySQL
* Taper : ```a2enmod rewrite```
* Taper : ```a2enmod expires```
* Taper : ```a2enmod include```
* Taper : ```vi /etc/apache2/apache2.conf```, insérer à la fin le code suivant :
```
<VirtualHost *:80>
    ServerName prevarisc.sdis??.fr
    DocumentRoot /var/www/prevarisc/public
    SetEnv APPLICATION_ENV “production”
    <Directory /var/www/prevarisc/public>
        DirectoryIndex index.php
        AllowOverride all
        Order allow,deny
        Allow from all
    </Directory>
</VirtualHost>
```
où ?? est le numéro de département et enregistrer
* Taper :```/etc/init.d/apache2 restart```

## Etape 3 : installation de Prevarisc ##

* Depuis le navigateur Iceweasel, télécharger le fichier compressé de prevarisc depuis “https://github.com/SDIS62/prevarisc” dans votre dossier “Downloads”
* Taper : ```mv /home/$USER/Downloads/prevarisc-master.zip /var/www/``` ou $USER est votre nom d’utilisateurde la session
* Taper : ```7z x prevarisc-master.zip```
* Taper : ```mv prevarisc-master prevarisc```
* Taper : ```chmod 755 prevarisc```
* Taper : ```cd prevarisc```
* Taper : ```curl https://getcomposer.org/installer | php```
* Taper : ```php composer.phar install```
* Taper : ```vi ./application/configs/secret.ini``` et insérer les lignes suivantes :
```
[general]
ldap.host =
ldap.port =
ldap.username =
ldap.password =
ldap.baseDn =
security.salt = [7aec3ab8e8d025c19e8fc8b6e0d75227]

[production]
resources.db.params.host = localhost
resources.db.params.username = root
resources.db.params.password = root
resources.db.params.dbname = prevarisc
```
* Taper : ```mv ./docs/db/MCD\ Prevarisc\ avec\ Mysql\ Workbench.mwb /home/$USER/Downloads``` où $USER est le nom d’utilisateur de la session
* Taper : ```chown –R wwwh-data:www-data *```
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
* Sur le poste Windows, ouvrir le fichier C:\windows\system32\drivers\etc\hosts et ajouter la ligne avec l’adresse IP du serveur Debian suivi de “prevarisc.sdis??.fr”
* Ouvrir Firefox et saisir http://prevarisc.sdis??.fr
* A ce point vous devez être capable d'accéder à Prevarisc ! Le premier compte utilisateur est ```root```, mot de passe ```root``` (à désactiver le plus rapidement possible pour des raisons de sécurité).

# Mise à jour de la base de données de Prevarisc #

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
