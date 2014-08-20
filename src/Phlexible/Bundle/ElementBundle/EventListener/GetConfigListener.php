<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\GetConfigEvent;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Get config listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetConfigListener
{
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetConfigEvent $event
     */
    public function onGetConfig(GetConfigEvent $event)
    {
        $container = $this->container;

        $config = $event->getConfig();

        $config->set(
            'elements.publish.comment_required',
            (bool) $container->getParameter('phlexible_element.publish.comment_required')
        );
        $config->set(
            'elements.publish.confirm_required',
            (bool) $container->getParameter('phlexible_element.publish.confirm_required')
        );
        $config->set(
            'elements.create.use_multilanguage',
            (bool) $container->getParameter('phlexible_element.create.use_multilanguage')
        );
        $config->set(
            'elements.create.restricted',
            (bool) $container->getParameter('phlexible_element.create.restricted')
        );

        $siterootManager = $container->get('phlexible_siteroot.siteroot_manager');
        $treeManager = $container->get('phlexible_tree.tree_manager');
        $securityContext = $container->get('security.context');

        $siteroots = $siterootManager->findAll();
        $allLanguages = explode(',', $container->getParameter('phlexible_cms.languages.available'));

        $siterootLanguages = array();
        $siterootConfig = array();

        foreach ($siteroots as $siteroot) {
            $siterootId = $siteroot->getId();

            if ($securityContext->isGranted(Acl::RESOURCE_SUPERADMIN) ||
                $securityContext->isGranted(Acl::RESOURCE_DEVELOPMENT)
            ) {
                $siterootLanguages[$siterootId] = $allLanguages;
            } else {
                $siterootLanguages[$siterootId] = array();

                foreach ($allLanguages as $language) {
                    $tree = $treeManager->getBySiterootId($siterootId);
                    $root = $tree->getRoot();

                    if (!$securityContext->isGranted($root, 'VIEW', $language)) {
                        continue;
                    }

                    $siterootLanguages[$siterootId][] = $language;
                }
            }

            if (count($siterootLanguages[$siterootId])) {
                $siterootConfig[$siterootId] = array(
                    'id' => $siteroot->getId(),
                    'title' => $siteroot->getTitle(),
                );
            }
        }

        $config->set('user.siteroot.languages', $siterootLanguages);

        $config->set('siteroot.config', $siterootConfig);
    }
}
