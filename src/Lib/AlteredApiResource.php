<?php

namespace Toxicity\AlteredApi\Lib;

use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Service\AlteredApiService;

abstract class AlteredApiResource
{
    private static ?AlteredApiService $_instance = null;

    static function build(): AlteredApiService
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new AlteredApiService(new AlteredHttpClient());
        }

        return self::$_instance;
    }
}
