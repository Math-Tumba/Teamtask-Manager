# Convention de codage

Ce document définit les conventions de nommage et de structure à respecter afin de maintenir une cohérence dans le code du projet.

Les règles écrites dans ce document priment sur celles présentes dans les liens.

# Architecture

# Langages

## PHP/Symfony
### Structure
[Symfony coding standards (structure)](https://symfony.com/doc/current/contributing/code/standards.html#structure)<br>

- Un argument par ligne dans les méthodes des classes Controller, sauf s'il en existe qu'un seul.
- trois lignes d'espace entre chaque méthode.

### Nommage
[Symfony coding standards (nommage)](https://symfony.com/doc/current/contributing/code/standards.html#structure)*

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