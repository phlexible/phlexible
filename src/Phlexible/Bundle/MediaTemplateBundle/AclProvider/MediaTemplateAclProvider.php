<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\AclProvider;

use Phlexible\Bundle\SecurityBundle\Acl\AclProvider\AclProvider;

/**
 * Media template acl provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTemplateAclProvider extends AclProvider
{
    /**
     * {@inheritdoc}
     */
    public function provideResources()
    {
        return array(
            'mediatemplates',
        );
    }
}