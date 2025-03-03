<?php

namespace Toxicity\AlteredApi\Builder;

use Toxicity\AlteredApi\Entity\Set;
use DateTimeImmutable;

class SetBuilder
{
    public function build(Set $set, array $data): Set
    {
        if ($set->getId()) {
            $set->setUpdatedDate(new DateTimeImmutable());
        }

        $set->setAlteredId($data['id']);
        if (array_key_exists('code', $data)) {
            $set->setCode($data['code']);
        }
        if (array_key_exists('isActive', $data)) {
            $set->setIsActive($data['isActive']);
        }
        if (array_key_exists('name', $data)) {
            $set->setName($data['name']);
        }
        if (array_key_exists('illustration', $data)) {
            $set->setIllustration($data['illustration']);
        }
        if (array_key_exists('reference', $data)) {
            $set->setReference($data['reference']);
        }
        if (array_key_exists('illustration', $data)) {
            $set->setIllustration($data['illustration']);
        }
        if (array_key_exists('illustrationPath', $data)) {
            $set->setIllustrationPath($data['illustrationPath']);
        }
        if (array_key_exists('cardGoogleSheets', $data)) {
            $set->setCardGoogleSheets($data['cardGoogleSheets']);
        }
        if (array_key_exists('date', $data)) {
            $set->setDate(new DateTimeImmutable($data['date']));
        }

        return $set;
    }
}
