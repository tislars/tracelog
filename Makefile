.DEFAULT_GOAL := help

# ── Colours ─────────────────────────────────────────────────────────────────
BOLD  := \033[1m
GREEN := \033[0;32m
CYAN  := \033[0;36m
YELLOW:= \033[0;33m
RED   := \033[0;31m
RESET := \033[0m

# ── Paths ────────────────────────────────────────────────────────────────────
CMS_DIR  := apps/cms
WEB_DIR  := apps/web
DC       := docker compose -f $(CMS_DIR)/docker-compose.yml

# ── Help ─────────────────────────────────────────────────────────────────────
.PHONY: help
help: ## Show this help
	@echo ""
	@echo "  $(BOLD)Headless WordPress PoC$(RESET)"
	@echo ""
	@awk 'BEGIN {FS = ":.*##"} /^[a-zA-Z_-]+:.*##/ { printf "  $(CYAN)%-20s$(RESET) %s\n", $$1, $$2 }' $(MAKEFILE_LIST)
	@echo ""

# ── First-time Setup ─────────────────────────────────────────────────────────
.PHONY: setup
setup: check-deps env-files cms-auth composer-install js-install cms-up db-seed ## Full first-time setup (installs WordPress + seeds demo content)
	@echo ""
	@echo "  $(GREEN)$(BOLD)✓ Setup complete!$(RESET)"
	@echo ""
	@echo "  WordPress:  $(CYAN)http://localhost:8080$(RESET)"
	@echo "  WP Admin:   $(CYAN)http://localhost:8080/wp/wp-admin$(RESET)  (admin / admin)"
	@echo ""
	@echo "  Run $(CYAN)make dev$(RESET) to start the Nuxt frontend."
	@echo ""

# ── Prerequisite Checks ───────────────────────────────────────────────────────
.PHONY: check-deps
check-deps: ## Check required tools are installed
	@echo "$(BOLD)Checking prerequisites...$(RESET)"
	@command -v node     >/dev/null 2>&1 || { echo "$(RED)✗ node not found$(RESET)    → https://nodejs.org"; exit 1; }
	@command -v npm      >/dev/null 2>&1 || { echo "$(RED)✗ npm not found$(RESET)     → https://nodejs.org"; exit 1; }
	@command -v composer >/dev/null 2>&1 || { echo "$(RED)✗ composer not found$(RESET) → https://getcomposer.org"; exit 1; }
	@command -v docker   >/dev/null 2>&1 || { echo "$(RED)✗ docker not found$(RESET)  → https://docs.docker.com/get-docker/"; exit 1; }
	@docker compose version >/dev/null 2>&1 || { echo "$(RED)✗ docker compose v2 not found$(RESET)"; exit 1; }
	@echo "  $(GREEN)✓ node, npm, composer, docker$(RESET)"

# ── Environment Files ─────────────────────────────────────────────────────────
.PHONY: env-files
env-files: ## Copy .env.example files if .env doesn't exist yet
	@echo "$(BOLD)Configuring environment files...$(RESET)"
	@if [ ! -f $(CMS_DIR)/.env ]; then \
		cp $(CMS_DIR)/.env.example $(CMS_DIR)/.env; \
		echo "  $(GREEN)✓ Created $(CMS_DIR)/.env$(RESET)"; \
		echo "  $(YELLOW)⚠  Edit $(CMS_DIR)/.env — add WP salts from https://roots.io/salts.html$(RESET)"; \
	else \
		echo "  $(CYAN)→ $(CMS_DIR)/.env already exists, skipping$(RESET)"; \
	fi
	@if [ ! -f $(WEB_DIR)/.env ]; then \
		cp $(WEB_DIR)/.env.example $(WEB_DIR)/.env; \
		echo "  $(GREEN)✓ Created $(WEB_DIR)/.env$(RESET)"; \
	else \
		echo "  $(CYAN)→ $(WEB_DIR)/.env already exists, skipping$(RESET)"; \
	fi

# ── ACF Pro Auth ──────────────────────────────────────────────────────────────
.PHONY: cms-auth
cms-auth: ## Set up auth.json for ACF Pro (copies example if needed)
	@echo "$(BOLD)Checking ACF Pro Composer auth...$(RESET)"
	@if [ ! -f $(CMS_DIR)/auth.json ]; then \
		cp $(CMS_DIR)/auth.json.example $(CMS_DIR)/auth.json; \
		echo "  $(YELLOW)⚠  $(CMS_DIR)/auth.json created from example.$(RESET)"; \
		echo "  $(YELLOW)   Replace YOUR_ACF_PRO_LICENSE_KEY with your real license key before running composer install.$(RESET)"; \
		echo "  $(YELLOW)   Get your key at: https://www.advancedcustomfields.com/my-account/$(RESET)"; \
		echo ""; \
		echo "  Press Enter once auth.json is updated, or Ctrl-C to abort."; \
		read _; \
	else \
		echo "  $(CYAN)→ $(CMS_DIR)/auth.json already exists, skipping$(RESET)"; \
	fi

# ── PHP Dependencies ──────────────────────────────────────────────────────────
.PHONY: composer-install
composer-install: ## Install PHP dependencies via Composer
	@echo "$(BOLD)Installing PHP dependencies...$(RESET)"
	cd $(CMS_DIR) && composer install --no-interaction
	@echo "  $(GREEN)✓ Composer install complete$(RESET)"

# ── JS Dependencies ───────────────────────────────────────────────────────────
.PHONY: js-install
js-install: ## Install JS dependencies via npm
	@echo "$(BOLD)Installing JS dependencies...$(RESET)"
	cd $(WEB_DIR) && npm install
	@echo "  $(GREEN)✓ npm install complete$(RESET)"

# ── Docker / CMS ─────────────────────────────────────────────────────────────
.PHONY: cms-up
cms-up: ## Start WordPress (Docker)
	@echo "$(BOLD)Starting CMS containers...$(RESET)"
	$(DC) up -d --build
	@echo "  $(GREEN)✓ CMS running at http://localhost:8080$(RESET)"

.PHONY: cms-down
cms-down: ## Stop WordPress containers
	$(DC) down

.PHONY: cms-fresh
cms-fresh: ## Wipe DB volumes and restart CMS
	$(DC) down -v
	$(DC) up -d --build

.PHONY: db-seed
db-seed: ## Install WordPress (if needed) and seed demo content
	@bash $(CMS_DIR)/bin/seed.sh

.PHONY: fresh
fresh: cms-fresh db-seed ## Wipe database, reinstall WordPress, and reseed demo content

.PHONY: cms-logs
cms-logs: ## Tail CMS container logs
	$(DC) logs -f

.PHONY: cms-shell
cms-shell: ## Open a shell in the PHP container
	$(DC) exec php sh

# ── Development ───────────────────────────────────────────────────────────────
.PHONY: dev
dev: ## Start Nuxt dev server (CMS must already be running)
	cd $(WEB_DIR) && npm run dev

.PHONY: build
build: ## Build Nuxt for production
	cd $(WEB_DIR) && npm run build

.PHONY: preview
preview: ## Preview the production Nuxt build
	cd $(WEB_DIR) && npm run preview

# ── Utilities ─────────────────────────────────────────────────────────────────
.PHONY: clean
clean: ## Remove generated files (.nuxt, .output, vendor) — does NOT wipe DB
	rm -rf $(WEB_DIR)/.nuxt $(WEB_DIR)/.output
	rm -rf $(CMS_DIR)/vendor
	@echo "  $(GREEN)✓ Cleaned build artifacts$(RESET)"

.PHONY: reset
reset: cms-down clean ## Full reset: stop containers, remove build artifacts
	@echo "  $(YELLOW)Run 'make setup' to start fresh.$(RESET)"
