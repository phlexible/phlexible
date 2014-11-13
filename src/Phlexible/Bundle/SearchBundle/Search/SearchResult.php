<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SearchBundle\Search;

use Phlexible\Bundle\GuiBundle\Menu\Item;

/**
 * Search result
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SearchResult
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $component;

    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */
    private $menuHandler;

    /**
     * @param string $id          ID of the item
     * @param string $title       Title of the item
     * @param string $author      Author of the item
     * @param string $date        Create date of the item
     * @param string $image       Image that should be shown in the result for the found item
     * @param string $component   Component the item belongs
     * @param array  $menuHandler A menu item that should be called when the result item is clicked
     */
    public function __construct($id, $title, $author, $date, $image, $component, array $menuHandler = null)
    {
        $this->id          = $id;
        $this->author      = $author;
        $this->title       = $title;
        $this->date        = $date;
        $this->component   = $component;
        $this->image       = $image;
        $this->menuHandler = $menuHandler;
    }

    /**
     * Return array repesentation of this search result
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'        => $this->id,
            'author'    => $this->author,
            'title'     => $this->title,
            'date'      => $this->date,
            'component' => $this->component,
            'image'     => $this->image,
            'menu'      => $this->menuHandler
        ];
    }
}
