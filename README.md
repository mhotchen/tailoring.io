# Tailoring Online Platform API

This is the API for the Tailoring Online Platform (TOP).

The objective of TOP is to provide a complete set of tools for tailors to manage their business, such as customer and
order management.

This API provides all of that functionality using JSON over HTTP which the Single Page Application (SPA) then consumes.

## Requirements for development:

* PHP 7.2 w/ composer, DOM and mbstring extensions
* Docker w/ docker-compose

## Running

```bash
cp .env.example .env # You'll need to edit this ready for local development
composer install
docker-compose up
docker exec top-php-cli ./artisan migrate:fresh --seed
```

## Design goals

* The code must be clear and simple. This is largely a CRUD application so it doesn't require a complex architecture yet
* Response times should be within ~50ms in development (ie. without network latency)
* With control over the client we can design the endpoints to be perfectly catered to what the client needs
* Follow the principles of YAGI and KISS. Only do things when they're necessary and never a moment before
* Consider the scale of thousands of users with potentially a couple of million customers, not beyond. This application
  has a pretty specific niche and will never grow bigger than this, so the complexity added to consider serious scale
  will never pay off. In addition if we ever do reach that scale it will be smarter to start with a simpler code base
  with which we can apply the latest scaling solutions, instead of trying to use the technology that currently exists

## A note on 'REST'

This is **not** a REST API! This is JSON over HTTP. The URLs are static, as are the resources. REST is designed for
fluidity of such concepts and in order to design an API to be truly RESTful the client would need to be extremely
intelligent. The payoff is minimal at best and the cost is not only an order of magnitude more complexity in both
the client and server implementations, but also considerably more chatter between the two which goes against one of
the core goals of this entire product: it must be usable with an unreliable internet connection.

See [my article](https://dividebyze.ro/2016/08/09/stop-building-rest-apis.html) about this stuff to understand where
I'm coming from.

## Performance

All API responses should be within ~50ms. This number is very forgiving of writing legible code but it does mean that
extensive use of indexes in the database must be made. Thankfully Postgres has very powerful indexing, much moreso than
MySQL.

Take a look at, for example, the `2018_05_27_135818_create_customers_table` and
`2018_06_01_133958_create_customer_search_index` migrations. In the customer creation you can see an index that first
sorts customers by the company, then sorts by when they were updated in descending order. This allows customers to be
retrieved extremely quickly for the home page. The search index is much more advanced, it using the GIN (general
indexes) functionality to build a generic tokenized index from various fields on the customer to allow for fast
searching of the name, email and telephone number in a single search. In addition as you can see you can index the
results of function calls.

These indexes allow for the PHP to be heavily focused on clarity, and removes the need for extra services dedicated to
search or other services that would be necessary if the database were MySQL.

### Caching

Caching isn't in use right now but if it's ever required follow this simple mantra to guarantee safe caching: cache
without expiration and instead ensure you invalidate the cache on update. I have used this technique before with great
success and it's the only way to cache properly. Of course an expiration can later be added to reduce the cache size
if we ever encounter cache size issues, but that probably won't happen.

## Security

Apply common sense! This is a multi-tenant application so make sure to use policies to guarantee correct permissions
for access and make use of relationships, eg. the relationship between a company and their customers that ensures
queries automatically have the correct where clauses when calling `$company->customers()`, instead of creating a query
directly against the customers and potentially forgetting the WHERE clause to limit the customers to only those with
the correct `company_id`.

In addition use UUID for all IDs. There's a trait for the models to ensure each UUID is unique (although the chances of
a collision are already astronomically small). Upon research the performance implication of using a UUID as the ID
is minute, small enough to have no performance penalty unless we're in the billions of rows.

