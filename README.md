![PHP](https://img.shields.io/badge/PHP-8.2-green)
![Symfony](https://img.shields.io/badge/Symfony-7.4-green)
![Docker](https://img.shields.io/badge/Docker-ready-green)

# TeamTask-Manager

Découverte de Symfony via le développement de cette application web collaborative de gestion de projets (gestion des tâches en équipe) soutenue par une API REST dont l'authentification est basée sur le système de JWT + refresh token.

## Stack
Backend :
- Symfony 7.4
- PHP 8.2
- Doctrine
- SSE (Mercure)
- JWT (LexikJWTAuthenticationBundle)
- PostgreSQL

Frontend :
- Twig
- Typescript
- SASS
- Bootstrap


## Fonctionnalités prévues et objectifs de développement

- Création d'un système d'utilisateurs
  - Inscription / Connexion :white_check_mark:
  - Modification de profil :white_check_mark:
  - Gestion de relations entre les utilisateurs :white_check_mark:
    
- Création d'une API REST pour simuler un besoin d'être consommée, par exemple, par un équivalent de TeamTask en appli mobile.
  - Mise en place d'une authentification via JWT + refresh token. :white_check_mark:
  - Documentation avec Nelmio. :white_check_mark:
    
- Mise à jour en temps réel de certaines données (tâches, demandes d'amis, ...)
  - Ajout d'un système de SSE (Mercure) :white_check_mark: 
  - Système de notifications (réception d'une demande d'ami) :white_check_mark:
    
- Création d'un système d'équipes
  - Gestion des droits des membres de l'équipe en fonction du rôle attribué par le chef d'équipe
  - Gestion des tâches (CRUD, attribution de la tâche à un ou plusieurs membres, statut, ...)
    
- Création d'un espace administrateur

- Mise en place de tests
  - Tests unitaires
  - Tests fonctionnels

- Dockerisation de l'application pour l'environnement prod (Basé sur la solution de [dunglas/symfony-docker](https://github.com/dunglas/symfony-docker)) :white_check_mark:

## Objectifs derrière la création de cette application

- Découvrir un framework populaire et moderne via de façon expérimentale, en passant par de multiples moments de refactoring causés par la découverte de features ou de meilleures solutions.
- Apprendre à créer une API et à la sécuriser (auth JWT avec refresh token)
- Expérimenter les mises à jour en temps réel avec le SSE (Mercure)
- Apprendre à déployer une application (en local via Docker)

## Déploiement en production via Docker 

### Récupération et lancement de l'application
```
git clone https://github.com/Math-Tumba/Teamtask-Manager.git
cd teamtask-manager
docker compose -f compose.yaml -f compose.prod.yaml build --pull --no-cache
docker compose -f compose.yaml -f compose.prod.yaml up --wait
```

Ouvrir un nouvel onglet avec cette URL : https://teamtask-manager.local

### Génération des clés JWT privée et publique 
Ne le faire que :
- Au premier lancement
- Si la passphrase a été modifiée
- Si le contenu de ./config/jwt a été modifié / effacé
```
docker compose exec php bin/console lexik:jwt:generate-keypair --overwrite
```

### État de l'application

Lancer les conteneurs dockers
```
docker compose -f compose.yaml -f compose.prod.yaml up --wait
```

Stopper les conteneurs dockers
```
docker compose down --remove-orphans
```

### Génération d'un environnement testable avec des utilisateurs fictifs

Lance la génération d'utilisateurs fictifs. Cette action **entraîne la regénération de la base de données**.
```
docker compose exec php bin/console foundry:load-fixtures main
```

Un utilisateur spécifique avec des relations existantes entre lui et d'autres utilisateurs fictifs est instancié.
Sur la page de connexion, se connecter avec ces identifiants :
- Nom d'utilisateur : toto
- Mot de passe : password