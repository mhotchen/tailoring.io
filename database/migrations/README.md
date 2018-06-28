# Determinism in migrations

I found myself writing a similar comment in several migrations so I've decided to write a small document instead.

Migrations *must* be deterministic. What this means is that no changes to the business domain or code should have any
effect on how an existing migration behaves. This way, whether you're starting from a fresh migration or have an already
existing DB then the structure will always be the same.

I do in fact rely on a few classes in the domain in order to build migrations. Specifically the enum types which allow
for easy conversion to/from the PHP classes and the database values. It would be tempting the use the `Enum::values`
method when creating an enum type in the database but this would be non-deterministic; if we add a new enum value then
the migration's behavior will change. Instead you should explicitly lay out which values from the Enum are used at the
point in time the enum was created in the database. The same goes for the default value, which could come from the
domain (eg. `UnitOfMeasurementSetting::DEFAULT`) but again this is non-deterministic because if the domain changes so to
would the migration. When changes do need made then they should exist as new migrations and should only rely on domain
values that are never going to change.

The seeds/factories, because they're used only for testing, can be non-deterministic.