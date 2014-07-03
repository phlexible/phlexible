<?php

class Makeweb_Elements_Event_ListFilter extends Brainbits_Event_Notification_Abstract
{
    /**
     * @var string
     */
    protected $_notificationName = Makeweb_Elements_Event::LIST_FILTER;

    /**
     * @var array
     */
    protected $_filterData = null;

    /**
     * @var Zend_Db_Select
     */
    protected $_select = null;

    /**
     * Constructor
     *
     * @param array          $filterData
     * @param Zend_Db_Select $select
     */
    public function __construct(array $filterData, Zend_Db_Select $select)
    {
        $this->_filterData = $filterData;
        $this->_select = $select;
    }

    /**
     * Return filter data
     *
     * @return array
     */
    public function getFilterData()
    {
        return $this->_filterData;
    }

    /**
     * Return select
     *
     * @return Zend_Db_Select
     */
    public function getSelect()
    {
        return $this->_select;
    }
}
