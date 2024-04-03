# TP Planning

## Utilisation 
### Lancer l'application (Quelques minutes au premier lancement)
> docker compose up -d

### Accéder à l'application
> http://localhost

Contenu : Un cas d'exemple est configuré
Pour le modifier, éditer le fichier **public/index.php** en choisissant les variables suivantes :
> $month : Contient le mois souhaité (Format entier de 1 à 12)
> 
> $year : Contient l'année souhaitée (Format entier)
> 
> $input : Tableau des jours travaillés
> 
>> *Note*: Ne nécessite pas la présence de tout les jours du mois
> 
>> Pour chaque jour, renseigner **'date'** au format (JJ/MM) et **'duration'** au format entier positif

---
## Travail réalisé

### Temps requis

Environ 30 minutes : Mise en place d'un environnement Dockerisé simple

Environ 45 minutes : Création du squelette de l'application et mise en place des tests

Environ 1h : Réalisation de l'algorithme de calcul complet

Environ 45 minutes : Mise en place d'une page rudimentaire & Rédaction de cette documentation

_Temps supplémentaire : Environ 30 minutes : Branche permettant l'automatisation des jours fériés (voir ci-après dans 'Contraintes techniques')_

**Temps total : 3h + 30min**


### Contraintes fonctionnelles requises

- Le mois concerné est le mois de mai 2024.


- Un jour férié est considéré comme travaillé si le nombre d'heures dépasse 4 sinon les heures travaillées
sur la journée sont considérées comme du temps de travail ordinaire.


- Un dimanche est considéré comme travaillé si le nombre d'heures dépasse 3 sinon les heures travaillées
sur la journée sont considérées comme du temps de travail ordinaire.


- En cas de dimanche férié, les heures de fériés sont prioritaires sur les heures de dimanche.


### Contraintes fonctionnelles supplémentaires auto-imposées
- L'algorithme de calcul supporte que tous les jours ne soient pas renseignés en entrée.


- L'algorithme de calcul est capable de trouver les dimanches de n'importe quel mois/année tout seul.


- L'algorithme de calcul ignore les dates/durées invalides.


- L'algorithme de calcul limite les heures par jour à 8 maximum.


- **Pour rendre les cas de tests plus intéressants, le dimanche 26 mai 2024 est considéré ferié.**

### Contraintes techniques auto-imposées

- Il doit être simple de pouvoir utiliser l'algorithme sur d'autres mois que mai 2024
> Le mois/année désiré peut être passé en paramètre de la fonction. Il faudra par contre manuellement renseigner 
> pour chaque mois utilisé ses jours fériés selon le format choisi
> 
> Note : Une branche de feature additionnelle permet d'automatiser cette opération via une librairie externe
> 
> _Nom de la branche : feature/automatic-holidays_

- Il doit être simple de modifier les limites d'heures par jour/heures min pour dimanches/jours fériés
> Mise en place de constantes

- Le code produit doit être testé unitairement
> Mise en place de tests phpunit

- Le code produit doit réussir l'analyse statique de phpstan au niveau le plus rigoureux


- Le style du code doit être validé par php-cs-fixer

### Evolutions futures possibles

- Découpage du code : L'algorithme principal peut être scindé en 3 parties : 
 
    1/ Traitement de l'input : Nettoyage et reformatage selon l'entrée, permettra d'envisager par exemple un import depuis fichier
    
    2/ Calculs basés sur les règles métier

    3/ Traitement de l'output : Formattage des résultats


- Support de plusieurs créneau de travail par jour : Actuellement, si deux blocs d'heures correspondant au même jour sont reçus, on les additionne

  - On pourrait mettre en place un système qui saurait gérer ces cas (Limitation des heures max, ne compter qu'un seul jour)

### Limitations

- Prise en compte de différents formats de date : Actuellement, point faible du code réalisé, qui ne gère qu'un format bien défini (dd/mm)
  - La gestion des erreurs/exceptions n'est pas non plus réalisée si les entrées ne respectent pas les formats attendus

--- 
### Commandes utiles en DEV

#### Lancer une commande composer
> docker compose run --rm php composer ...

#### Execution des tests PHPUnit
> docker compose run --rm php ./vendor/bin/phpunit tests/

### Lancer une analyse PHP-CS-FIXER
> docker compose run --rm php ./vendor/bin/php-cs-fixer check src

### Lancer une analyse PHPSTAN
> docker compose run --rm php ./vendor/bin/phpstan analyse src tests --level 9
