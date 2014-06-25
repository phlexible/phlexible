<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\AccessControl;

use Phlexible\Bundle\AccessControlBundle\Rights\RightsProviderInterface;

/**
 * Teaser rights provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserRightsProvider implements RightsProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRights()
    {
        return array(
            'internal' => array(
                'teaser' => array(
                    'VIEW' => array(
                        'iconCls' => 'p-element-view-icon',
                        'bit'     => 1,
                    ),
                    'EDIT' => array(
                        'iconCls' => 'p-element-edit-icon',
                        'bit'     => 2,
                    ),
                    'CREATE' => array(
                        'iconCls' => 'p-element-add-icon',
                        'bit'     => 4,
                    ),
                    'DELETE' => array(
                        'iconCls' => 'p-element-delete-icon',
                        'bit'     => 8,
                    ),
                    'PUBLISH' => array(
                        'iconCls' => 'p-element-publish-icon',
                        'bit'     => 16,
                    ),
                    'ACCESS' => array(
                        'iconCls' => 'p-element-tab_rights-icon',
                        'bit'     => 32,
                    ),
                ),
            ),
        );
    }
}