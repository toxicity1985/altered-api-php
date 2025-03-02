# altered-api-php
This library is based on the Altered.gg website. I develop it because I need it to manage my card collections.

## Dependencies
- PHP >= 8.2

## Code Example
```php
<?php

use Toxicity\AlteredApi\Lib\Cards;

$card = Cards::byReference('ALT_ALIZE_A_AX_35_C');
echo $card['reference'];
```

Some methods require authentication:
```php
<?php

use Toxicity\AlteredApi\Lib\Friends;

$friends = Friends::all($token);
echo $friends[0]['id'];
```


## Installation

### Using composer

```sh
composer.phar require toxicity/altered-api-php
```
or
```sh
composer require toxicity/altered-api-php
```

### If you don't have composer
You can download it [here](https://getcomposer.org/download/).

More explanation on available functionality:

- Cards => https://github.com/toxicity1985/altered-api-php/blob/main/docs/Cards.md

- Events => https://github.com/toxicity1985/altered-api-php/blob/main/docs/Events.md

- Factions => https://github.com/toxicity1985/altered-api-php/blob/main/docs/Factions.md

- Sets => https://github.com/toxicity1985/altered-api-php/blob/main/docs/Sets.md
