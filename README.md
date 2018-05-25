# Tailoring Online Platform API

This is the API for the Tailoring Online Platform (TOP).

The objective of TOP is to provide a complete set of tools for tailors to manage their business, such as customer and
order management.

This API provides all of that functionality over REST which the Single Page Application (SPA) then consumes.

## Requirements for development:

* php 7.2 w/ composer, dom and mbstring extensions

## Running

```bash
cp .env.example .env
composer install
php artisan key:generate
docker-compose up
docker-compose up -d # run in background
```

## A note on 'REST'

This is **not** a REST API! This is JSON over HTTP. The URLs are static, as are the resources. REST is designed for
fluidity of such concepts and in order to design an API to be truly RESTful the client would need to be extremely
intelligent. The payoff is minimal at best and the cost is not only an order of magnitude more complexity in both
the client and server implementations, but also considerably more chatter between the two which goes against one of
the core goals of this entire product: it must be usable with an unreliable internet connection.

See [my article](https://dividebyze.ro/2016/08/09/stop-building-rest-apis.html) about this stuff to understand where
I'm coming from.