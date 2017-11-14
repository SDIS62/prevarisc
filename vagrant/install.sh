# ------------------------------
# - INSTALLATION DE PREVARISC
# ------------------------------

echo && echo "Installation prevarisc"

# Execution en root
if ( ! (whoami | grep root > /dev/null) ); then
  echo && echo "not root : sudo su"
  sudo su
fi

# Assignation de valeurs par défaut si nécessaire
export PREVARISC_DB_PASSWORD=${PREVARISC_DB_PASSWORD:=root}
export PREVARISC_APPROOTPASSWORD=${PREVARISC_APPROOTPASSWORD:=root}
export PREVARISC_SERVERNAME=${PREVARISC_SERVERNAME:=prevarisc}
export PREVARISC_PLUGIN_IGNKEY=${PREVARISC_PLUGIN_IGNKEY:=A_RENSEIGNER}
export PREVARISC_SECURITY_SALT=${PREVARISC_SECURITY_SALT:=`date | md5sum | awk '{ print $1 }'`}
export PREVARISC_ZIP_BRANCH=${PREVARISC_ZIP_BRANCH:=https://github.com/SDIS62/prevarisc/archive/2.x.zip}

if [ $PREVARISC_PLUGIN_IGNKEY = "A_RENSEIGNER" ]; then
    echo && echo "WARNING : PREVARISC_PLUGIN_IGNKEY dans /etc/apache2/sites-available/prevarisc"
fi


# ------------------------------
# - Prérequis
# ----------

echo && echo "Mise à jour des paquets"
aptitude -y update
aptitude -y upgrade

echo && echo "Configuration et installation des nouveaux paquets"

# Définir le mot de passe root MySQL
echo "mysql-server mysql-server/root_password password ${PREVARISC_DB_PASSWORD}" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password ${PREVARISC_DB_PASSWORD}" | debconf-set-selections

# Installer les dépendances
apt-get -y install mysql-server mysql-workbench apache2 php5 php5-gd php5-ldap php5-mysql php-apc curl git p7zip-full


# ------------------------------
# - Serveur web
# ----------

echo && echo "Coniguration du serveur apache"

a2enmod rewrite
a2enmod expires
a2enmod include

mkdir -p /var/www/prevarisc/public
envsubst << "EOF" > /etc/apache2/sites-available/prevarisc
<VirtualHost *:80>

    ServerName ${PREVARISC_SERVERNAME}
    DocumentRoot /var/www/prevarisc/public

    #SetEnv [CLE DE CONFIGURATION] [VALEUR]
    #SetEnv PREVARISC_APPLICATION_PATH /var/www/prevarisc/application

    SetEnv PREVARISC_BRANCH 2.x
    #SetEnv PREVARISC_REVISION

    SetEnv PREVARISC_DB_ADAPTER Pdo_Mysql
    SetEnv PREVARISC_DB_CHARSET utf8
    SetEnv PREVARISC_DB_HOST localhost
    Setenv PREVARISC_DB_USERNAME root
    SetEnv PREVARISC_DB_PASSWORD ${PREVARISC_DB_PASSWORD}
    SetEnv PREVARISC_DB_DBNAME prevarisc
    SetEnv PREVARISC_CACHE_LIFETIME 3600
    # Clé pour le nom "prevarisc" 
    SetEnv PREVARISC_PLUGIN_IGNKEY ${PREVARISC_PLUGIN_IGNKEY}
    # Clé de sécurité pour la hashage des mots de passe
    SetEnv PREVARISC_SECURITY_SALT ${PREVARISC_SECURITY_SALT}

    <Directory /var/www/prevarisc/public>
        DirectoryIndex index.php
        AllowOverride all
        Order allow,deny
        Allow from all
    </Directory>

    RewriteEngine off
    <Location />
        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} -s [OR]
        RewriteCond %{REQUEST_FILENAME} -l [OR]
        RewriteCond %{REQUEST_FILENAME} -d
        RewriteRule ^.*$ - [NC,L]
        RewriteRule ^.*$ /index.php [NC,L]
    </Location>

</VirtualHost>
EOF

# (Dés)activation des sites Apache
a2dissite default
a2ensite prevarisc

/etc/init.d/apache2 restart


# ------------------------------
# - Application Prevarisc
# ----------

echo && echo "Installation de l'application prevarisc"

cd /var/www
echo "Téléchargement sources..." && wget -q ${PREVARISC_ZIP_BRANCH} --output-document=prevarisc.zip
7z x prevarisc.zip -oprevarisctmp
mv -f prevarisctmp/`ls prevarisctmp`/* prevarisc/
rm -rf prevarisctmp
rm -f prevarisc.zip

chmod 755 prevarisc
cd prevarisc
curl https://getcomposer.org/installer | php

# Si besoin : augmenter le timeout dans /var/www/prevarisc/composer.json :
#nano /var/www/prevarisc/composer.json
#"config": {
#    "process-timeout" : 1000
#},

#php composer.phar install --prefer-source
php composer.phar install --prefer-dist
chown -R www-data:www-data *
chmod -R 555 *
chmod -R 755 public/


# ------------------------------
# - Base de données
# ----------

echo && echo "Création de la base de données"

mysql -hlocalhost -uroot -p${PREVARISC_DB_PASSWORD} < /var/www/prevarisc/sql/init/prevarisc.sql

# Ajout des droits à l'utilisateur root :
cat << "EOF" > /root/acces.sql
begin;
insert into groupe(LIBELLE_GROUPE, DESC_GROUPE) values ('Tous les droits', 'Tous les droits');
insert into `groupe-privileges`(ID_GROUPE, id_privilege) SELECT (select ID_GROUPE from groupe where libelle_groupe='Tous les droits'), id_privilege FROM privileges;
update utilisateur set ID_GROUPE=(select ID_GROUPE from groupe where libelle_groupe='Tous les droits') where USERNAME_UTILISATEUR = 'root';
commit;
EOF

mysql -hlocalhost -uroot -p${PREVARISC_DB_PASSWORD} prevarisc < /root/acces.sql

# Définition du mot de passe de l'utilisateur applicatif root
mysql -uroot -p${PREVARISC_DB_PASSWORD} prevarisc --execute="update utilisateur set PASSWD_UTILISATEUR = md5(CONCAT(USERNAME_UTILISATEUR, '${PREVARISC_SECURITY_SALT}', '${PREVARISC_APPROOTPASSWORD}')) where USERNAME_UTILISATEUR = 'root';" 


# ------------------------------
# - Redémarrage Apache final
# ----------

service apache2 restart

