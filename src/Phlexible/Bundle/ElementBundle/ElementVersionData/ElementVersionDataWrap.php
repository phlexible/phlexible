<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersionData;

/**
 * Element version data wrap
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionDataWrap
    implements \Iterator, \RecursiveIterator, \ArrayAccess, \Countable
{
    protected $_childWraps = array();

    protected $_data = null;

    protected $_valid = false;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function __toString()
    {
        return $this->_data['content'];

        if (sizeof($this->_data) == 1)
        {
            $item = current($this->_data);
            if (is_array($item))
            {
                return new self($item);
            }
            else
            {
                return $item;
            }
        }
    }

    public function getValue($key)
    {
        if (!isset($this->_data[$key]))
        {
            return null;
        }

        return $this->_data[$key];
    }

    public function count()
    {
        if (!isset($this->_data['children']))
        {
            return null;
        }

        return count($this->_data['children']);
    }

    public function current()
    {
        $item = current($this->_data['children']);

        if ($item)
        {
            return new self($item);
        }

        return false;
    }

    public function key()
    {
        return key($this->_data['children']);
    }

    public function next()
    {
        if(empty($this->_data['children']))
        {
            $this->_valid = false;
            return;
        }

        $this->_valid = (FALSE !== next($this->_data['children']));
    }

    public function rewind()
    {
        if(empty($this->_data['children']))
        {
            $this->_valid = false;
            return;
        }

        $this->_valid = (FALSE !== reset($this->_data['children']));
    }

    public function valid()
    {
        return $this->_valid;
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->_data))
        {
            return $this->_data[$offset];

            if (is_array($this->_data[$offset]))
            {
                return new self($this->_data[$offset]);
            }
            else
            {
                return $this->_data[$offset];
            }
        }
    }

    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
//        unset($this->_data[$offset]);
    }

    public function hasChildren()
    {
        return !empty($this->_data['children']);
    }

    public function getChildren()
    {
        return $this->current();

        $children = $this->childrenAsArray();
        $return = array();
        foreach($children as $key => $child)
        {
            $return[$key] = new self($child);
        }

        return $return;
    }

    /**
     * Enter description here...
     *
     * @param string  $offset
     * @param boolean $returnContent
     *
     * @return $this
     */
    public function first($offset, $returnContent = false)
    {
        if(empty($this->_data['children']))
        {
            return array();
        }

        foreach ($this->_data['children'] as $key => $child)
        {
            $node = $this->child($key);

            if ($child['working_title'] == $offset)
            {
                return $returnContent ? $node->getValue('content') : $node;
            }

            $return = $node->first($offset, $returnContent);

            if (!empty($return))
            {
                return $return;
            }
        }

        return $returnContent ? '' : array();
    }

    /**
     * Enter description here...
     *
     * @param string  $offset
     * @param boolean $returnContent
     *
     * @return $this
     */
    public function firstDsId($offset, $returnContent = false)
    {
        if(empty($this->_data['children']))
        {
            return array();
        }

        foreach($this->_data['children'] as $key => $child)
        {
            $node = $this->child($key);

            if ($child['ds_id'] == $offset)
            {
                return $returnContent ? $node->getValue('content') : $node;
            }

            $return = $node->firstDsId($offset, $returnContent);
            if(!empty($return))
            {
                return $return;
            }
        }

        return $returnContent ? '' : array();
    }

    /**
     * Enter description here...
     *
     * @param string $offset
     *
     * @return array
     *
     * @deprecated
     */
    public function find($offset, $returnContent = false)
    {
        return $this->first($offset, $returnContent);
    }

    /**
     * Enter description here...
     *
     * @param string $offset
     *
     * @return array
     */
    public function all($offset)
    {
        if(empty($this->_data['children']))
        {
            return array();
        }

        $data = array();
        foreach($this->_data['children'] as $key => $child)
        {
            $node = $this->child($key);

            if ($child['working_title'] == $offset)
            {
                $data[] = $node;
            }
            elseif(!empty($child['children']))
            {
                $data = array_merge($data, $node->all($offset));
            }
        }

        return $data;
    }

    /**
     * Enter description here...
     *
     * @param string $offset
     *
     * @return array
     */
    public function children($offset = null)
    {
        if ($offset !== null)
        {
            $node = $this->first($offset);
        }
        else
        {
            return $this->_data['children'];
        }

        if($node)
        {
            $children = $node->childrenAsArray();
            foreach(array_keys($children) as $key)
            {
                // build complete _childWraps array
                $node->child($key);
            }

            return $node->_childWraps;
        }

        return array();
        throw new \Exception('[data->children(): offset "'.$offset.'" not found]');
    }

    public function childrenOf($offset)
    {
        return $this->children($offset);
    }

    public function instancesOf($offset)
    {
        return $this->all($offset);
    }

    /**
     * Enter description here...
     *
     * @param string $offset
     *
     * @return array
     *
     * @deprecated
     */
    public function findChilds($offset)
    {
        return $this->children($offset);
    }

    /**
     * Get child as data wrap object or an empty array if offset is not existing.
     *
     * @param string $offset
     *
     * @return $this|array
     */
    public function child($offset)
    {
        // check child wraps cache
        if (!isset($this->_childWraps[$offset]))
        {
            // if offset is not existing retuen empty array
            if (empty($this->_data['children'][$offset]))
            {
                return array();
            }

            // build data wrap and store it in cache
            $data = $this->_data['children'][$offset];
            $node = new self($data);
            $this->_childWraps[$offset] = $node;
        }

        return $this->_childWraps[$offset];
    }

    public function childrenAsArray()
    {
        if (empty($this->_data['children']))
        {
            return array();
        }

        return $this->_data['children'];
    }

    public function print_r()
    {
        echo '<pre>';
        print_r($this->_data);
        echo '</pre>';
    }
}
