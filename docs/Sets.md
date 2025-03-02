## Altered Api Php - Sets

This class is used to get sets from altered website.

## Code Example

```php
<?php

use Toxicity\AlteredApi\Lib\Sets;

$sets = Sets::all('fr-fr'');
```

## Response

```js
[
   
    {
        "@id":"\/card_sets\/CORE",
        "@type":"CardSet",
        "date":"2024-08-26T00:00:00+00:00",
        "code":"BTG",
        "isActive":true,
        "illustration":null,
        "illustrationPath":null,
        "cardGoogleSheets":[[]],
        "createdAt":"2024-01-04T14:55:59+00:00",
        "updatedAt":"2024-07-17T23:46:31+00:00",
        "id":"01HKAFJN3HG3TWKYV0E014K01G",
        "reference":"CORE",
        "name":"Au-del\u00e0 des portes",
        "description":null
    },
    {
        "@id":"\/card_sets\/COREKS",
        "@type":"CardSet",
        "date":null,
        "code":"BTG",
        "isActive":true,
        "illustration":null,
        "illustrationPath":null,
        "cardGoogleSheets":[[]],
        "createdAt":"2024-01-27T16:08:57+00:00",
        "updatedAt":"2024-02-14T17:41:21+00:00",
        "id":"01HN5TWSSVYM93FHCEC3K8NNCM",
        "reference":"COREKS",
        "name":"Au-del\u00e0 des portes - \u00c9dition KS",
        "description":null
    },
    {
        "@id":"\/card_sets\/ALIZE",
        "@type":"CardSet",
        "date":null,
        "code":"TBF",
        "isActive":true,
        "illustration":null,
        "illustrationPath":null,
        "cardGoogleSheets":[[]],
        "createdAt":"2024-07-01T12:35:26+00:00",
        "updatedAt":"2025-01-03T12:21:35+00:00",
        "id":"01J1Q4NZCDCGMFTFKASM8H29N6",
        "reference":"ALIZE",
        "name":"\u00c9preuve du froid",
        "description":null
    }
]