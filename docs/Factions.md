## Altered Api Php - Sets

This class is used to get factions from altered website.

## Code Example

```php
<?php

use Toxicity\AlteredApi\Lib\Factions;

$factions = Factions::all('fr-fr'');
```

## Response

```js
[
    {
        "@id":"\/factions\/YZ",
        "@type":"Faction",
        "reference":"YZ",
        "color":"#764891",
        "id":"01GE7AC9WEQKW1Y1BF8SCY7459",
        "name":"Yzmir"
    },
    {
        "@id":"\/factions\/BR",
        "@type":"Faction",
        "reference":"BR",
        "color":"#c32637",
        "id":"01GE7AC9WY6PK56RADXXD6P1T4",
        "name":"Bravos"
    }
]