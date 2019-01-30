#!/usr/bin/env bash

DIR_ROOT="$(dirname "${BASH_SOURCE[0]}")"

function warn() {
    echo -e "\e[31m$1\e[0m"
}

if [ ! -f "$DIR_ROOT/.env" ]; then
    warn '.env file not found. Run this to create it:'
    echo
    echo "    cp $DIR_ROOT/.env.example $DIR_ROOT/.env"
    echo
    warn 'Be sure to adjust `DB_*` values in .env before continuing!'
    exit 1
fi

# Install dependencies
composer install -d "$DIR_ROOT"

# Initialize database
php "$DIR_ROOT/artisan" migrate --step

# Download image metadata from data-aggregator
php "$DIR_ROOT/artisan" api:import

# Download actual image files (~20 GB)
php "$DIR_ROOT/artisan" image:download

# Download info.json files from IIIF
php "$DIR_ROOT/artisan" info:download

# Imports info.json files into database
php "$DIR_ROOT/artisan" info:import

# Calculate dominant color for images
php "$DIR_ROOT/artisan" image:color

# Calculate low-quality image placeholders (LQIPs)
php "$DIR_ROOT/artisan" image:lqip
