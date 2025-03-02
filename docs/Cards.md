## Altered Api Php - Events

This class is used to search for events based on country code and latitude.
In response, you get the events based on the search and the shops related.

## Code Example

### Get card

```php
<?php

use Toxicity\AlteredApi\Lib\Cards;

$card = Cards::byReference('ALT_CORE_B_AX_10_C', 'fr-fr');
```

Response

```js
{
    "@context": "/contexts/Card",
    "@id": "/cards/ALT_CORE_B_AX_10_C",
    "@type": "Card",
    "loreEntries": [
        {
            "@id": "/lore_entries/01HNGY4YVCFT4RS6PYD0K9Z67Q",
            "@type": "LoreEntry",
            "loreEntryElements": [
                {
                    "@id": "/lore_entry_elements/01HNJH5QEJSR3FSK59Z0WPJ662",
                    "@type": "LoreEntryElement",
                    "loreEntryElementType": {
                        "@id": "/lore_entry_element_types/01GR6AENPX6HQP8G16KNV7BNA2",
                        "@type": "LoreEntryElementType",
                        "id": "01GR6AENPX6HQP8G16KNV7BNA2",
                        "reference": "FLAVOR_TEXT",
                        "subject": "Flavor Text"
                    },
                    "id": "01HNJH5QEJSR3FSK59Z0WPJ662",
                    "text": "« L'Aérolithe ne semble pas affectée par la gravité. En domestiquant ses propriétés, nous pourrions créer des vaisseaux et des villes volantes, tutoyer les nuages... »"
                },
                {
                    "@id": "/lore_entry_elements/01HNJH5QEYYJ9YGHNV22Y939EG",
                    "@type": "LoreEntryElement",
                    "loreEntryElementType": {
                        "@id": "/lore_entry_element_types/01GR6AE2ZSVVYP6SDJXN8Q5DVF",
                        "@type": "LoreEntryElementType",
                        "id": "01GR6AE2ZSVVYP6SDJXN8Q5DVF",
                        "reference": "STORY",
                        "subject": "Histoire"
                    },
                    "id": "01HNJH5QEYYJ9YGHNV22Y939EG",
                    "text": "Jian Lam pose une main sur le cœur d'Aérolithe, tapotant sa surface tandis qu'il nous parle de ses propriétés. J'écoute attentivement l'Eidolon. Comme le Kélon, l'Aérolithe est un matériau qui a émergé avec la Confluence. Les Nomades du Tumulte en parlaient déjà durant leurs voyages : des ruines flottantes, des rocs suspendus dans les airs comme par magie... Ce n'était pas un simple caprice de la nature, mais un nouvel élément, qui pouvait être exploité. C'est ce qu'avait fait Jian Lam il y a plus de trois cents ans de ça. Il avait dessiné des plates-formes et des barges à lévitation, des quantités d'inventions fantasques et visionnaires... Quelque part, c'est même grâce à lui que la capitale ressemble à ce qu'elle est aujourd'hui, avec des zones lacustres qui volettent au-dessus de l'eau sans l'aide de pilotis.\n\nLam active un bouton, et la sphère d'Aérolithe s'élève de quelques centimètres. Il active son propre propulseur dorsal et prend de la hauteur à son tour. Bon, il n'est pas toujours facile de suivre ses explications. Paju le décrit comme quelqu'un de distraitement passionné, avec un tempérament rêveur. Mais quand il parle de ses créations, ses yeux se mettent à pétiller et son enthousiasme est communicatif. Même s'il perd parfois la notion du temps et que ses cours ne finissent jamais à l'heure... Pour nous, l'Aérolithe est ce qui va permettre à nos airships de s'élever dans le ciel et de côtoyer les oiseaux. Situé au centre de l'appareil, juste derrière le cockpit, ce noyau est un peu notre bouée. En y envoyant des impulsions, on pourra faire varier l'altitude, tandis que les moteurs au Kélon nous propulseront en avant ou en arrière. À la demande de l'Eidolon, j'enfile mon réacteur dorsal. J'adore cet aspect de nos travaux pratiques."
                },
            ],
            "loreEntryType": {
                "@id": "/lore_entry_types/01HNGY4YPR0FVZVYWD8YFE0PHP",
                "@type": "LoreEntryType",
                "id": "01HNGY4YPR0FVZVYWD8YFE0PHP",
                "reference": "CHARACTER",
                "name": "Character",
                "description": ""
            },
            "id": "01HNGY4YVCFT4RS6PYD0K9Z67Q",
            "reference": "ALT_CORE_B_AX_10_C"
        }
    ],
    "cardType": {
        "@id": "/card_types/01H19NWA92A4ERAC4ATMSZNASS",
        "@type": "CardType",
        "reference": "CHARACTER",
        "id": "01H19NWA92A4ERAC4ATMSZNASS",
        "name": "Personnage"
    },
    "cardSubTypes": [
        {
            "@type": "CardSubType",
            "@id": "/.well-known/genid/b76126588a60c773e4ce",
            "reference": "ENGINEER",
            "id": "01HKAGPA9AS71JN0H9HQZTBNCD",
            "name": "Ingénieur"
        }
    ],
    "cardSet": {
        "@id": "/card_sets/CORE",
        "@type": "CardSet",
        "id": "01HKAFJN3HG3TWKYV0E014K01G",
        "reference": "CORE",
        "name": "Au-delà des portes"
    },
    "rarity": {
        "@id": "/rarities/COMMON",
        "@type": "Rarity",
        "reference": "COMMON",
        "id": "01GE7AC9WEQKW1Y1BF8SCY745A",
        "name": "Commun"
    },
    "cardRulings": [],
    "imagePath": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/fr_FR/9a03425c92dabfed748f4ead05838698.jpg",
    "assets": {
        "WEB": [
            "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/ALT_CORE_B_AX_10_C_WEB.jpg",
            "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/ALT_CORE_B_AX_10_R_WEB.jpg",
            "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/ALT_CORE_B_AX_10_U_WEB.jpg"
        ]
    },
    "lowerPrice": 0,
    "qrUrlDetail": "https://qr.altered.gg/ALT_CORE_B_AX_10_C",
    "isSuspended": false,
    "reference": "ALT_CORE_B_AX_10_C",
    "id": "01HKAFJNJ6S2WR2VDA7DSRY8MQ",
    "mainFaction": {
        "@id": "/factions/AX",
        "@type": "Faction",
        "reference": "AX",
        "color": "#8c432a",
        "id": "01GE7AC9XBG707G19F03A95TH1",
        "name": "Axiom"
    },
    "allImagePath": {
        "de-de": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/de_DE/070872d8020193675961950f4f5728f5.jpg",
        "en-us": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/en_US/8a71802fe71a6a48e19317c6892d388a.jpg",
        "fr-fr": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/fr_FR/9a03425c92dabfed748f4ead05838698.jpg",
        "it-it": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/it_IT/69022eb78169fb863ab2497700262a5d.jpg",
        "es-es": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/es_ES/5663d9ffa4eb015c2360d31c2c21400b.jpg"
    },
    "name": "Jian, Superviseur d'Assemblage",
    "elements": {
        "MAIN_COST": "2",
        "RECALL_COST": "2",
        "OCEAN_POWER": "0",
        "MOUNTAIN_POWER": "2",
        "FOREST_POWER": "3"
    }
}
```

