# Convention de codage

Ce document définit les conventions de nommage et de structure à respecter afin de maintenir une cohérence dans le code du projet.

Les règles écrites dans ce document priment sur celles présentes dans les liens.

# Architecture

L'application suit une architecture par couches. Toutes les actions front-end utilisant des requêtes AJAX altérant les données sauvegardées en BDD passent obligatoirement par l'API.
Elle se découpe comme suit :

### Controllers :
Le dossier contient deux sous-dossiers : 
- API : contient toutes les routes liées aux actions API, toutes documentées via Nelmio.
- Web : contient toutes les routes liées aux pages web. Les formulaires présents sur les pages d'édition sont protégés par un validator. Chaque route est liée à un template Twig.

Dans ces deux dossiers, toutes les opérations de récupération ou d'altération des données doivent utiliser les méthodes présentes dans les services dédiées.

### Services : 
Les services incluent les actions sur les données ainsi que les utilitaires réutilisables au sein de l'application. Ils communiquent avec les repositories et les entités si besoin, ils constituent donc la passerelle entre PHP et l'ORM. Les services comprennent les méthodes levant les potentielles erreurs HTTP qui, si elles surviennent, sont capturées et renvoyées au client grâce à un exceptionListener (Apparition d'un message flash côté web, détail de l'erreur en json côté API).<br>
Les méthodes des classes services sont communes aux controllers web et API.

### Repositories :
Les repositories contiennent la logique de requêtage, les requêtes sont écrites suivant la syntaxe du queryBuilder de Doctrine.

### Entities :
Les classes composant les entités représentent les tables de la BDD. Ces entités sont mappées via Doctrine, permettant d'utiliser les données directement sous la forme d'objets.

# Langages

## PHP/Symfony
### Structure
[Symfony coding standards (structure)](https://symfony.com/doc/current/contributing/code/standards.html#structure)<br>

- Au sein des méthodes des classes Controller : un argument par ligne, sauf s'il en existe qu'un seul.
- Trois lignes d'espace entre chaque méthode.

### Nommage
[Symfony coding standards (nommage)](https://symfony.com/doc/current/contributing/code/standards.html#naming-conventions)

### Documentation
- Pas de paramètre déjà explicite dans les blocs.

# Annexes 

## php-cs-fixer

Voici les configurations à mettre en place pour php-cs-fixer : 

```json
"php-cs-fixer.rules": {
    "@Symfony": true,
    "class_attributes_separation": false,
    "no_extra_blank_lines": {
        "tokens": [
            "break", "case", "continue", "curly_brace_block", "parenthesis_brace_block", "default", "return", "square_brace_block", "switch", "throw", "use", "use_trait"
        ]
    }
},
```