<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\Model;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;

/**
 * Content channel collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelCollection
{
    /**
     * @var Contentchannel[]
     */
    private $contentchannels = [];

    /**
     * @var array
     */
    private $uniqueIdMap = [];

    /**
     * Add template
     *
     * @param Contentchannel $contentchannel
     *
     * @return $this
     */
    public function add(Contentchannel $contentchannel)
    {
        $this->contentchannels[$contentchannel->getId()] = $contentchannel;
        $this->uniqueIdMap[$contentchannel->getUniqueId()] = $contentchannel->getId();

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Contentchannel
     */
    public function get($id)
    {
        if (isset($this->contentchannels[$id])) {
            return $this->contentchannels[$id];
        }

        return null;
    }

    /**
     * @param string $uniqueId
     *
     * @return Contentchannel
     */
    public function getByUniqueId($uniqueId)
    {
        if (isset($this->uniqueIdMap[$uniqueId])) {
            return $this->get($this->uniqueIdMap[$uniqueId]);
        }

        return null;
    }

    /**
     * @return Contentchannel[]
     */
    public function getAll()
    {
        return $this->contentchannels;
    }
}
