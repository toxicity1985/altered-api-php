<?php

namespace Toxicity\AlteredApi\Service;

use Doctrine\ORM\EntityManagerInterface;
use Toxicity\AlteredApi\Builder\CardBuilder;
use Toxicity\AlteredApi\Contract\ObjectInterface;
use Toxicity\AlteredApi\Entity\Card;
use Toxicity\AlteredApi\Entity\Faction;
use Toxicity\AlteredApi\Entity\Set;

class CardService extends AbstractObjectService
{
    private CardBuilder $cardBuilder;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->entityClassName = Card::class;
        $this->cardBuilder = new CardBuilder($this->getRepository(Faction::class), $this->getRepository(Set::class));
    }

    public function buildFromData(array $data, string $locale = 'fr-fr'): Card
    {
        $card = $this->getRepository()->findOneBy(['reference' => $data['reference']]);

        if ($card === null) {
            $card = new Card();

        }
        return $this->cardBuilder->build($card, $data, $locale);
    }

    public function save(Card|ObjectInterface $card): void
    {
        foreach ($card->getTranslations() as $t) {
            $this->entityManager->persist($t);
        }
        parent::save($card);
    }
}
