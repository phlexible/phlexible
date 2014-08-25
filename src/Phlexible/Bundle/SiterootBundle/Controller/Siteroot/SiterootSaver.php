<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Controller\Siteroot;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
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
            ->applyContentchannels($siteroot, $data)
            ->applyProperties($siteroot, $data)
            ->applyCustomTitles($siteroot, $data)
            ->applyNavigations($siteroot, $data)
            ->applyShortUrls($siteroot, $data)
            ->applyUrls($siteroot, $data);

        $this->siterootManager->updateSiteroot($siteroot);

        return $siteroot;
    }

    /**
     * Apply content channels
     *
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

        $contentchannels = array();
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
     * Apply custom titles
     *
     * @param Siteroot $siteroot
     * @param array    $data
     *
     * @return $this
     */
    private function applyCustomTitles(Siteroot $siteroot, array $data)
    {
        if (!array_key_exists('customtitles', $data)) {
            // noting to save
            return $this;
        }

        $customTitlesData = $data['customtitles'];

        $siteroot->setHeadTitle($customTitlesData['head_title']);
        $siteroot->setStartHeadTitle($customTitlesData['start_head_title']);

        return $this;
    }

    /**
     * Apply custom titles
     *
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
}
