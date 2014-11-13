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
        return [
            'internal' => [
                'teaser' => [
                    'VIEW' => [
                        'iconCls' => 'p-element-view-icon',
                        'bit'     => 1,
                    ],
                    'EDIT' => [
                        'iconCls' => 'p-element-edit-icon',
                        'bit'     => 2,
                    ],
                    'CREATE' => [
                        'iconCls' => 'p-element-add-icon',
                        'bit'     => 4,
                    ],
                    'DELETE' => [
                        'iconCls' => 'p-element-delete-icon',
                        'bit'     => 8,
                    ],
                    'PUBLISH' => [
                        'iconCls' => 'p-element-publish-icon',
                        'bit'     => 16,
                    ],
                    'ACCESS' => [
                        'iconCls' => 'p-element-tab_rights-icon',
                        'bit'     => 32,
                    ],
                ],
            ],
        ];
    }
}
