<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

/**
 * Video field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoField extends AbstractField
{
    const TYPE = 'video';
    public $options = false;
    public $icon = 'p-frontendmedia-field_video-icon';

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $item['templates']        = array();
        $item['templates_config'] = array();
        $item['media']            = array();

        try
        {
            if (!empty($item['data_content']))
            {
                $file = $this->_getFile($item['data_content']);

                if ($file !== null)
                {
                    $item['media']  = $this->_getMediaData($file);
                    $item['master'] = $this->_getMasterData($item);

                    $item['media']['media'] = BASE_URL . '/media/' . $file->getId(). '/' . $file->getName();

                    try
                    {
                        $item['templates'] = array_merge($item['templates'], $this->_getImageTemplates($item, $media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }

                    try
                    {
                        $item['templates'] = array_merge($item['templates'], $this->_getVideoTemplates($item, $media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }

                    try
                    {
                        $item['templates'] = array_merge($item['templates'], $this->_getAudioTemplates($item, $media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }

                    try
                    {
                        $item['templates_config'] = array_merge($item['templates_config'], $this->_getTemplateConfig($media, $file));
                    }
                    catch(Exception $e)
                    {
                        MWF_Log::exception($e);
                    }
                }
            }
        }
        catch(Exception $e)
        {
            MWF_Log::exception($e);
            $item['data_content'] = '';
            $item['media'] = array();
        }

        return $item;
    }
}
