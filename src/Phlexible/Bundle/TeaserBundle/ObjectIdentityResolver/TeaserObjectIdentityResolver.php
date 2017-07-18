<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\ObjectIdentityResolver;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Component\AccessControl\Domain\HierarchicalObjectIdentity;
use Phlexible\Component\AccessControl\ObjectIdentityResolver\ObjectIdentityResolverInterface;

/**
 * Folder object identity resolver.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserObjectIdentityResolver implements ObjectIdentityResolverInterface
{
    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @param TeaserManagerInterface $teaserManager
     */
    public function __construct(TeaserManagerInterface $teaserManager)
    {
        $this->teaserManager = $teaserManager;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($objectType, $objectId)
    {
        if ($objectType !== Teaser::class) {
            return null;
        }

        $teaser = $this->teaserManager->find($objectId);

        return $objectIdentity = HierarchicalObjectIdentity::fromDomainObject($teaser);
    }
}
