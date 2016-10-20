<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta data value
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaDataValue
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="set_id", type="string", length=36, options={"fixed"=true})
     */
    protected $setId;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    protected $language;

    /**
     * @var string
     * @ORM\Column(name="field_id", type="string", length=36, options={"fixed"=true})
     */
    protected $fieldId;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $value;

    /**
     * @param string $setId
     * @param string $language
     * @param string $fieldId
     */
    public function __construct($setId, $language, $fieldId)
    {
        $this->setId = $setId;
        $this->language = $language;
        $this->fieldId = $fieldId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSetId()
    {
        return $this->setId;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getFieldId()
    {
        return $this->fieldId;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
