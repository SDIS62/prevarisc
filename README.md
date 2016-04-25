# Prevarisc

[Prevarisc](http://sdis62.github.io/prevarisc/) - Application web open-source gérant l'ensemble de l'activité du service prévention au sein d'un SDIS.

![](http://sdis62.github.io/prevarisc/assets/img/screenshot.png)

## Caractéristiques

* Géo-localisation : Les établissements sont géolocalisés automatiquement à la saisie et sont visibles sur une carte GeoPortail.
* LDAP : Prevarisc permet d'interagir  avec votre annuaire d'entreprise. Plus besoin de créer de double compte, utilisez simplement l'existant !
* Génération automatique de documents : En un clic, générez des documents d'études et/ou de visites.
* Gestion des commissions : Consultez les calendriers des commissions, gérez le passage des dossiers en salle.
* Extractions et statistiques : Exploitez la base de données de prevarisc pour sortir des statistiques aidant notamment à remplir INFOSDIS.
* Propulsé par la communauté : Prevarisc est en constante évolution ! Simple utilisateur ou contributeur, participez à son amélioration.

## Guide de démarrage

Sur un serveur web :

```sh
$ git clone https://github.com/SDIS62/prevarisc
$ cd prevarisc
$ php composer.phar install --prefer-source
$ chown –R www-data:www-data *
$ chmod –R 555 *
$ chmod –R 755 public/
```
Ensuite vous pouvez créer un fichier secret.ini dans application/configs afin de configurer Prevarisc pour qu'il ait accès à la base de données (entre autres).
Vous devez créer un Virtualhost pointant vers le dossier public de Prevarisc.
Une documentation détaillée est disponible ici : [Documentation d'installation](https://github.com/SDIS62/prevarisc/blob/2.x/docs/documentation_installation.md).

## Suivi du projet

* [Travail en cours / jalons](https://github.com/SDIS62/prevarisc/issues/milestones) ;
* [Suivi des bugs et corrections](https://github.com/SDIS62/prevarisc/issues);
* [FAQ](https://github.com/SDIS62/prevarisc/issues?q=is%3Aissue+label%3Aquestion).

## Participation au projet

Voir la [documentation de contribution](https://github.com/SDIS62/prevarisc/blob/2.x/CONTRIBUTING.md).

Ps: Nous sommes régulièrement solicité sur l'ajout et/ou sur la suppression de certaines fonctionnalités. Si un changement peut avoir un impact positif sur l'ensemble des utilisateurs, nous serions heureux de l'examiner. 

Sinon, Prevarisc est une application fork-friendly et vous pouvez parfaitement maintenir une version personnalisée.

## Licence

[CeCILL-B](http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html)
