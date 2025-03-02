## Altered Api Php - Events

This class is used to search for events based on country code and latitude.
In response, you get the events based on the search and the shops related.

## Code Example

```php
<?php

use Toxicity\AlteredApi\Lib\Events;

$searchEventRequest = new SearchEventRequest();
$searchEventRequest->afterDate = (new DateTimeImmutable());
$searchEventRequest->countryCode = 'BE';
$searchEventRequest->latitude = '50.84770289999999';
$searchEventRequest->longitude = '4.357200100000001';

$events = Events::search($searchEventRequest, $alteredLocale);
```

Response

```js
[
    {
        "@type":"EventLocator",
        "@id":"\/.well-known\/genid\/25f70b520b61a6774278",
        "events":[
            {
                "@id":"\/events\/01JJA9XZZB69TVVRFBFZK10QS8",
                "@type":"Event",
                "eventFormat":{
                    "@id":"\/event_formats\/01J7R7H4132DYX9VES7QF4VXFH",
                    "@type":"EventFormat",
                    "id":"01J7R7H4132DYX9VES7QF4VXFH",
                    "reference":"DRAFT"
                },
                "deckIsMandatory":false,
                "address":{
                    "@id":"\/addresses\/01JJ9YXNFT5P8GFHX20B1B2WJV",
                    "@type":"Address",
                    "street":"13 Keppestraat",
                    "additionalAddress":"",
                    "zipCode":"9320",
                    "city":"Aalst",
                    "region":"",
                    "latitude":50.9191525,
                    "longitude":4.054596,
                    "country":"\/countries\/BE",
                    "fullAddress":"Keppestraat 13, 9320 Aalst, Belgi\u00eb",
                    "phone":"+32 476 09 06 28",
                    "countryCode":"BE"
                },
                "useShopAddress":true,
                "image":{
                    "@id":"\/media\/01JJA9XPR401J62RFRG7QE7TGC",
                    "@type":"https:\/\/schema.org\/MediaObject",
                    "contentUrl":"https:\/\/altered-prod-eu.s3.amazonaws.com\/media\/shop\/photo\/223ba69f5e1e880b6869156abd2973b1b4e47283.png"
                },
                "imagePath":"https:\/\/altered-prod-eu.s3.amazonaws.com\/media\/shop\/photo\/223ba69f5e1e880b6869156abd2973b1b4e47283.png",
                "isFree":false,
                "isComplete":false,
                "ticketingLink":"https:\/\/www.nerdgeek.gg\/event?tags=%5B9%5D",
                "placeNumber":32,
                "organizer":{
                    "@id":"\/users\/01JDMRAP44Z8058A8DW24M3ZG9",
                    "@type":"User",
                    "avatarPath":"https:\/\/altered-prod-eu.s3.amazonaws.com\/media\/shop\/logo\/2f590a95781f9f437043ce8afbeca5d6d30d19ef.jpg",
                    "addresses":[
                        {
                            "@id":"\/addresses\/01JJ9YXNFT5P8GFHX20B1B2WJV",
                            "@type":"Address",
                            "street":"13 Keppestraat",
                            "additionalAddress":"",
                            "zipCode":"9320",
                            "city":"Aalst",
                            "region":"",
                            "latitude":50.9191525,
                            "longitude":4.054596,
                            "country":"\/countries\/BE",
                            "fullAddress":"Keppestraat 13, 9320 Aalst, Belgi\u00eb",
                            "phone":"+32 476 09 06 28",
                            "countryCode":"BE"
                        }
                    ],
                    "shop":{
                        "@id":"\/shops\/01JDMRANV8R6H4STWAEPCSZ0SG",
                        "@type":"Shop",
                        "name":"nerdgeek",
                        "mobilityReducedAccessibility":true,
                        "eatingSpace":false,
                        "sanitary":true,
                        "user":"\/users\/01JDMRAP44Z8058A8DW24M3ZG9",
                        "shopSocialNetworks":[
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SH",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/GOOGLE",
                                    "@type":"SocialNetwork",
                                    "name":"Google"
                                },
                                "socialUrl":"https:\/\/maps.app.goo.gl\/gFHebrhGptraDgX59"
                            },
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SJ",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/WEBSITE",
                                    "@type":"SocialNetwork",
                                    "name":"Website"
                                },
                                "socialUrl":"https:\/\/nerdgeek.be\/"
                            },
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SK",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/FACEBOOK",
                                    "@type":"SocialNetwork",
                                    "name":"Facebook"
                                },
                                "socialUrl":"https:\/\/www.facebook.com\/nerdgeek.gg\/"
                            },
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SM",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/INSTRAGRAM",
                                    "@type":"SocialNetwork",
                                    "name":"Instagram"
                                },
                                "socialUrl":"http:\/\/www.instagram.com\/nerdgeek.gg\/"
                            },
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SN",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/DISCORD",
                                    "@type":"SocialNetwork",
                                    "name":"Discord"
                                },
                                "socialUrl":"http:\/\/discord.gg\/pdXpGqj8jB"
                            },
                            {
                                "@id":"\/shop_social_networks\/01JDMRANV8R6H4STWAEPCSZ0SP",
                                "@type":"ShopSocialNetwork",
                                "socialNetwork":{
                                    "@id":"\/social_networks\/TIK_TOK",
                                    "@type":"SocialNetwork",
                                    "name":"TIK TOK"
                                },
                                "socialUrl":"http:\/\/www.tiktok.com\/@nerdgeek.gg"
                            }
                        ],
                        "id":"01JDMRANV8R6H4STWAEPCSZ0SG",
                        "distance":22.67
                    },
                    "uniqueId":"Nerdgeek_4929"
                },
                "status":"PUBLISHED",
                "mobilityReducedAccessibility":true,
                "startDateTime":"2025-03-02T09:00:00+00:00",
                "endDateTime":"2025-03-02T14:00:00+00:00",
                "isRecurrent":false,
                "createdAt":"2025-01-23T19:23:55+00:00",
                "updatedAt":"2025-01-23T19:24:12+00:00",
                "id":"01JJA9XZZB69TVVRFBFZK10QS8",
                "distance":22.67,
                "name":"Booster Draft",
                "description":"See https:\/\/www.nerdgeek.gg\/event"
            }
        ],
        "shops":[
            {
                "@id":"\/shops\/01J3FRA7HM5MYCYQ08B025X72Y",
                "@type":"Shop",
                "name":"Outpost Brussels",
                "biography":"Welcome to Outpost Brussels, Belgium's top spot for gaming! Open everyday!",
                "mobilityReducedAccessibility":true,
                "eatingSpace":true,
                "sanitary":true,
                "user":{
                "@id":"\/users\/01J3FRA7Y89GV0Y56H7C885VT9",
                "@type":"User",
                "avatarPath":"https:\/\/altered-prod-eu.s3.amazonaws.com\/media\/shop\/logo\/8bed7c40f10b552f5bfa3a7778a57234115df4d3.png",
                "addresses":[
                    {
                        "@id":"\/addresses\/01J3FRA7RFVRQWHMSFWYC80DQG",
                        "@type":"Address",
                        "street":"8 Rue de la Tribune",
                        "zipCode":"1000",
                        "city":"Bruxelles",
                        "region":"",
                        "latitude":50.8486405,
                        "longitude":4.3651891,
                        "country":"\/countries\/BE",
                        "fullAddress":"Rue de la Tribune 8, Brussels, Belgium",
                        "countryCode":"BE"
                    }
                ],
                "shop":"\/shops\/01J3FRA7HM5MYCYQ08B025X72Y",
                "uniqueId":"OutpostBrussels_8777"
                },
                "shopSocialNetworks":[
                    {
                        "@id":"\/shop_social_networks\/01J7ZN7Q7TBEN8V5ZWTM7PF2KP",
                        "@type":"ShopSocialNetwork",
                        "socialNetwork":{
                        "@id":"\/social_networks\/GOOGLE",
                        "@type":"SocialNetwork",
                        "name":"Google"
                    },
                    "socialUrl":"https:\/\/outpostbrussels.be\/"
                },
                "id":"01J3FRA7HM5MYCYQ08B025X72Y",
                "distance":0.57
            }
        ]
   }
]

