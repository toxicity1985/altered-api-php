<?php

namespace Toxicity\AlteredApi\Lib;

use Toxicity\AlteredApi\Provider\AlteredHttpClient;
use Toxicity\AlteredApi\Service\AlteredApiService;

abstract class AlteredApiResource
{
    static function build(): AlteredApiService
    {
        return new AlteredApiService(new AlteredHttpClient());
    }
}
