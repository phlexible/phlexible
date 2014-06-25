<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Field;

/**
 * Base field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class Field
{
    /**
     * @var boolean
     */
    protected $isField = false;

    /**
     * @var boolean
     */
    protected $isContainer = false;

    /**
     * @var boolean
     */
    protected $hasContent = false;

    /**
     * @var boolean
     */
    protected $hasOptions = false;

    /**
     * @var string
     */
    protected $icon = 'p-elementtypes-field_fallback.gif';

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return boolean
     */
    public function isContainer()
    {
        return $this->isContainer;
    }

    /**
     * @return boolean
     */
    public function isField()
    {
        return $this->isField;
    }

    /**
     * @return boolean
     */
    public function hasContent()
    {
        return $this->hasContent;
    }

    /**
     * @return boolean
     */
    public function hasOptions()
    {
        return $this->hasOptions;
    }

    /**
     * Transforms old field types to new elementar types
     *
     * @param array   $item
     * @param integer $eid
     * @param integer $version
     * @param string  $language
     *
     * @return array
     */
    public function transform($item, $eid, $version, $language)
    {
        $this->_eid      = $eid;
        $this->_version  = $version;
        $this->_language = $language;

        $item['configuration'] = !empty($item['configuration']) ? ($item['configuration']) : array();

        $labels          = !empty($item['labels']) ? ($item['labels']) : array();
        $validation      = !empty($item['validation']) ? ($item['validation']) : array();
        $options         = !empty($item['options']) ? ($item['options']) : array();
        $media           = !empty($item['media']) ? ($item['media']) : array();
        $contentChannels = !empty($item['content_channels']) ? ($item['content_channels']) : array();

        unset($item['labels']);
//        unset($item['validation']);
        unset($item['options']);
        unset($item['media']);
        unset($item['content_channels']);

//        if($validation === null)
//        {
//            $validation = array();
//        }

        if($options === null)
        {
            $options = array();
        }

        if($media === null)
        {
            $media = array();
        }

//        $item['validation'] = $validation;

        $interfaceLang = 'de';//MWF_Env::getUser()->getInterfaceLanguage();

        $item['name'] = array();
        if (!empty($labels['fieldlabel']))
        {
            $item['name'] = $labels['fieldlabel'];
        }

        $item['boxlabel'] = array();
        if (!empty($labels['boxlabel']))
        {
            $item['boxlabel'] = $labels['boxlabel'];
        }

        $item['help'] = array();
        if (!empty($labels['context_help']))
        {
            $item['help'] = $labels['context_help'];
        }

        $item['prefix'] = array();
        if (!empty($labels['prefix']))
        {
            $item['prefix'] = $labels['prefix'];
        }

        $item['suffix'] = array();
        if (!empty($labels['suffix']))
        {
            $item['suffix'] = $labels['suffix'];
        }

        // call field specific transformations
//        $field = self::get($item['type']);
        $item = $this->_transform($item, $media, $options);

        if (!array_key_exists('content', $item))
        {
            if (strlen($item['data_content']))
            {
                $item['content'] = $item['data_content'];
            }
            else
            {
                $item['content'] = '';
            }
        }

        if (!array_key_exists('rawContent', $item))
        {
            if (strlen($item['data_content']))
            {
                $item['rawContent'] = $item['data_content'];
            }
            else
            {
                $item['rawContent'] = '';
            }
        }

        unset($item['data_content']);

        return $item;
    }

    public function transformSave($value, $eid, $version, $language)
    {
        if (is_array($value))
        {
            $value = implode(',', $value);
        }

        return $value;
    }

    public function postSave($value, $structureNode, $eid, $version, $language)
    {

    }

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    abstract protected function _transform(array $item, array $media, array $options);
}