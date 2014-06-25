<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Link field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LinkField extends AbstractField
{
    protected $icon = 'p-elementtype-field_link-icon';

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $match = null;
        $language = $this->_language ? $this->_language : MWF_Registry::getContainer()->getParam(':phlexible_cms.languages.default');

        if (preg_match('/^(id|sr):(\d+)(,\d+)?(;newWindow)?$/', $item['data_content'], $match))
        {
            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
            $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();

            $type = $match[1];
            $id   = $match[2];
            $eid  = null;
            if (!empty($match[3]))
            {
                $eid = substr($match[3], 1);
            }
            $newWindow = false;
            if (!empty($match[4]))
            {
                $newWindow = true;
            }

            try
            {
                $node = $treeManager->getNodeByNodeID($id);

                if ($node)
                {
                    $siteRoot   = $node->getTree()->getSiteRoot();
                    $defaultUrl = $siteRoot->getDefaultUrl();

                    $eid = $node->getEid();

                    if ($node->isPublished($language))
                    {
                        $elementVersion = $elementVersionManager->get($eid, $node->getOnlineVersion($language));
                    }
                    else
                    {
                        $elementVersion = $elementVersionManager->getLatest($eid);
                    }

                    $item['displayContent'] = $elementVersion->getBackendTitle($language) . ' [' . $id . ']';

                    $item['link'] = array(
                        'type'        => 'eid',
                        'id'          => $id,
                        'eid'         => $eid,
                        'siteroot_id' => $siteRoot->getId(),
                        'scheme'      => $node->isHttps($elementVersion->getVersion()) ? 'https' : 'http',
                        'host'        => $defaultUrl->getUrl(),
                        'path'        => $defaultUrl->getPath(),
                        'title'       => $elementVersion->getBackendTitle($language),
                        'new_window'  => $newWindow,
                    );

                    if ($type == 'sr')
                    {
                        $item['link']['type'] = 'intrasiteroot';
                    }
                }
            }
            catch(Exception $e)
            {
                $item['data_content']   = '';
                $item['displayContent'] = '';
            }
        }
        elseif (preg_match('/^(newWindow;)?([a-z][a-z];)?(http[s]{0,1}:\/\/.*?)$/', $item['data_content'], $match))
        {
            $newWindow = !empty($match[1]) ? true : false;
            $language  = !empty($match[2]) ? substr($match[3], 0, 2) : false;
            $url       = $match[3];

            $item['displayContent'] = $url;

            $item['link'] = array(
                'type'       => 'url',
                'url'        => $url,
                'new_window' => $newWindow,
                'language'   => substr($language, 1, 2),
            );
        }
        elseif (preg_match('/^mailto:(.*$)/', $item['data_content'], $match))
        {
            $item['displayContent'] = $item['data_content'];

            $item['link'] = array(
                'type'   => 'mailto',
                'mailto' => $item['data_content'],
            );

            $item['rawContent'] = $item['data_content'];
            $item['content']    = $match[1];
        }
        else
        {
            $item['data_content']   = '';
            $item['displayContent'] = '';
        }

        return $item;
    }
}
