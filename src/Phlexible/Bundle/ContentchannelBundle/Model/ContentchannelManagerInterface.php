<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\Model;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
use Phlexible\Bundle\ContentchannelBundle\Exception\InvalidArgumentException;

/**
 * Content channel manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentchannelManagerInterface
{
    /**
     * @return ContentchannelCollection
     */
    public function getCollection();

    /**
     * Find content channel
     *
     * @param int $id
     *
     * @throws InvalidArgumentException
     * @return Contentchannel
     */
    public function find($id);

    /**
     * Return content channel by unique ID
     *
     * @param string $uniqueId
     *
     * @throws InvalidArgumentException
     * @return Contentchannel
     */
    public function findByUniqueID($uniqueId);

    /**
     * Return all Content Channels
     *
     * @return Contentchannel[]
     */
    public function findAll();

    /**
     * @param Contentchannel $contentchannel
     */
    public function updateContentchannel(Contentchannel $contentchannel);
}
