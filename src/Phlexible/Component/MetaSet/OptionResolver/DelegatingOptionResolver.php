<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\OptionResolver;

use Phlexible\Component\MetaSet\Domain\MetaSetField;

/**
 * Delegating option resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingOptionResolver implements OptionResolverInterface
{
    /**
     * @var OptionResolverInterface[]
     */
    private $optionResolvers = array();

    /**
     * @param OptionResolverInterface[] $optionResolvers
     */
    public function __construct(array $optionResolvers = array())
    {
        foreach ($optionResolvers as $type => $optionResolver) {
            $this->addOptionResolver($type, $optionResolver);
        }
    }

    /**
     * @param string                  $type
     * @param OptionResolverInterface $optionResolver
     */
    public function addOptionResolver($type, OptionResolverInterface $optionResolver)
    {
        $this->optionResolvers[$type] = $optionResolver;
    }

    /**
     * @param MetaSetField $field
     *
     * @return null|array
     */
    public function resolve(MetaSetField $field)
    {
        $type = $field->getType();

        if (isset($this->optionResolvers[$type])) {
            return $this->optionResolvers[$type]->resolve($field);
        }

        return null;
    }
}
