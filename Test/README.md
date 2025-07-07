# Projet Kiwi - Clone du design de Kiwi.com pour la section vol

Ce projet reproduit le design et les fonctionnalités de recherche de vols de Kiwi.com. Il s'agit d'une implémentation PHP qui utilise l'API Tequila de Kiwi.com pour récupérer des données réelles sur les vols.

## Structure du projet

```
projet-kiwi/
├── assets/
│   ├── css/
│   ├── js/
│   ├── fonts/
│   └── img/
├── api/
│   ├── tequila.php (classe pour interagir avec l'API Tequila)
│   └── endpoints/ (différents endpoints API: recherche, vérification, réservation, etc.)
├── config/
│   ├── database.php (configuration de la base de données)
│   └── api_config.php (configuration de l'API Tequila)
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── functions.php
├── models/
│   ├── Airport.php
│   ├── Booking.php
│   ├── Location.php
│   └── Agency.php
├── views/
│   ├── home.php (page d'accueil avec formulaire de recherche)
│   ├── search_results.php (résultats de recherche)
│   ├── flight_details.php (détails du vol)
│   ├── booking.php (page de réservation)
│   ├── payment.php (page de paiement)
│   └── confirmation.php (page de confirmation)
├── sql/
│   └── database.sql (script de création de base de données)
├── index.php (contrôleur principal)
└── README.md (documentation)
```

## Installation

1. Cloner le dépôt
2. Configurer la base de données en important le fichier SQL dans `sql/database.sql`
3. Configurer les informations de connexion à la base de données dans `config/database.php`
4. Configurer les clés API Tequila dans `config/api_config.php`
5. Lancer le serveur web

## Configuration requise

- PHP 7.4+
- MySQL 5.7+
- Extension PHP PDO
- Extension PHP cURL

## API Tequila

Ce projet utilise l'API Tequila de Kiwi.com pour rechercher et réserver des vols. Vous devez vous inscrire sur le portail développeur de Kiwi.com pour obtenir une clé API et configurer `config/api_config.php` avec vos identifiants.

## Licence

Ce projet est à des fins éducatives uniquement. Le design et le nom Kiwi.com sont la propriété de Kiwi.com.
