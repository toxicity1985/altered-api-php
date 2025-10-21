<?php

namespace Toxicity\AlteredApi\tests\units\Request;

use Toxicity\AlteredApi\Request\SearchCardRequest;
use Toxicity\AlteredApi\Exception\InvalidSearchCardRequestException;
use atoum\atoum;

class SearchCardRequestTest extends atoum\test
{
    public function getTestedClassName()
    {
        return 'Toxicity\AlteredApi\Request\SearchCardRequest';
    }

    public function testConstructWithDefaults()
    {
        $request = new SearchCardRequest();

        $this
            ->object($request)
                ->isInstanceOf(SearchCardRequest::class)
            ->array($request->cardSets)
                ->isEmpty()
            ->array($request->factions)
                ->isEmpty()
            ->array($request->rarities)
                ->isEmpty()
        ;
    }

    public function testGetUrlParameters()
    {
        $request = new SearchCardRequest();
        $urlParams = $request->getUrlParameters();

        $this
            ->string($urlParams)
        ;
    }

    public function testGetUrlParametersWithName()
    {
        $request = new SearchCardRequest();
        $request->name = 'Dragon';
        $urlParams = $request->getUrlParameters();

        $this
            ->string($urlParams)
                ->contains('query=Dragon')
        ;
    }
}

