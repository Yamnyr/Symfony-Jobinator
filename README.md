# Jobinator

Les consignes sont disponibles dans le fichier [CONSIGNES.md](CONSIGNES.md) à la racine du projet.

## Installation

Installation des dépendances PHP avec composer :

```shell
composer install
```

Création d'un fichier `.env.local` à partir du fichier `.env` :

```shell
cp .env .env.local
```

Puis modifiez les variables d'environnement du fichier `.env.local` selon votre environnement local.

Mise en place de la base de données :

```shell
composer db
```

## Développement

Lancement du serveur de développement :

```shell
symfony serve
```

Exécuter les tests :

```shell
composer test
```

Corriger les erreurs de style de code avec PHP-CS-Fixer :

```shell
composer cs
```
