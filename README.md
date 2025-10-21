# teamtask-manager

Découverte de Symfony via le développement de cette application web collaborative de gestion de projets (gestion des tâches en équipe) soutenue par une API REST dont l'authentification est basée sur le système de JWT + refresh token.

## Fonctionnalités prévues et objectifs de développement

- Création d'un système d'utilisateurs
  - Inscription / Connexion :white_check_mark:
  - Modification de profil :white_check_mark:
  - Gestion de relations entre les utilisateurs :white_check_mark:
    
- Création d'une API REST pour simuler un besoin d'être consommée, par exemple, par un équivalent de TeamTask en appli mobile.
  - Mise en place d'une authentification via JWT + refresh token. :white_check_mark:
  - Documentation avec Nelmio. :white_check_mark:
    
- Mise à jour en temps réel de certaines données (tâches, demandes d'amis, ...)
  - Configuration de WebSockets
  - Système de notifications (réception d'une demande d'ami)
    
- Création d'un système d'équipes
  - Gestion des droits des membres de l'équipe en fonction du rôle attribué par le chef d'équipe
  - Gestion des tâches (CRUD, attribution de la tâche à un ou plusieurs membres, statut, ...)
    
- Création d'un espace administrateur

- Mise en place de tests
  - Tests unitaires
  - Tests fonctionnels

- Dockerisation de l'application

## Configuration

### À venir
