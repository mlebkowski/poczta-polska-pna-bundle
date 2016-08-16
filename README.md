Poczta Polska PNA Bundle
------------------------

Provides a Symfony Form validator to ensure post code matches given city, street and house number.

## Installation

```
composer require nassau/poczta-polska-pna-bundle
```

Add the bundle to your `Kernel`:

```
    $bundles = [
// ...
        new Nassau\PocztaPolskaPnaBundle\PocztaPolskaPnaBundle(),
// ...
    ];
```

## Usage

After the database is imported (and optionally indexed), add the `PnaAddress` constraint to your form. There are options
to provide names of the different fields for each value (city, province, post code, etc). 

## Examples

Look at the `DefaultController` for a demo. 

## Importing the database

Make sure you have the entities mapped and the database schema updated. Then, use the `pna:import` command:

```
app/console pna:import spispna-cz1.txt
```

The file is located in the "UTF-8" directory on the [drive provided by Poczta Polska](https://www.poczta-polska.pl/spis-pna/).

### Normalization of city names

There are some cities that are separated into districts in the `spispna-cz1.txt`. This can be undone using the `--exceptions` 
option. Just use this option multiple times with names of the cities you wish to preserve. For example, if there is a:
`Foo (bar district)` and you’d like to change it to "Foo", use `app/console pna:import --exceptions Foo`

By default all the major cities (Warszawa, Poznań, Wrocław, Łódź and Kraków) are normalized.

## Algolia indexing

You may want to define the algolia indexing using symfony config:

```
# app/config/config.yml
nassau_pna:
    index_name: "pna_cities"
```

You are required to define the Algolia Client using [goldenline/algolia-bundle](https://github.com/GoldenLine/AlgoliaBundle).

Then you have the `pna:index` command (no arguments).
