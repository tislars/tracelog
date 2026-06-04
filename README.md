# Headless WordPress PoC

[Roots Bedrock](https://roots.io/bedrock/) backend + [Nuxt 4](https://nuxt.com/) frontend, connected via [WPGraphQL](https://www.wpgraphql.com/).

```
apps/
  cms/   Bedrock (WordPress) + Docker
  web/   Nuxt frontend
```

## Prerequisites

- Node.js + npm
- Composer
- Docker + Docker Compose v2
- ACF Pro license key

## First-time setup

```bash
make setup
```

This installs all dependencies, starts Docker, installs WordPress via WP-CLI, activates all plugins, and seeds demo content (pages, team members, contact form). No browser wizard needed.

| URL | Credentials |
|-----|-------------|
| Site: `http://localhost:8080` | — |
| WP Admin: `http://localhost:8080/wp/wp-admin` | `admin` / `admin` |
| GraphQL: `http://localhost:8080/wp/graphql` | — |

## Daily workflow

```bash
make cms-up   # start WordPress
make dev      # start Nuxt dev server → http://localhost:3000
make cms-down # stop WordPress
```

## Database

```bash
make db-seed  # (re)install WordPress and seed demo content — idempotent
make fresh    # wipe the database and start completely fresh
```

The seed creates:
- **Pages**: Home (front page), About Us, Team, Contact
- **Team members**: 4 members with role, bio, and LinkedIn via ACF
- **Gravity Form**: Contact form (Name, Email, Phone, Message)

Other commands: `make help`
