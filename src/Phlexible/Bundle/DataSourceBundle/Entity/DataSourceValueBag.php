<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data source value bag
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="datasource_value", indexes={@ORM\Index(columns={"datasource_id", "language"})})
 */
class DataSourceValueBag
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(name="active_values", type="json_array")
     */
    private $activeValues = array();

    /**
     * @var string
     * @ORM\Column(name="inactive_values", type="json_array")
     */
    private $inactiveValues = array();

    /**
     * @var DataSource
     * @ORM\ManyToOne(targetEntity="DataSource", inversedBy="valueBags")
     * @ORM\JoinColumn(name="datasource_id", referencedColumnName="id")
     */
    private $datasource;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveValues()
    {
        return array_values($this->activeValues);
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setActiveValues(array $values)
    {
        $this->activeValues = $values;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addActiveValue($value)
    {
        if (!$this->hasActiveValue($value)) {
            $this->activeValues[] = $value;
            sort($this->activeValues);
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function hasActiveValue($value)
    {
        return in_array($value, $this->activeValues);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function removeActiveValue($value)
    {
        if ($this->hasActiveValue($value)) {
            unset($this->activeValues[array_search($value, $this->activeValues)]);
            sort($this->activeValues);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInactiveValues()
    {
        return array_values($this->inactiveValues);
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function setInactiveValues(array $values)
    {
        $this->inactiveValues = $values;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function addInactiveValue($value)
    {
        if (!$this->hasInactiveValue($value)) {
            $this->inactiveValues[] = $value;
            sort($this->inactiveValues);
        }

        return $this;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public function hasInactiveValue($value)
    {
        return in_array($value, $this->inactiveValues);
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function removeInactiveValue($value)
    {
        if ($this->hasInactiveValue($value)) {
            unset($this->inactiveValues[array_search($value, $this->inactiveValues)]);
            sort($this->inactiveValues);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return array_merge($this->getActiveValues(), $this->getInactiveValues());
    }

    /**
     * @return DataSource
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * @param DataSource $datasource
     *
     * @return $this
     */
    public function setDatasource(DataSource $datasource = null)
    {
        $this->datasource = $datasource;

        return $this;
    }
}
