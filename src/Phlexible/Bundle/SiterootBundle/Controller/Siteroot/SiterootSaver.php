<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller\Siteroot;

use Phlexible\Bundle\SiterootBundle\Entity\Navigation;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot saver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootSaver
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * Save siteroot
     *
     * @param Request $request
     *
     * @return Siteroot
     */
    public function saveAction(Request $request)
    {
        $siterootId = $request->get('id');
        $data = json_decode($request->get('data'), true);

        $siteroot = $this->siterootManager->find($siterootId);

        $this
            ->applyTitles($siteroot, $data)
            ->applyContentchannels($siteroot, $data)
            ->applyProperties($siteroot, $data)
            ->applyNamedTids($siteroot, $data)
            ->applyNavigations($siteroot, $data)
            ->applyUrls($siteroot, $data);

        $this->siterootManager->updateSiteroot($siteroot);

        return $siteroot;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyTitles(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('titles', $data)) {
            // noting to save
            return $this;
        }

        $siteroot->setTitles($data['titles']);

        return $this;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyContentchannels(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('contentchannels', $data)) {
            // noting to save
            return $this;
        }

        $contentchannelsData = $data['contentchannels'];

        $contentchannels = [];
        foreach ($contentchannelsData as $row) {
            if (!$row['used']) {
                continue;
            }

            $contentchannels[$row['contentchannel_id']] = $row['default'] ? true : false;
        }

        $siteroot->setContentChannels($contentchannels);

        return $this;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyProperties(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('properties', $data)) {
            // noting to save
            return $this;
        }

        $propertiesData = $data['properties'];

        $siteroot->setProperties($propertiesData);

        return $this;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyNamedTids(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('specialtids', $data)) {
            // noting to save
            return $this;
        }

        $specialTidsData = $data['specialtids'];

        $specialTids = [];
        foreach ($specialTidsData as $row) {
            $specialTids[] = [
                'name'     => $row['key'],
                'language' => !empty($row['language']) ? $row['language'] : null,
                'treeId'   => $row['tid'],
            ];
        }
        $siteroot->setSpecialTids($specialTids);

        return $this;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyNavigations(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('navigations', $data)) {
            // noting to save
            return $this;
        }

        $navigationData = $data['navigations'];

        foreach ($navigationData['created'] as $row) {
            $navigation = new Navigation();
            $navigation
                ->setSiteroot($siteroot)
                ->setAdditional(!empty($row['additional']) ? $row['additional'] : null)
                ->setFlags($row['flags'])
                ->setStartTreeId($row['start_tid'])
                ->setTitle($row['title'])
                ->setMaxDepth($row['max_depth']);

            $siteroot->addNavigation($navigation);
        }

        foreach ($navigationData['modified'] as $row) {
            foreach ($siteroot->getNavigations() as $navigation) {
                if ($navigation->getId() === $row['id']) {
                    $navigation
                        ->setSiteroot($siteroot)
                        ->setAdditional(!empty($row['additional']) ? $row['additional'] : null)
                        ->setFlags($row['flags'])
                        ->setStartTreeId($row['start_tid'])
                        ->setTitle($row['title'])
                        ->setMaxDepth($row['max_depth']);

                    break;
                }
            }
        }

        foreach ($navigationData['deleted'] as $id) {
            foreach ($siteroot->getNavigations() as $navigation) {
                if ($navigation->getId() === $id) {
                    $siteroot->removeNavigation($navigation);
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyUrls(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('mappings', $data)) {
            // noting to save
            return $this;
        }

        $urlsData = $data['mappings'];

        foreach ($urlsData['created'] as $row) {
            $url = new Url();
            $url
                ->setSiteroot($siteroot)
                ->setDefault($row['default'])
                ->setGlobalDefault($row['global_default'])
                ->setHostname($row['hostname'])
                ->setLanguage($row['language'])
                ->setTarget($row['target']);

            $siteroot->addUrl($url);
        }

        foreach ($urlsData['modified'] as $row) {
            foreach ($siteroot->getUrls() as $url) {
                if ($url->getId() === $row['id']) {
                    $url
                        ->setSiteroot($siteroot)
                        ->setDefault($row['default'])
                        ->setGlobalDefault($row['global_default'])
                        ->setHostname($row['hostname'])
                        ->setLanguage($row['language'])
                        ->setTarget($row['target']);

                    break;
                }
            }
        }

        foreach ($urlsData['deleted'] as $id) {
            foreach ($siteroot->getUrls() as $url) {
                if ($url->getId() === $id) {
                    $siteroot->removeUrl($url);
                    break;
                }
            }
        }

        return $this;
    }
}
