<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Usage\Usage;

/**
 * Elementtype update change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UpdateChange extends Change
{
    /**
     * @var bool
     */
    private $needImport = false;

    /**
     * @var ElementSource[]
     */
    private $outdatedElementSources = [];

    /**
     * @param Elementtype     $elementtype
     * @param Usage[]         $usage
     * @param bool            $needImport
     * @param ElementSource[] $outdatedElementSources
     */
    public function __construct(Elementtype $elementtype, array $usage, $needImport, array $outdatedElementSources)
    {
        parent::__construct($elementtype, $usage);

        $this->needImport = $needImport;

        foreach ($outdatedElementSources as $outdatedElementSource) {
            $this->addOutdatedElementSource($outdatedElementSource);
        }
    }

    /**
     * @return bool
     */
    public function getNeedImport()
    {
        return $this->needImport;
    }

    /**
     * @param ElementSource $outdatedElementSource
     *
     * @return $this
     */
    public function addOutdatedElementSource(ElementSource $outdatedElementSource)
    {
        $this->outdatedElementSources[] = $outdatedElementSource;

        return $this;
    }

    /**
     * @return ElementSource[]
     */
    public function getOutdatedElementSources()
    {
        return $this->outdatedElementSources;
    }

    /**
     * {@inheritdoc}
     */
    public function getReason()
    {
        return 'Elementtype updated';
    }
}
