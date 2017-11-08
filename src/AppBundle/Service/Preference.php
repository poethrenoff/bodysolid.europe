<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Preference as PreferenceEntity;

class Preference
{
    /**
     * @var array
     */
    protected $preferences;

    /**
     * Preference constructor
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $preferences = $entityManager->getRepository(PreferenceEntity::class)->findAll();
        foreach ($preferences as $preference) {
            $this->preferences[$preference->getName()] = $preference->getValue();
        }
    }

    /**
     * Get preference
     *
     * @param  string $name
     * @param  string|null $default
     *
     * @return string
     */
    public function get(string $name, string $default = null)
    {
        return $this->preferences[$name] ?? $default;
    }
}