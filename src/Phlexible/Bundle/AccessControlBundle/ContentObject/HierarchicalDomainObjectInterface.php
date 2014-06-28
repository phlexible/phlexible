<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\ContentObject;

/**
 * Hierarchical domain object interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
 interface HierarchicalDomainObjectInterface extends DomainObjectInterface
{
     /**
      * Return hierarchical domain identifiers
      *
      * @return array
      */
     public function getHierarchicalDomainIdentifiers();
 }
