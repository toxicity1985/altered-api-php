# altered-api-php
This library is based on the Altered.gg website. I develop it because I need it to manage my card collections.

## Dependencies
- PHP >= 8.2

## Code Example
```php
$card = \Toxicity\AlteredApi\Lib\Cards::byId('ALT_ALIZE_A_AX_35_C');
echo $card['reference'];
```

Some methods require authentication:
```php
$friends = Toxicity\AlteredApi\Lib\Friends::all($token);
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
