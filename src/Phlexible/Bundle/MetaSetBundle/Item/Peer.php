<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */
use Phlexible\Bundle\MetaSetBundle\ItemException;
use Phlexible\Bundle\MetaSetBundle\ItemInterface;

/**
 * Meta item peer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Media_MetaSets_Item_Peer
{
    /**
     * Return a set
     *
     * @param string        $setId
     * @param ItemInterface $itemIdentifier
     * @param string $id
     */
    public static function get($setId, ItemInterface $itemIdentifier, $metaSetItemClass = null)
    {
        if ($metaSetItemClass !== null && !class_exists($metaSetItemClass))
        {
            throw new ItemException('Unknown meta set item class "'.$metaSetItemClass.'"');
        }

        if ($metaSetItemClass !== null &&
            $metaSetItemClass !== 'Media_MetaSets_Item' &&
            !is_subclass_of($metaSetItemClass, 'Media_MetaSets_Item'))
        {
            throw new ItemException('Meta set item class must be a subclass of Media_MetaSets_Item');
        }
        elseif ($metaSetItemClass === null)
        {
            $metaSetItemClass = 'Media_MetaSets_Item';
        }

        $item = new $metaSetItemClass($setId, $itemIdentifier);

        return $item;
    }
}
