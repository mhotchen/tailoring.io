# Custom database connection

See: https://stackoverflow.com/questions/30264517/is-there-a-way-to-create-a-citext-field-using-laravel-5-migrations

The reason I did this is because I want to utilise more types in postgres than what laravel supports by default, along
with having custom definitions for eg. enum types that are required for more than one table. The default enum
implementation in laravel actually replicates enum behavior using check constraints which is useful when an enum is
only used in one table, but not when you want to use the same enum across several tables.

I could have recreated the enum behavior I wanted by using more tables however this reduces database query overhead
along with simplifying the design of the database.

I opted not to use the commenter's own library for the following reasons:

1. It only provides a `passthru` method which means typos could happen in the type since it's simply a string
2. My preliminary design of the database includes complex types like arrays of enums
3. It wouldn't work with the latest version of laravel anyway as the way custom connections are set up has changed

## Adding a custom type

Adding a custom type is easy. First add the type to the `App\Database\Schema\Grammars\CustomGrammar` class. The method
name will be mapped to camelCase, ignoring the 'type' prefix, so for example `typeGarmentTypeArray` is mapped to
`garmentTypeArray` which can then be referenced in the blueprint. The method should return a string which is a valid
field type in CREATE TABLE/ALTER TABLE/etc. queries.

### An example

Let's say you want to create the following table:

```sql
CREATE TABLE payment_schedule (
    name     text,
    schedule timestamp[]
)
```

Laravel by default doesn't support the array of timestamp type so we have to define a custom one. You would start by
defining this custom type on the `CustomGrammer` class:

```php
<?php
// ...

public function typeTimestampArray(Fluent $column): string
{
    return 'timestamp[]';
}

// ...
```

The above 'type' can now be referenced in the Blueprint class with *timestampArray*. You can create a method which
can then be called directly from the migrations by adding it to the `App\Database\Schema\CustomBluePrint` class like so:

```php
<?php
// ...

public function timestampArray(string $column): Fluent
{
    // timestampArray is mapped to the typeTimestampArray field on the CustomGrammar class.
    return $this->addColumn('timestampArray', $column);
}

// ...
```

Finally, in the migration:

```php
<?php
// ...

Schema::create('payment_schedule', function (CustomBlueprint $table) {
    $table->timestampArray('schedule');
});

// ...
```

Voila!

## Using custom types from models

All types return a string in PDO/Eloquent, so it's up to your models to figure out how to parse that string. For example
you can create type mappers in Eloquent to automatically convert to/from PHP objects or simply use the string as is if
there's no need for type conversion.

Refer to [the Laravel documentation](https://laravel.com/docs/5.6/eloquent-mutators#defining-a-mutator) on defining
mutators.