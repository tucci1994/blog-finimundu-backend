# Blog API — Laravel 11 + Sanctum + Docker

## Avvio rapido

```bash
# 1. Avvia i container
docker compose up -d --build

# 2. Setup (installa Laravel, migra DB, crea utenti) — solo la prima volta
docker compose exec app bash setup.sh

# 3. API pronta su http://localhost:8000/api/v1
```

## Utenti di test (password: "password")

| Email             | Ruolo  |
|-------------------|--------|
| admin@blog.it     | admin  |
| editor@blog.it    | editor |
| author@blog.it    | author |

## Endpoint principali

| Metodo | URL                       | Auth |
|--------|---------------------------|------|
| POST   | /api/v1/auth/login        | No   |
| POST   | /api/v1/auth/logout       | Si   |
| GET    | /api/v1/auth/me           | Si   |
| GET    | /api/v1/posts             | Si   |
| POST   | /api/v1/posts             | Si   |
| GET    | /api/v1/posts/{id}        | Si   |
| PUT    | /api/v1/posts/{id}        | Si   |
| DELETE | /api/v1/posts/{id}        | Si   |

## Comandi utili

```bash
# Reset DB
docker compose exec app php artisan migrate:fresh --seed --seeder=AdminUserSeeder

# Tinker
docker compose exec app php artisan tinker

# Log
docker compose logs -f app
```
