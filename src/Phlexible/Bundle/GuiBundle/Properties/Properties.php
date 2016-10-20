<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Properties;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\GuiBundle\Entity\Property;

/**
 * Properties
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Properties
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Property[]
     */
    private $properties;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get property
     *
     * @param string $component
     * @param string $name
     * @param string $defaultValue
     *
     * @return string
     */
    public function get($component, $name, $defaultValue = null)
    {
        $this->load();

        $propertyKey = sprintf('%s__%s', $component, $name);

        if (isset($this->properties[$propertyKey])) {
            return $this->properties[$propertyKey]->getValue();
        }

        return $defaultValue;
    }

    /**
     * Set property
     *
     * @param string $component
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function set($component, $name, $value)
    {
        $this->load();

        $propertyKey = sprintf('%s__%s', $component, $name);

        if (isset($this->properties[$propertyKey])) {
            $property = $this->properties[$propertyKey];
        } else {
            $property = new Property();
            $property
                ->setComponent($component)
                ->setName($name);
            $this->entityManager->persist($property);
            $this->properties[$propertyKey] = $property;
        }

        $property->setValue($value);

        $this->entityManager->flush($property);

        return $this;
    }

    /**
     * Remove property
     *
     * @param string $component
     * @param string $name
     *
     * @return $this
     */
    public function remove($component, $name)
    {
        $this->load();

        $propertyKey = sprintf('%s__%s', $component, $name);

        if (isset($this->properties[$propertyKey])) {
            $this->entityManager->remove($this->properties[$propertyKey]);
            unset($this->properties[$propertyKey]);
        }

        return $this;

    }

    /**
     * Is property set?
     *
     * @param string $component
     * @param string $name
     *
     * @return bool
     */
    public function has($component, $name)
    {
        $this->load();

        return (bool) mb_strlen($this->get($component, $name));
    }

    private function load()
    {
        if ($this->properties !== null) {
            return;
        }

        $repository = $this->entityManager->getRepository('PhlexibleGuiBundle:Property');

        $properties = [];
        foreach ($repository->findAll() as $property) {
            $id = sprintf('%s__%s', $property->getComponent(), $property->getName());

            $properties[$id] = $property;
        }

        $this->properties = $properties;
    }
}
