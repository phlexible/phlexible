<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\SearchBundle\Search;

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
    private $handler;

    /**
     * @param string    $id        ID of the item
     * @param string    $title     Title of the item
     * @param string    $author    Author of the item
     * @param \DateTime $date      Date of the item
     * @param string    $image     Image that should be shown in the result for the found item
     * @param string    $component Component the item belongs
     * @param array     $handler   A menu item that should be called when the result item is clicked
     */
    public function __construct($id, $title, $author, $date, $image, $component, array $handler = null)
    {
        $this->id        = $id;
        $this->author    = $author;
        $this->title     = $title;
        $this->date      = $date;
        $this->component = $component;
        $this->image     = $image;
        $this->handler   = $handler;
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
            'date'      => $this->date->format('U'),
            'component' => $this->component,
            'image'     => $this->image,
            'handler'   => $this->handler
        ];
    }
}
