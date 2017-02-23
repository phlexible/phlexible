<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle;

use Phlexible\Bundle\ElementBundle\DependencyInjection\Compiler\AddFieldMappersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Element bundle.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleElementBundle extends Bundle
{
    const RESOURCE_ELEMENTS = 'elements';
    const RESOURCE_ELEMENTS_PUBLISH = 'elements_publish';
    const RESOURCE_ELEMENTS_DELETE = 'elements_delete';
    const RESOURCE_ELEMENTS_CREATE = 'elements_create';
    const RESOURCE_ELEMENTS_ACCORDION_CHILDREN = 'elements_accordion_children';
    const RESOURCE_ELEMENTS_ACCORDION_PAGE = 'elements_accordion_page';
    const RESOURCE_ELEMENTS_ACCORDION_PAGE_ADVANCED = 'elements_accordion_page_advanced';
    const RESOURCE_ELEMENTS_ACCORDION_TEASER = 'elements_accordion_teaser';
    const RESOURCE_ELEMENTS_ACCORDION_VERSIONS = 'elements_accordion_versions';
    const RESOURCE_ELEMENTS_ACCORDION_INSTANCES = 'elements_accordion_instances';
    const RESOURCE_ELEMENTS_ACCORDION_COMMENT = 'elements_accordion_comment';
    const RESOURCE_ELEMENTS_ACCORDION_CONTEXT = 'elements_accordion_context';
    const RESOURCE_ELEMENTS_ACCORDION_META = 'elements_accordion_meta';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new AddFieldMappersPass());
    }
}
