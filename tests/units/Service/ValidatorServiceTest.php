<?php

namespace Toxicity\AlteredApi\tests\units\Service;

use atoum\atoum\test;
use Toxicity\AlteredApi\Model\CardFactionConstant;
use Toxicity\AlteredApi\Request\SearchCardRequest;
use Toxicity\AlteredApi\Service\ValidatorService;

class ValidatorServiceTest extends test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Service\ValidatorService';
    }

    public function testValidateSearchRequestWithValidRequest()
    {
        $searchRequest = new SearchCardRequest();
        $searchRequest->factions = [CardFactionConstant::AXIOM];
        $searchRequest->name = 'Sigismar';
        $searchRequest->mainCost = 3;

        $errors = ValidatorService::validateSearchRequest($searchRequest);

        $this
            ->array($errors)
                ->isEmpty()
        ;
    }

    public function testValidateSearchRequestWithEmptyRequest()
    {
        $searchRequest = new SearchCardRequest();

        $errors = ValidatorService::validateSearchRequest($searchRequest);

        $this
            ->array($errors)
                ->isEmpty()
        ;
    }
}

