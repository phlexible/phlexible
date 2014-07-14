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
     * @ORM\ManyToOne(targetEntity="DataSource", inversedBy="values")
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
        return $this->activeValues;
    }

    /**
     * @param array $activeValues
     *
     * @return $this
     */
    public function setActiveValues(array $activeValues)
    {
        $this->activeValues = $activeValues;

        return $this;
    }

    /**
     * @param string $activeValue
     *
     * @return $this
     */
    public function addActiveValue($activeValue)
    {
        if (!in_array($activeValue, $this->activeValues)) {
            $this->activeValues[] = $activeValue;
        }

        return $this;
    }

    /**
     * @param string $activeValue
     *
     * @return $this
     */
    public function removeActiveValue($activeValue)
    {
        if (in_array($activeValue, $this->activeValues)) {
            unset($this->activeValues[array_search($activeValue, $this->activeValues)]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getInactiveValues()
    {
        return $this->inactiveValues;
    }

    /**
     * @param array $inactiveValues
     *
     * @return $this
     */
    public function setInactiveValues(array $inactiveValues)
    {
        $this->inactiveValues = $inactiveValues;

        return $this;
    }

    /**
     * @param string $inactiveValue
     *
     * @return $this
     */
    public function addInactiveValue($inactiveValue)
    {
        if (!in_array($inactiveValue, $this->inactiveValues)) {
            $this->inactiveValues[] = $inactiveValue;
        }

        return $this;
    }

    /**
     * @param string $inactiveValue
     *
     * @return $this
     */
    public function removeInactiveValue($inactiveValue)
    {
        if (in_array($inactiveValue, $this->inactiveValues)) {
            unset($this->activeValues[array_search($inactiveValue, $this->inactiveValues)]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->getActiveValues() + $this->getInactiveValues();
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
