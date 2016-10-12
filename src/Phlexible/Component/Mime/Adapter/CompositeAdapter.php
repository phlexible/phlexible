<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Mime\Adapter;

/**
 * Internet media type detector composite adapter
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class CompositeAdapter implements AdapterInterface
{
    /**
     * @var AdapterInterface[]
     */
    private $adapters = [];

    /**
     * @param AdapterInterface[] $adapters
     */
    public function __construct(array $adapters = [])
    {
        $this->setAdapters($adapters);
    }

    /**
     * Set adapters
     *
     * @param AdapterInterface[] $adapters
     *
     * @return $this
     */
    public function setAdapters(array $adapters = [])
    {
        $this->adapters = [];
        foreach ($adapters as $adapter) {
            $this->addAdapter($adapter);
        }

        return $this;
    }

    /**
     * Add adapter
     *
     * @param AdapterInterface $adapter
     *
     * @return $this
     */
    public function addAdapter(AdapterInterface $adapter)
    {
        $this->adapters[] = $adapter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable($filename)
    {
        foreach ($this->adapters as $adapter) {
            /* @var $adapter AdapterInterface */
            if ($adapter->isAvailable($filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getInternetMediaTypeStringFromFile($filename)
    {
        foreach ($this->adapters as $adapter) {
            /* @var $adapter AdapterInterface */
            if ($adapter->isAvailable($filename)) {
                $internetMediaTypeString = $adapter->getInternetMediaTypeStringFromFile($filename);
                if ($internetMediaTypeString) {
                    return $internetMediaTypeString;
                }
            }
        }

        return null;
    }
}
