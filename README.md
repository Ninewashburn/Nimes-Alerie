# La Nîmes'Alerie

Application e-commerce avec forum communautaire, construite avec **Symfony 7.2** (API backend) et **Angular 19** (frontend SPA).

## Stack technique

- **Backend** : Symfony 7.2, API Platform 3.4, Doctrine ORM 3.0
- **Frontend** : Angular 19 (standalone components, signals, lazy-loaded routes)
- **Auth** : JWT (Lexik JWT Authentication)
- **Base de données** : MySQL 8.0
- **PHP** : 8.2+ (8.3 recommandé)
- **Qualité** : PHPStan (level 6), PHP-CS-Fixer, PHPUnit 10

## Installation avec Docker (recommandé)

### Prérequis
- Docker et Docker Compose

### Lancement

```bash
git clone https://github.com/Ninewashburn/Nimes-Alerie.git
cd Nimes-Alerie
cp .env.example .env
# Modifiez .env avec vos secrets (APP_SECRET, JWT_PASSPHRASE)

docker compose up -d
```

Services disponibles :
- **API Symfony** : http://localhost:8000
- **Frontend Angular** : http://localhost:4200

### Initialisation de la base de données

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console hautelook:fixtures:load  # Données de test
```

### Génération des clés JWT

```bash
docker compose exec php php bin/console lexik:jwt:generate-keypair
```

## Installation locale (sans Docker)

### Prérequis
- PHP >= 8.2 avec extensions intl, pdo_mysql, zip
- Composer 2.x
- Node.js >= 18
- MySQL 8.0+

### Backend

```bash
composer install
cp .env.example .env
# Configurez DATABASE_URL dans .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
symfony serve
```

### Frontend Angular

```bash
cd frontend
npm install
npm start
# Accessible sur http://localhost:4200
```

## Architecture

```
├── src/                    # Backend Symfony
│   ├── Controller/         # Contrôleurs API et web
│   ├── Entity/             # Entités Doctrine (16 entités)
│   ├── Service/            # Couche service (Product, Cart, User, Forum)
│   ├── Security/           # Authenticators
│   └── Trait/              # TimestampableTrait
├── frontend/               # Frontend Angular 19
│   └── src/
│       ├── app/core/       # Services, guards, interceptors, modèles
│       ├── app/features/   # Composants par page (lazy-loaded)
│       └── app/shared/     # Composants réutilisables
├── config/                 # Configuration Symfony
├── migrations/             # Migrations Doctrine
├── docker/                 # Configuration Docker (nginx)
├── Dockerfile              # Image PHP 8.3-fpm
└── docker-compose.yml      # Stack complète (PHP, Nginx, MySQL, Node)
```

## Sécurité

Les fichiers suivants ne doivent **jamais** être commités :
- `.env` (secrets de production)
- `.env.local` (configuration locale)
- `config/jwt/*.pem` (clés JWT)

### Génération de secrets

```bash
# APP_SECRET
php -r "echo bin2hex(random_bytes(16));"

# JWT_PASSPHRASE
php -r "echo bin2hex(random_bytes(32));"
```

## Commandes utiles

```bash
# Cache
php bin/console cache:clear

# Qualité du code
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix --dry-run

# Tests
vendor/bin/phpunit

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Licence

Propriétaire - Tous droits réservés
