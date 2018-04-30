# Backend API for TMP

This is the API/backend for the tailoring management platform.

The objective of the tailoring management platform is to provide a complete set of tools for tailors to manage their
business, such as customer and order management.

## Requirements for development:

* nodejs 8.11 LTS w/ npm
* php 7.2 w/ composer, dom and mbstring extensions
* libpng-dev and build-essentials (fucking nodejs libraries man...)

## Running

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
docker-compose up
docker-compose up -d # run in background
```

## Development

The following will watch changes to the frontend and automatically compile them. Make sure both your composer and
npm dependencies have been installed beforehand:

```bash
npm run watch
```