### Get variant card

```php
<?php

use Toxicity\AlteredApi\Lib\Cards;

$cards = Cards::alternateCardsByReference('ALT_CORE_B_AX_10_C', 'fr-fr');
```

Response

```js
[
    {
        "@id": "/cards/ALT_CORE_B_AX_10_R1",
        "@type": "Card",
        "rarity": {
            "@id": "/rarities/RARE",
            "@type": "Rarity",
            "reference": "RARE"
        },
        "imagePath": "https://altered-prod-eu.s3.amazonaws.com/Art/CORE/CARDS/ALT_CORE_B_AX_10/JPG/fr_FR/730a937931d0510f51fbba6ce8761fd0.jpg",
        "inMyCollection": 0,
        "reference": "ALT_CORE_B_AX_10_R1",
        "id": "01HKAFJNJ7558PPJYXX3MH5GYE",
        "name": "Jian, Superviseur d'Assemblage"
    },
    {
        "@id": "/cards/ALT_COREKS_B_AX_10_C",
        "@type": "Card",
        "rarity": {
            "@id": "/rarities/COMMON",
            "@type": "Rarity",
            "reference": "COMMON"
        },
        "imagePath": "https://altered-prod-eu.s3.amazonaws.com/Art/COREKS/CARDS/ALT_CORE_B_AX_10/JPG/fr_FR/f1a64e4f2fefef99a035cd4b6f5120b2.jpg",
        "inMyCollection": 0,
        "reference": "ALT_COREKS_B_AX_10_C",
        "id": "01HN5TXR38MQZ2Z5W44ABSMMXX",
        "name": "Jian, Superviseur d'Assemblage"
    }
]
```