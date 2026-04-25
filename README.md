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

### 1. Configure ACF Pro

Copy the Composer auth template and insert your ACF Pro license key:

```bash
cp apps/cms/auth.json.example apps/cms/auth.json
# Edit apps/cms/auth.json — replace YOUR_ACF_PRO_LICENSE_KEY
```

### 2. Configure environment files

```bash
cp apps/cms/.env.example apps/cms/.env
cp apps/web/.env.example apps/web/.env
# Edit both files as needed (salts, DB passwords, etc.)
```

Generate WP salts at https://roots.io/salts.html and paste them into `apps/cms/.env`.

### 3. Install PHP dependencies

```bash
cd apps/cms && composer install && cd ../..
```

### 4. Install JS dependencies

```bash
pnpm install
```

### 5. Start the CMS (WordPress + MySQL)

```bash
pnpm cms:up
```

Open http://localhost:8080 to complete the WordPress installation wizard.
- Set the site URL to `http://localhost:8080`
- Activate the **Headless Redirect** theme
- Activate plugins: **WPGraphQL**, **WPGraphQL for ACF**, **Advanced Custom Fields PRO**

### 6. Start the Nuxt dev server

```bash
pnpm dev
```

Frontend is available at http://localhost:3000.

## Daily Development

| Command | Description |
|---|---|
| `pnpm dev` | Start Nuxt dev server |
| `pnpm build` | Build Nuxt for production |
| `pnpm preview` | Preview production build |
| `pnpm cms:up` | Start WordPress (Docker) |
| `pnpm cms:down` | Stop WordPress |
| `pnpm cms:logs` | Tail WordPress logs |
| `pnpm cms:fresh` | Wipe DB volumes and restart |

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
