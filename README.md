# La Nimes'Alerie

Application e-commerce avec forum communautaire, construite avec **Symfony 7.2** (API backend) et **Angular 19** (frontend SPA).

Projet educatif fullstack realise dans le cadre d'une formation en reconversion professionnelle (2021-2022), modernise en 2026.

## Stack technique

- **Backend** : Symfony 7.2, API Platform 3.4, Doctrine ORM 3.0, PHP 8.2+
- **Frontend** : Angular 19 (standalone components, signals, lazy-loaded routes)
- **Auth** : JWT (Lexik JWT Authentication Bundle)
- **Base de donnees** : MySQL 8.0
- **Qualite** : PHPStan (level 6), PHP-CS-Fixer, PHPUnit 10
- **Docker** : PHP 8.3-fpm-alpine, Nginx, MySQL 8.0, Node 22

## Architecture du projet

```
Nimes-Alerie/
├── backend/                    # API Symfony 7.2
│   ├── src/
│   │   ├── Controller/         # Endpoints API (Registration, Security)
│   │   ├── Entity/             # Entites Doctrine (16 entites)
│   │   ├── EventListener/      # JWT custom claims
│   │   ├── DataFixtures/       # Processor pour fixtures
│   │   ├── Service/            # Couche service (Product, Cart, User, Forum)
│   │   └── Repository/         # Repositories Doctrine
│   ├── config/                 # Configuration Symfony
│   ├── fixtures/               # Donnees de demonstration (Alice YAML)
│   ├── migrations/             # Migrations Doctrine
│   ├── tests/                  # Tests PHPUnit
│   ├── Dockerfile              # Image PHP 8.3-fpm
│   └── .env.example            # Variables d'environnement
├── frontend/                   # SPA Angular 19
│   └── src/app/
│       ├── core/               # Services, guards, interceptors, modeles
│       ├── features/           # Composants par fonctionnalite (lazy-loaded)
│       │   ├── admin/          # Dashboard, gestion produits/utilisateurs
│       │   ├── articles/       # Liste et detail des articles
│       │   ├── auth/           # Login et inscription
│       │   ├── bonus/          # Easter egg Space Invaders
│       │   ├── cart/           # Panier
│       │   ├── contact/        # Formulaire de contact
│       │   ├── forum/          # Categories, sous-categories, threads, posts
│       │   ├── home/           # Page d'accueil
│       │   ├── products/       # Boutique et detail produit
│       │   └── space/          # Espace utilisateur
│       └── shared/             # Header, footer
├── docker/                     # Configuration Nginx
└── docker-compose.yml          # Orchestration Docker
```

## Installation avec Docker

### Prerequis

- Docker et Docker Compose

### Lancement

```bash
git clone https://github.com/Ninewashburn/Nimes-Alerie.git
cd Nimes-Alerie
docker compose up -d
```

Services disponibles :
- **API Symfony** : http://localhost:8000
- **API Docs** : http://localhost:8000/api/docs
- **Frontend Angular** : http://localhost:4200

### Initialisation de la base de donnees

```bash
docker compose exec php php bin/console doctrine:migrations:migrate
docker compose exec php php bin/console lexik:jwt:generate-keypair
docker compose exec php php bin/console hautelook:fixtures:load
```

### Comptes de demonstration

| Email | Mot de passe | Role |
|-------|-------------|------|
| admin@nimes-alerie.fr | admin123 | Admin |
| client@test.fr | client123 | Client |
| sophie.legrand@email.com | sophie123 | Client |
| pierre.roux@email.com | pierre123 | Client |

## Installation locale (sans Docker)

### Prerequis

- PHP >= 8.2 avec extensions intl, pdo_mysql, zip
- Composer 2.x
- Node.js >= 18
- MySQL 8.0+

### Backend

```bash
cd backend
composer install
cp .env.example .env
# Configurez DATABASE_URL dans .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
php bin/console hautelook:fixtures:load
symfony serve
```

### Frontend

```bash
cd frontend
npm install
npm start
# Accessible sur http://localhost:4200
```

## Securite

Les fichiers suivants ne doivent **jamais** etre commites :
- `backend/.env` (secrets de production)
- `backend/.env.local` (configuration locale)
- `backend/config/jwt/*.pem` (cles JWT)

## Commandes utiles

```bash
# Depuis le dossier backend/
php bin/console cache:clear
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/phpunit

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Licence

Proprietaire - Tous droits reserves
