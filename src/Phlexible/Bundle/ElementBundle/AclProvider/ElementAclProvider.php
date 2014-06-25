<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\AclProvider;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProvider;

/**
 * Element acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementAclProvider extends AclProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array(
            'elements',
            'elements_publish',
            'elements_create',
            'elements_delete',
            'elements_accordion_children',
            'elements_accordion_page',
            'elements_accordion_page_advanced',
            'elements_accordion_teaser',
            'elements_accordion_versions',
            'elements_accordion_instances',
            'elements_accordion_comment',
            'elements_accordion_context',
            'elements_accordion_meta',
        );
    }
}