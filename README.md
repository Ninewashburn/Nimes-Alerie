# La Nîmes'Alerie 🛸

> *Équipez vos compagnons canins et félins pour les conditions extrêmes de la vie orbitale.*

Application e-commerce à thème spatial dédiée aux animaux de compagnie, avec forum communautaire et back-office complet. Construite avec **Symfony 7.2** (API REST) et **Angular 19** (SPA).

Projet éducatif fullstack réalisé dans le cadre d'une formation en reconversion professionnelle (2021-2022), entièrement modernisé en 2026 (Symfony 5.4 → 7.2, Angular 9 → 19, Docker, API Platform 3.4, Signals, Tailwind CSS).

---

## Stack technique

| Couche | Technologie |
|---|---|
| **Backend** | Symfony 7.2, API Platform 3.4, Doctrine ORM 3.0, PHP 8.2+ |
| **Frontend** | Angular 19 — composants standalone, Signals, routes lazy-loaded |
| **Authentification** | JWT — Lexik JWT Authentication Bundle, RSA 4096 bits |
| **Base de données** | MySQL 8.0 |
| **Styles** | Tailwind CSS v3, glassmorphism, dark mode natif |
| **Qualité backend** | PHPStan niveau 6, PHP-CS-Fixer, PHPUnit 10 |
| **Qualité frontend** | ESLint (flat config), Prettier |
| **Conteneurisation** | Docker — PHP 8.3-fpm-alpine, Nginx, MySQL 8.0, Node 22 |

---

## Fonctionnalités

### Boutique
- Catalogue produits avec filtres (recherche textuelle, catégorie, budget)
- Pagination progressive ("charger plus")
- Fiche produit détaillée
- Panier persistant
- Tunnel de commande en 3 étapes (panier → livraison → paiement)
- Confirmation de commande avec numéro de facture

### Espace utilisateur
- Inscription / connexion JWT
- Profil éditable (coordonnées, mot de passe)
- Historique de commandes avec détail des articles et statuts
- Page "Mon Espace" réservée aux clients (les admins ont accès au back-office)

### Forum communautaire
- Arborescence : Catégories → Sous-catégories → Fils de discussion → Messages
- Création de fil et de message (utilisateurs connectés)
- Édition et suppression de ses propres messages
- Suppression de fil (admins uniquement)
- Compteurs de votes (up/down) par message

### Journaux orbitaux (Articles)
- Liste avec mise en avant du dernier article
- Vue détaillée
- Rédaction, modification et suppression (admins)

