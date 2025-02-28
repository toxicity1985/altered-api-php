<?php

namespace Toxicity\AlteredApi\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Toxicity\AlteredApi\Contract\SearchRequestInterface;
use DateTimeImmutable;

class SearchEventRequest implements SearchRequestInterface
{
    #[Assert\NotBlank]
    #[Assert\Country]
    public string $countryCode;

    #[Assert\Date]
    public ?DateTimeImmutable $afterDate = null;

    #[Assert\Date]
    public ?DateTimeImmutable $beforeDate = null;

    #[Assert\Type('float')]
    public ?string $latitude = null;

    #[Assert\Type('float')]
    public ?string $longitude = null;

    #[Assert\Type('boolean')]
    public ?bool $mobilityReducedAccessibility = null;

    public function getUrlParameters(): string
    {
        $urlParameters = 'countryCode=' . $this->countryCode . '&country=' . $this->countryCode;
        if ($this->afterDate !== null) {
            $urlParameters .= '&startDateTime[after]=' . $this->afterDate->format('Y-m-d');
        }
        if ($this->beforeDate !== null) {
            $urlParameters .= '&startDateTime[before]=' . $this->beforeDate->format('Y-m-d');
        }
        if ($this->latitude !== null) {
            $urlParameters .= '&latitude=' . $this->latitude;
        }
        if ($this->longitude !== null) {
            $urlParameters .= '&longitude=' . $this->longitude;
        }
        if ($this->mobilityReducedAccessibility === false) {
            $urlParameters .= '&mobilityReducedAccessibility=false';
        }
        if ($this->mobilityReducedAccessibility === true) {
            $urlParameters .= '&mobilityReducedAccessibility=true';
        }

        return $urlParameters;
    }
}
