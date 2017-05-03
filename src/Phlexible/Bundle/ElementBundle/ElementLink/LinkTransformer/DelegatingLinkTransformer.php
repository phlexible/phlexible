<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink\LinkTransformer;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;

/**
 * Link transformer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DelegatingLinkTransformer implements LinkTransformerInterface
{
    /**
     * @var LinkTransformerInterface[]
     */
    private $transformers = [];

    /**
     * @param LinkTransformerInterface[] $transformers
     */
    public function __construct(array $transformers = [])
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }
    }

    /**
     * @param LinkTransformerInterface $transformer
     */
    private function addTransformer(LinkTransformerInterface $transformer)
    {
        $this->transformers[] = $transformer;
    }

    public function supports(ElementLink $elementLink)
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($elementLink)) {
                return true;
            }
        }

        return false;
    }

    public function transform(ElementLink $elementLink, array $data)
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($elementLink)) {
                $data = $transformer->transform($elementLink, $data);
            }
        }

        return $data;
    }
}
