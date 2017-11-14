# Prevarisc - Installation automatisée

**L'installation automatisée** est inspirée de la [documentation officielle](https://sdis62.github.io/prevarisc/docs/installation-dun-serveur-prevarisc/).
## Dans une machine virtuelle
Le fichier ```Vagrantfile``` permet de créer rapidement une machine virtuelle [VirtualBox](https://www.virtualbox.org) munie de prevarisc via [Vagrant](https://www.vagrantup.com/downloads.html). Pour tester :
<pre>
cd vagrant && vagrant up
</pre>

Accès après l'installation : http://localhost:8001

## Sur un serveur
Pour installer rapidement un serveur prevarisc, on réalise l'équivalent de ce qui est réalisé par Vagrant. A savoir :

1. Partir d'un serveur Debian 7X vierge
2. Déposer le script d'installation ```install.sh```
2. Redéfinir les variables d'environnement ```PREVARISC_*``` présentes dans le fichier Vagrantfile (mots de passe, nom du serveur, clé IGN, lien vers le package prevarisc, etc.)
3. Exécuter ```install.sh```

Accès après l'installation : http://prevarisc.sdisxx.fr
