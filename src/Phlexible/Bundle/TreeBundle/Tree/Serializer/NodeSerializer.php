<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Tree\Serializer;

use Phlexible\Bundle\AccessControlBundle\ContentObject\ContentObjectInterface;
use Phlexible\Bundle\AccessControlBundle\Permission\PermissionCollection;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\SecurityBundle\Acl\Acl;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @var PermissionCollection
     */
    private $permissions;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param ElementService           $elementService
     * @param ElementtypeService       $elementtypeService
     * @param IconResolver             $iconResolver
     * @param StateManagerInterface    $stateManager
     * @param PermissionCollection     $permissions
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        IconResolver $iconResolver,
        StateManagerInterface $stateManager,
        PermissionCollection $permissions,
        SecurityContextInterface $securityContext)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->iconResolver = $iconResolver;
        $this->stateManager = $stateManager;
        $this->permissions = $permissions;
        $this->securityContext = $securityContext;
    }

    /**
     * Serialize nodes
     *
     * @param TreeNodeInterface[] $nodes
     * @param string              $language
     *
     * @return array
     */
    public function serializeNodes(array $nodes, $language)
    {
        $return = array();

        foreach ($nodes as $node) {
            $nodeData = $this->serializeNode($node, $language);

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
     *
     * @return array
     */
    public function serializeNode(TreeNodeInterface $node, $language)
    {
        $userRights = array();
        if ($node instanceof ContentObjectInterface) {
            if (!$this->securityContext->isGranted(Acl::RESOURCE_SUPERADMIN)) {
                if ($this->securityContext->isGranted(array('right' => 'VIEW', 'language' => $language), $node)) {
                    return null;
                }

                $userRights = array(); //$contentRightsManager->getRights($language);
                $userRights = array_keys($userRights);
            } else {
                $userRights = array_keys(
                    $this->permissions->getByContentClass(get_class($node))
                );
            }
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
        $allowedElementtypeIds = array();
        foreach ($this->elementtypeService->findAllowedChildren($elementtype) as $allowedElementtype) {
            $allowedElementtypeIds[] = $allowedElementtype->getId();
        }

        $qtip = 'TID: ' . $node->getId() . '<br />' .
            'EID: ' . $element->getEid() . '<br />' .
            'Version: ' . $elementVersion->getVersion() . '<br />' .
            '<hr>' .
            'Element Type: ' . $elementtype->getTitle() . '<br />' .
            'Element Type Version: ' . $elementtype->getRevision() .
            $lockQtip;

        $data = array(
            'id'                  => $node->getID(),
            'eid'                 => $element->getEid(),
            'text'                => $elementVersion->getBackendTitle($language, $element->getMasterLanguage()),
            'icon'                => $this->iconResolver->resolveTreeNode($node, $language),
            'navigation'          => $node->getInNavigation(),
            'restricted'          => $node->getNeedAuthentication(),
            'element_type'        => $elementtype->getTitle(),
            'element_type_id'     => $elementtype->getId(),
            'element_type_type'   => $elementtype->getType(),
            'alias'               => 1, //$node->isInstance(),
            'allow_drag'          => true,
            'sort_mode'           => $node->getSortMode(),
            'areas'               => array(355),
            'allowed_et'          => $allowedElementtypeIds,
            'is_published'        => $this->stateManager->isPublished($node, $language),
            'rights'              => $userRights,
            'qtip'                => $qtip,
            'allow_children'      => $elementtype->getHideChildren() ? false : true,
            'default_tab'         => $elementtype->getDefaultTab(),
            'default_content_tab' => $elementtype->getDefaultContentTab(),
            'masterlanguage'      => $element->getMasterLanguage()
        );

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