<?php

namespace Toxicity\AlteredApi\Service;

use Symfony\Component\Validator\Validation;
use Toxicity\AlteredApi\Request\SearchCardRequest;

class ValidatorService
{
    static function validateSearchCardRequest(SearchCardRequest $searchCardRequest): array
    {
        $errors = [];
        $violations = (Validation::createValidator())->validate($searchCardRequest);
        if (0 !== count($violations)) {
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
