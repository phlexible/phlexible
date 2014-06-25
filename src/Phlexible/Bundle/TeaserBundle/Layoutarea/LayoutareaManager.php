<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Layoutarea;

/**
 * Layoutarea manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LayoutareaManager
{
    /**
     */
    public function __construct()
    {

    }

    /**
     * Return all Teasers for the given EID
     *
     * @param $elementTypeID string
     *
     * @return array
     */
    public function getFor($elementTypeID)
    {
        $elementTypeManager = Makeweb_Elementtypes_Elementtype_Manager::getInstance();

        $layoutElementTypes = $elementTypeManager->getByType(Makeweb_Elementtypes_Elementtype_Version::TYPE_LAYOUTAREA);

        $layoutAreas = array();

        foreach ($layoutElementTypes as $layoutElementTypeID => $layoutElementType)
        {
            $layoutElementTypeVersion = $layoutElementType->getVersion();
            $viabilityIDs = $layoutElementTypeVersion->getViabilityIDs();

            if (!in_array($elementTypeID, $viabilityIDs))
            {
                continue;
            }

//            $layoutAreas[$layoutElementTypeID] = new Makeweb_Teasers_Layoutarea($layoutElementTypeVersion);
            $layoutAreas[$layoutElementTypeID] = $layoutElementType->getLatest();
        }

        return $layoutAreas;
    }

}

