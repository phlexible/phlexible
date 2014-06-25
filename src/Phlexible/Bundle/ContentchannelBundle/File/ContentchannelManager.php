<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
use Phlexible\Bundle\ContentchannelBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ContentchannelBundle\Model\ContentchannelCollection;
use Phlexible\Bundle\ContentchannelBundle\Model\ContentchannelManagerInterface;

/**
 * Content channel repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelRepository implements ContentchannelManagerInterface
{
    /**
     * @var ContentchannelLoader
     */
    private $loader;

    /**
     * @var ContentchannelDumper
     */
    private $dumper;

    /**
     * @var ContentchannelCollection
     */
    private $contentchannels;

    /**
     * @param ContentchannelLoader $loader
     * @param ContentchannelDumper $dumper
     */
    public function __construct(ContentchannelLoader $loader, ContentchannelDumper $dumper)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
    }

    /**
     * @return ContentchannelCollection
     */
    public function getCollection()
    {
        if ($this->contentchannels === null) {
            $this->contentchannels = $this->loader->loadContentchannels();
        }

        return $this->contentchannels;
    }

    /**
     * Find content channel
     *
     * @param integer $id
     *
     * @throws InvalidArgumentException
     * @return Contentchannel
     */
    public function find($id)
    {
        $contentchannel = $this->getCollection()->get($id);

        if ($contentchannel) {
            return $contentchannel;
        }

        throw new InvalidArgumentException('Content channel not found.');
    }

    /**
     * Return content channel by unique ID
     *
     * @param string $uniqueId
     *
     * @throws InvalidArgumentException
     * @return Contentchannel
     */
    public function findByUniqueID($uniqueId)
    {
        $contentchannel = $this->getCollection()->getByUniqueId($uniqueId);

        if ($contentchannel) {
            return $contentchannel;
        }

        throw new InvalidArgumentException('Content channel not found.');
    }

    /**
     * Return all Content Channels
     *
     * @return Contentchannel[]
     */
    public function findAll()
    {
        return $this->getCollection()->getAll();
    }

    /**
     * @param Contentchannel $contentchannel
     */
    public function updateContentchannel(Contentchannel $contentchannel)
    {
        $this->dumper->dump($contentchannel);
    }
}
