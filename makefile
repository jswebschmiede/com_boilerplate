ROOT_DIR = .
ADMIN_DIR = src/administrator/components/com_boilerplate
VENDOR_DIR = vendor
DEPENDENCIES_DIR = $(ADMIN_DIR)/dependencies

# Main targets
.PHONY: all
all: install scope dump delvendor

# Install dependencies
.PHONY: install
install:
	composer install

# Run PHP-Scoper and move files
.PHONY: scope
scope:
	php-scoper add-prefix --output-dir=$(DEPENDENCIES_DIR) --working-dir=$(ROOT_DIR)

# Dump autoload
.PHONY: dump
dump:
	composer dump-autoload -d $(DEPENDENCIES_DIR) --optimize --classmap-authoritative

# Clean up
.PHONY: clean
clean: delvendor deldependencies

# Delete all files in the directory
.PHONY: delvendor
delvendor:
	rm -rf $(VENDOR_DIR)

.PHONY: deldependencies
deldependencies:
	rm -rf $(DEPENDENCIES_DIR)
