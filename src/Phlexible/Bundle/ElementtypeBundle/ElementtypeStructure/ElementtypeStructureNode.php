<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure;

/**
 * Elementtype structure node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureNode
{
    const FIELD_TYPE_REFERENCE = 'reference';
    const FIELD_TYPE_REFERENCE_ROOT = 'referenceroot';

    /**
     * @var int
     */
    private $id;

    /**
     * @var ElementtypeStructure
     */
    private $elementtypeStructure;

    /**
     * @var string
     */
    private $dsId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment = null;

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $parentId;

    /**
     * @var string
     */
    private $parentDsId;

    /**
     * @var int
     */
    private $referenceId;

    /**
     * @var int
     */
    private $referenceVersion;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var array
     */
    private $validation;

    /**
     * @var array
     */
    private $labels;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $contentChannels;
}