### Back-office admin
- Dashboard : KPIs (commandes, chiffre d'affaires, utilisateurs, produits), alertes stock faible, commandes récentes
- Navigation rapide vers tous les modules
- Édition de stock rapide depuis le dashboard
- Gestion produits complète : créer, modifier (titre, description, prix HT/TTC, stock, statut), supprimer
- Gestion des utilisateurs : liste, rôles

### Autres
- Pages légales (CGU, CGV, Mentions légales)
- Page de contact
- Easter egg : Space Invaders jouable avec effets sonores Web Audio API
- Thème galactique cohérent (glassmorphism, dark mode, typographie `Space Grotesk`)

---

## Architecture du projet

```
Nimes-Alerie/
├── backend/                        # API Symfony 7.2
│   ├── src/
│   │   ├── Controller/             # Endpoints REST (Admin, Orders, Registration, Security)
│   │   ├── Entity/                 # 16 entités Doctrine
│   │   │   # Article, Bill, Brand, Cart, CartLine, Category, Delivery,
│   │   │   # Order, OrderLine, Post, Product, Rate, SubType, Thread, Type, User
│   │   ├── Enum/                   # Enums PHP 8.1 (OrderStatus, PaymentMethod)
│   │   ├── State/                  # State Processors API Platform
│   │   │   # ThreadStateProcessor, PostStateProcessor (injection user + timestamps)
│   │   ├── EventListener/          # JWT custom claims (rôles, prénom)
│   │   ├── DataFixtures/           # Fixtures Alice YAML
│   │   ├── Service/                # Couche service (Cart, User, Forum...)
│   │   └── Repository/             # Repositories Doctrine
│   ├── config/
│   │   └── jwt/                    # Clés RSA (non versionnées)
│   ├── migrations/                 # Migrations Doctrine
│   ├── tests/                      # Tests PHPUnit
│   ├── Dockerfile
│   └── .env.example
│
├── frontend/                       # SPA Angular 19
│   └── src/app/
│       ├── core/
│       │   ├── guards/             # authGuard, adminGuard
│       │   ├── interceptors/       # JWT interceptor
│       │   ├── models/             # Interfaces TypeScript
│       │   └── services/           # admin, article, auth, cart, checkout, forum, product
│       ├── features/               # Composants lazy-loaded par domaine
│       │   ├── admin/              # Dashboard, produits, utilisateurs
│       │   ├── articles/           # Liste et détail
│       │   ├── auth/               # Login, inscription
│       │   ├── bonus/              # Space Invaders (Web Audio API)
│       │   ├── cart/               # Panier
│       │   ├── checkout/           # Tunnel de commande
│       │   ├── contact/            # Formulaire de contact
│       │   ├── forum/              # Catégories, fils, messages
│       │   ├── home/               # Accueil
│       │   ├── legal/              # CGU, CGV, Mentions légales
│       │   ├── products/           # Boutique et fiche produit
│       │   ├── profile/            # Profil utilisateur
│       │   └── space/              # Espace personnel (commandes)
│       └── shared/
│           └── components/         # Header, footer
│
├── docker/                         # Configuration Nginx
└── docker-compose.yml
```

---

## Installation avec Docker

### Prérequis

- Docker >= 24 et Docker Compose v2

### Démarrage en une commande

```bash
git clone https://github.com/Ninewashburn/Nimes-Alerie.git
cd Nimes-Alerie
docker compose up -d
```

Services démarrés :

| Service | URL |
|---|---|
| **Frontend Angular** | http://localhost:4200 |
| **API Symfony** | http://localhost:8000 |
| **Documentation API** | http://localhost:8000/api/docs |
| **MySQL** | localhost:3306 |

### Initialisation de la base de données

```bash
# Migrations
docker compose exec php php bin/console doctrine:migrations:migrate

# Génération des clés JWT
docker compose exec php php bin/console lexik:jwt:generate-keypair

# Chargement des données de démonstration
docker compose exec php php bin/console hautelook:fixtures:load
```

---

## Installation locale (sans Docker)

### Prérequis

- PHP >= 8.2 avec les extensions `intl`, `pdo_mysql`, `zip`, `openssl`
- Composer 2.x
- Node.js >= 18 et npm
- MySQL 8.0+

### Backend

```bash
cd backend
composer install
cp .env.example .env
# Éditez DATABASE_URL et JWT_PASSPHRASE dans .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
php bin/console hautelook:fixtures:load
symfony serve --port=8000
```

### Frontend

```bash
cd frontend
npm install
npm start
# http://localhost:4200
```

---

## Variables d'environnement

Copiez `backend/.env.example` vers `backend/.env` et renseignez :

| Variable | Description | Exemple |
|---|---|---|
| `APP_SECRET` | Secret Symfony (32 caractères aléatoires) | `openssl rand -hex 16` |
| `DATABASE_URL` | DSN MySQL | `mysql://user:pass@127.0.0.1:3306/nimes_alerie?serverVersion=8.0` |
| `JWT_PASSPHRASE` | Passphrase de la clé privée RSA | chaîne aléatoire sécurisée |
| `CORS_ALLOW_ORIGIN` | Origines autorisées pour le CORS | `^https?://(localhost\|127\.0\.0\.1)(:[0-9]+)?$` |

---

## Comptes de démonstration

| Email | Mot de passe | Rôle |
|---|---|---|
| admin@nimes-alerie.fr | admin123 | Admin |
| client@test.fr | client123 | Client |
| sophie.legrand@email.com | sophie123 | Client |
| pierre.roux@email.com | pierre123 | Client |

---

## Commandes utiles

```bash
# ── Backend (dans backend/) ──────────────────────────────

# Cache
php bin/console cache:clear

# Migrations
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:status

# Qualité de code
vendor/bin/phpstan analyse
vendor/bin/php-cs-fixer fix --dry-run
vendor/bin/phpunit

# Debug routes / services
php bin/console debug:router
php bin/console debug:container


# ── Frontend (dans frontend/) ────────────────────────────

# Développement
npm start

# Build de production
npm run build

# Lint & format
npx eslint src/
npx prettier --write src/
```

---

## Sécurité

Les fichiers suivants ne doivent **jamais** être versionnés :

```
backend/.env
backend/.env.local
backend/config/jwt/private.pem
backend/config/jwt/public.pem
```

Ces entrées sont déjà présentes dans `.gitignore`. Si les clés JWT sont absentes au premier lancement, régénérez-les avec `lexik:jwt:generate-keypair`.

---

## Licence

Propriétaire — Tous droits réservés © Ninewashburn
