<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree\Serializer;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\NodePermissionResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Tree interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeSerializer
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var NodePermissionResolver
     */
    private $nodePermissionResolver;

    /**
     * @param ElementService         $elementService
     * @param IconResolver           $iconResolver
     * @param StateManagerInterface  $stateManager
     * @param NodePermissionResolver $nodePermissionResolver
     */
    public function __construct(
        ElementService $elementService,
        IconResolver $iconResolver,
        StateManagerInterface $stateManager,
        NodePermissionResolver $nodePermissionResolver
    ) {
        $this->elementService = $elementService;
        $this->iconResolver = $iconResolver;
        $this->stateManager = $stateManager;
        $this->nodePermissionResolver = $nodePermissionResolver;
    }

    /**
     * Serialize nodes
     *
     * @param TreeNodeInterface[] $nodes
     * @param string              $language
     * @param TokenInterface      $token
     *
     * @return array
     */
    public function serializeNodes(array $nodes, $language, TokenInterface $token)
    {
        $return = [];

        foreach ($nodes as $node) {
            $nodeData = $this->serializeNode($node, $language, $token);

            if ($nodeData) {
                $return[] = $nodeData;
            }
        }

        return $return;
    }

    /**
     * Serialize node
     *
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param TokenInterface    $token
     *
     * @return array
     */
    public function serializeNode(TreeNodeInterface $node, $language, TokenInterface $token)
    {
        $userRights = $this->nodePermissionResolver->resolve($node, $language, $token);
        if ($userRights === null) {
            return null;
        }

        $eid = $node->getTypeId();
        $element = $this->elementService->findElement($eid);
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        //$identifier = new Makeweb_Elements_Element_Identifier($eid);
        $lockQtip = '';
        /*
        #if ($lockInfo = $lockManager->getLockInformation($identifier))
        #{
        #    if ($lockInfo['lock_uid'] == MWF_Env::getUid())
        #    {
        #        $lockQtip = '<hr>Locked by me.';
        #    }
        #    else
        #    {
        #        try
        #        {
        #            $user = MWF_Core_Users_User_Peer::getByUserID($lockInfo['lock_uid']);
        #        }
        #        catch (Exception $e)
        #        {
        #            $user = MWF_Core_Users_User_Peer::getSystemUser();
        #        }
        #
        #        $lockQtip = '<hr>Locked by '.$user->getUsername().'.';
        #    }
        #}
        */

        $elementtype = $this->elementService->findElementtype($element);
        $allowedElementtypeIds = [];
        foreach ($this->elementService->findAllowedChildren($elementtype) as $allowedElementtype) {
            $allowedElementtypeIds[] = $allowedElementtype->getId();
        }

        $qtip = 'TID: ' . $node->getId() . '<br />' .
            'EID: ' . $element->getEid() . '<br />' .
            'Version: ' . $elementVersion->getVersion() . '<br />' .
            '<hr>' .
            'Element Type: ' . $elementtype->getTitle() . '<br />' .
            'Element Type Version: ' . $elementtype->getRevision() .
            $lockQtip;

        $data = [
            'id'                  => $node->getID(),
            'eid'                 => $element->getEid(),
            'text'                => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()),
            'icon'                => $this->iconResolver->resolveTreeNode($node, $language),
            'navigation'          => $node->getInNavigation(),
            'restricted'          => $node->getNeedAuthentication(),
            'element_type'        => $elementtype->getTitle(),
            'element_type_id'     => $elementtype->getId(),
            'element_type_type'   => $elementtype->getType(),
            'alias'               => $node->getTree()->isInstance($node),
            'allow_drag'          => true,
            'sort_mode'           => $node->getSortMode(),
            'areas'               => [355],
            'allowed_et'          => $allowedElementtypeIds,
            'is_published'        => $this->stateManager->isPublished($node, $language),
            'rights'              => $userRights,
            'qtip'                => $qtip,
            'allow_children'      => $elementtype->getHideChildren() ? false : true,
            'default_tab'         => $elementtype->getDefaultTab(),
            'default_content_tab' => $elementtype->getDefaultContentTab(),
            'masterlanguage'      => $element->getMasterLanguage()
        ];

        if (count($node->getTree()->getChildren($node)) && !$elementtype->getHideChildren()) {
            $data['leaf'] = false;
            $data['expanded'] = false;
        } else {
            $data['leaf'] = true;
            $data['expanded'] = false;
        }

        if ($node->isRoot()) {
            $data['cls'] = 'siteroot-node';
            $data['expanded'] = true;
        }

        return $data;
    }
}
