# Headless WordPress PoC

pnpm monorepo with a [Roots Bedrock](https://roots.io/bedrock/) backend and a [Nuxt 3](https://nuxt.com/) frontend, connected via [WPGraphQL](https://www.wpgraphql.com/).

## Structure

```
.
├── apps/
│   ├── cms/        Bedrock (WordPress) + Docker
│   └── web/        Nuxt 3 frontend
├── package.json    Root lifecycle scripts
└── pnpm-workspace.yaml
```

## Prerequisites

- [pnpm](https://pnpm.io/) ≥ 9
- [Composer](https://getcomposer.org/) ≥ 2
- [Docker](https://www.docker.com/) + Docker Compose v2
- ACF Pro license key

## First-time Setup

```bash
make setup
```

This single command will:
1. Check that `pnpm`, `composer`, and `docker` are installed
2. Copy `.env.example` files (and remind you to fill in WP salts)
3. Create `apps/cms/auth.json` from the example and **pause** so you can add your ACF Pro license key
4. Run `composer install` in `apps/cms`
5. Run `pnpm install` at the workspace root
6. Build and start the Docker containers (WordPress on `:8080`)

After `make setup`, open **http://localhost:8080** to finish the WordPress install wizard, then activate:
- **Headless Redirect** theme
- **WPGraphQL**, **WPGraphQL for ACF**, **Advanced Custom Fields PRO** plugins

### Manual steps (if you prefer)

```bash
# ACF Pro Composer auth
cp apps/cms/auth.json.example apps/cms/auth.json
# Edit apps/cms/auth.json — replace YOUR_ACF_PRO_LICENSE_KEY

# Environment files
cp apps/cms/.env.example apps/cms/.env   # add WP salts
cp apps/web/.env.example apps/web/.env

# Dependencies
cd apps/cms && composer install && cd ../..
pnpm install

# Start CMS
pnpm cms:up
```

## Daily Development

| Command | Description |
|---|---|
| `make dev` | Start Nuxt dev server |
| `make build` | Build Nuxt for production |
| `make preview` | Preview production build |
| `make cms-up` | Start WordPress (Docker) |
| `make cms-down` | Stop WordPress |
| `make cms-logs` | Tail WordPress logs |
| `make cms-fresh` | Wipe DB volumes and restart |
| `make cms-shell` | Shell into the PHP container |
| `make clean` | Remove build artifacts |
| `make reset` | Stop + clean everything |

## Architecture

### Content Flow

```
WordPress Editor (Gutenberg)
  → ACF Blocks (acf/hero, acf/text-content, acf/image-gallery)
    → WPGraphQL + WPGraphQL-ACF exposes editorBlocks
      → Nuxt [...slug].vue queries nodeByUri
        → BlockResolver maps block.name → Vue component
          → HeroBlock / TextContentBlock / ImageGalleryBlock
```

### GraphQL Endpoint

`http://localhost:8080/wp/graphql`

### Key Files

| File | Purpose |
|---|---|
| `apps/cms/web/app/mu-plugins/poc-blocks/poc-blocks.php` | Register ACF blocks + field groups |
| `apps/cms/web/app/themes/headless-redirect/functions.php` | Redirect public WP URLs to Nuxt |
| `apps/cms/config/environments/development.php` | CORS headers + GraphQL debug |
| `apps/web/pages/[...slug].vue` | Catch-all Nuxt route |
| `apps/web/components/BlockResolver.vue` | Maps block names to Vue components |
| `apps/web/queries/getNodeByUri.gql` | Main GraphQL query |

## Adding a New Block

1. Register it in `apps/cms/web/app/mu-plugins/poc-blocks/poc-blocks.php`
2. Create `apps/web/components/blocks/YourBlock.vue`
3. Add an entry to `blockMap` in `apps/web/components/BlockResolver.vue`
