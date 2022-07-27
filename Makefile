MAKEFILE_DIRECTORY := $(dir $(abspath $(lastword $(MAKEFILE_LIST))))
export PROJECT_LOCATION := $(shell echo ${MAKEFILE_DIRECTORY})

SYMFONY         = php ${PROJECT_LOCATION}bin/console
COMPOSER        = composer --working-dir=${PROJECT_LOCATION}
VENDOR          = ${PROJECT_LOCATION}vendor/bin/
YARN            = yarn --cwd ${PROJECT_LOCATION}
NODE_MODULES    = ${PROJECT_LOCATION}node_modules/
NPX            	= npx --cwd ${PROJECT_LOCATION}

##
##=============================================================================================================
## WAT BNP Paribas
##=============================================================================================================
##-------------------------------------------------------------------------------------------------------------
## Setup
##-------------------------------------------------------------------------------------------------------------

hooks: ## Copy hooks to .git/hooks
	@echo "Source project (don't forget to escape the / character): [\/var\/www\/html\/bnp\/wat] " && read source && [ -z $$source ] && source="\/var\/www\/html\/bnp\/wat"; \
	echo "PHP container : [environment_web_1] " && read container && [ -z $$container ] && container="environment_web_1"; \
	for f in hooks/* ; do \
		cp $$f .git/$$f; \
		chmod +x .git/$$f; \
		sed -i -e "s/%root%/$$source/g" .git/$$f; \
		sed -i -e "s/%container%/$$container/g" .git/$$f; \
	done

${PROJECT_LOCATION}composer.lock: ${PROJECT_LOCATION}composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction -v

vendor: ${PROJECT_LOCATION}composer.lock ## Install the vendors
	$(COMPOSER) install -v

yinstall: ${PROJECT_LOCATION}yarn.lock ## Install modules according to the package-lock.json
	$(YARN) install
	@touch -c node_modules

ydev: ## Runs Webpack for development
	$(YARN) encore dev

ywatch: ## Runs Webpack for development in watching mode
	$(YARN) encore dev --watch

yprod: ## Runs Webpack for production
	$(YARN) encore production

setup: vendor db-create db-migrate yinstall ydev ## Set up the project

update: vendor db-migrate yinstall ydev ## Update the project

.PHONY: hooks vendor yinstall ydev ywatch yprod setup update

##
##-------------------------------------------------------------------------------------------------------------
## Symfony basics & project shortcuts
##-------------------------------------------------------------------------------------------------------------
cache: ## Clear the caches
	$(SYMFONY) cache:clear -v

entity: ## Creates or updates a Doctrine entity class, and optionally an API Platform resource
	$(SYMFONY) make:entity

imports-init: ## Run all initial imports
	@echo "This will erase all your data, are you sure you want to do this (y/n) ? [n]" && read ans && [ -z $$ans ] && ans="n"; \
	if [ $$ans = 'y' ] ; then  \
	$(SYMFONY) wat:import:country --truncate -n &&  \
	$(SYMFONY) wat:import:tournament --truncate -n &&  \
	$(SYMFONY) wat:import:ranking --truncate -n ; fi; \

imports: ## Run all imports
	$(SYMFONY) wat:import:country
	$(SYMFONY) wat:import:tournament
	$(SYMFONY) wat:import:ranking

##
##-------------------------------------------------------------------------------------------------------------
## Database / Doctrine
##-------------------------------------------------------------------------------------------------------------

db-drop: ## Drop database
	@echo "This will erase all your data, are you sure you want to do this (y/n) ? [n]" && read ans && [ -z $$ans ] && ans="n"; \
	if [ $$ans = 'y' ] ; then $(SYMFONY) doctrine:database:drop --if-exists --force; fi; \

db-create: ## Create database
	$(SYMFONY) doctrine:database:create --if-not-exists

db-dump: ## Dumps the SQL needed to update the database schema to match the current mapping metadata
	$(SYMFONY) doctrine:schema:update --dump-sql

db-force: ## Force to execute the SQL needed to update the database schema to match the current mapping metadata
	make db-dump
	$(SYMFONY) doctrine:schema:update --force

db-fixtures: ## Append the fixtures
	$(SYMFONY) doctrine:fixtures:load --append --env=dev

db-purge: ## Purge the fixtures
	$(SYMFONY) doctrine:fixtures:load --purge-with-truncate -n

db-generate: ## Creates a new migration based on database changes
	make db-dump
	$(SYMFONY) make:migration

db-migrate: ## Execute a migration to a specified version or the latest available version
	$(SYMFONY) doctrine:migrations:migrate -n

db-check: ## Tells you if your schema is up-to-date.
	$(SYMFONY) doctrine:migrations:up-to-date

db-down: ## Executes a single migration version down manually
	@echo "Migration identifier:" && read source; \
	$(SYMFONY) doctrine:migrations:execute --down $$source

db-mig-add: ## Manually add migration versions from the version table without applicating it
	@echo "Migration identifier:" && read source; \
	$(SYMFONY) doctrine:migrations:version --add $$source

db-mig-rm: ## Manually delete migration versions from the version table without "downing" it
	@echo "Migration identifier:" && read source; \
	$(SYMFONY) doctrine:migrations:version --delete $$source

db-reset: db-drop db-init ## Reset database

db: db-generate db-migrate ## Update SQL Schema

db-init: db-create db-migrate imports ## Initiate database

.PHONY: db-drop db-create db-dump db-fixtures db-generate db-migrate db-reset db

##
##-------------------------------------------------------------------------------------------------------------
## Tests
##-------------------------------------------------------------------------------------------------------------

phpstan: ## Run the PHPStan analysis at the default level (see phpstan.neon)
	$(VENDOR)phpstan analyse $(PROJECT_LOCATION)src --memory-limit='-1' --configuration=$(PROJECT_LOCATION)phpstan.neon

phpcsfixerdr: ## Run the PHP-CS-Fixer analysis with dry-run mode
	$(VENDOR)php-cs-fixer fix $(PROJECT_LOCATION)src --dry-run --using-cache=no --verbose --config=$(PROJECT_LOCATION).php_cs

phpcsfixer: ## Run the PHP-CS-Fixer analysis with hard mode
	$(VENDOR)php-cs-fixer fix $(PROJECT_LOCATION)src --using-cache=no --verbose --config=$(PROJECT_LOCATION).php_cs

security: ## Run the Security check
	$(VENDOR)security-checker security:check $(PROJECT_LOCATION)composer.lock

container: ## Ensures that arguments injected into services match type declarations
	$(SYMFONY) lint:container --env=dev

twig: ## Run the Twig lint
	$(SYMFONY) lint:twig $(PROJECT_LOCATION)templates

yaml: ## Run the YAML lint
	$(SYMFONY) lint:yaml $(PROJECT_LOCATION)config
	$(SYMFONY) lint:yaml $(PROJECT_LOCATION)translations

eslint: ## Run ESlint fixer
	$(NPX) eslint assets/js/ --fix

eslintaudit: ## Run ESLint audit
	$(NPX) eslint assets/js/

stylelint: ## Run Stylelint fixer
	$(NPX) stylelint "assets/css/**/*.scss" --syntax "scss" --fix

stylelintaudit: ## Run Stylelint audit
	$(NPX) stylelint "assets/css/**/*.scss" --syntax "scss"

tests: phpcsfixerdr security container twig yaml eslintaudit stylelintaudit phpstan ## Run tests application

tests-hooks: security container twig yaml eslintaudit stylelintaudit phpstan ## Run tests application for the git hooks

.PHONY: phpstan phpcsfixer phpcsfixerdr security container twig yaml eslintaudit stylelintaudit tests

# Help
.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) \
		| sed -e 's/^.*Makefile://g' \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' \
		| sed -e 's/\[32m##/[33m/'
.PHONY: help
