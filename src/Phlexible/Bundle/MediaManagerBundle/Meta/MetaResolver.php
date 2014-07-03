<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Media_Manager
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 2608 2007-02-23 15:57:36Z swentz $
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Component\Util\ArrayUtil;

/**
 * Meta resolver
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class MetaResolver
{
    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @var FolderMetaManager
     */
    private $folderMetaManager;

    /**
     * @var array
     */
    private $metaLanguages;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param SiteManager       $siteManager
     * @param FolderMetaManager $folderMetaManager
     * @param string            $languages
     * @param string            $defaultLanguage
     */
    public function __construct(
        SiteManager $siteManager,
        FolderMetaManager $folderMetaManager,
        $languages,
        $defaultLanguage)
    {
        $this->siteManager = $siteManager;
        $this->folderMetaManager = $folderMetaManager;
        $this->metaLanguages = explode(',', $languages);
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * Get meta data of a file.
     *
     * @param string $fileId
     * @param int    $fileVersion
     *
     * @return array
     */
    public function getFileMeta($fileId, $fileVersion = -1)
    {
        $site = $this->siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $setsDefaultLanguage = $asset->getMetas(); //($this->defaultLanguage);
        $setsSlaveLanguages = array();

        foreach ($this->metaLanguages as $metaSlaveLanguage) {
            if ($metaSlaveLanguage !== $this->defaultLanguage) {
                $setsSlaveLanguages[$metaSlaveLanguage] = $asset->getMeta($metaSlaveLanguage);
            }
        }

        return $this->_getMeta($setsDefaultLanguage, $setsSlaveLanguages);
    }

    /**
     * Get meta data of a folder.
     *
     * @param string $folderId
     *
     * @return array
     */
    public function getFolderMeta($folderId)
    {
        $setsDefaultLanguage =
            $this->folderMetaManager->getMeta($folderId, $this->defaultLanguage);

        $setsSlaveLanguages = array();

        foreach ($this->metaLanguages as $metaSlaveLanguage) {
            if ($metaSlaveLanguage !== $this->defaultLanguage) {
                $setsSlaveLanguages[$metaSlaveLanguage] =
                    $this->folderMetaManager->getMeta($folderId, $metaSlaveLanguage);
            }
        }

        return $this->_getMeta($setsDefaultLanguage, $setsSlaveLanguages);
    }

    /**
     * @param MetaBag $setsDefaultLanguage
     * @param array   $setsSlaveLanguages
     *
     * @return array
     */
    protected function _getMeta(MetaBag $setsDefaultLanguage, array $setsSlaveLanguages)
    {
        $meta = array();

        foreach ($setsDefaultLanguage->getAll() as $name => $metaData) {
            $set = array(
                'id'     => $name,
                'name'   => $name,
                'values' => array()
            );
            foreach ($metaData->getValues() as $key => $value) {
                $set['values'][] = array(
                    'type'         => 'text',
                    'options'      => array(),
                    'required'     => false,
                    'synchronized' => false,
                    'readonly'     => false,
                    'key'          => $key,
                    'tkey'         => $key,
                    'value_de'     => $value,
                    'value_en'     => $value,
                );
            }
            $meta[] = $set;
        }

        return $meta;

        foreach (array_values($setsDefaultLanguage) as $metaKey => $metaRow) {
            $metaRow['set_id'] = $metaRow['setId'];
            $metaRow['value_' . $this->defaultLanguage] = $metaRow['value'];
            unset($metaRow['setId']);
            unset($metaRow['value']);

            if ($metaRow['type'] == 'select') {
                $util = new ArrayUtil();
                $metaRow['options'] = $util->keyToValue($metaRow['options']);
            } elseif ($metaRow['type'] == 'suggest') {
                $metaRow['options']['values_' . $this->defaultLanguage]
                    = $metaRow['options']['values'];

                unset($metaRow['options']['values']);
            }

            $meta[$metaKey] = $metaRow;
        }

        foreach ($setsSlaveLanguages as $slaveLanguage => $slaveMetaItems) {
            foreach (array_values($slaveMetaItems) as $metaKey => $metaRow) {
                if (!empty($metaRow['value'])) {
                    $meta[$metaKey]['value_' . $slaveLanguage] = $metaRow['value'];
                }

                if ($metaRow['type'] == 'suggest') {
                    $meta[$metaKey]['options']['values_' . $slaveLanguage]
                        = $metaRow['options']['values'];
                }
            }
        }

        return $meta;
    }

}
