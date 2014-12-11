<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

use Phlexible\Component\MediaTemplate\Exception\InvalidArgumentException;

/**
 * Abstract template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractTemplate implements TemplateInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $cache = false;

    /**
     * @var bool
     */
    private $system = false;

    /**
     * @var string
     */
    private $storage = 'default';

    /**
     * @var int
     */
    private $revision = 0;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $modifiedAt;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * {@inheritdoc}
     */
    public function setCache($cache = true)
    {
        $this->cache = (bool) $cache;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSystem()
    {
        return $this->system;
    }

    /**
     * {@inheritdoc}
     */
    public function setSystem($system = true)
    {
        $this->system = (bool) $system;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * {@inheritdoc}
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * {@inheritdoc}
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedAt(\DateTime $modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters, $strict = true)
    {
        foreach ($parameters as $key => $value) {
            $this->setParameter($key, $value, $strict);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function setParameter($key, $value, $strict = true)
    {
        $allowedParameters = $this->getAllowedParameters();
        if (!in_array($key, $allowedParameters)) {
            if ($strict) {
                throw new InvalidArgumentException("Parameter $key not allowed in " . get_class($this) . ".");
            }

            return $this;
        }

        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($key, $defaultValue = null)
    {
        if (!isset($this->parameters[$key])) {
            $defaultParameters = $this->getDefaultParameters();
            if (isset($defaultParameters[$key])) {
                return $defaultParameters[$key];
            }

            if ($defaultValue !== null) {
                return $defaultValue;
            }

            return null;
        }

        return $this->parameters[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($key, $notEmpty = false)
    {
        return isset($this->parameters[$key]) && (!$notEmpty || $this->parameters[$key]);
    }
}
