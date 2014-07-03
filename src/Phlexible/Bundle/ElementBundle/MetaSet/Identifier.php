<?php

class Makeweb_Elements_Element_Version_MetaSet_Identifier implements Media_MetaSets_Item_Interface
{
    /**
     * @var array
     */
    protected $_identifiers = array();

    /**
     * Constructor
     *
     * @param Makeweb_Elements_Element_Version $elementVersion
     * @param string                           $language
     */
    public function __construct(Makeweb_Elements_Element_Version $elementVersion, $language)
    {
        $this->_identifiers = array(
            'eid'      => $elementVersion->getEid(),
            'version'  => $elementVersion->getVersion(),
            'language' => $language
        );
    }

    /**
     * Return table name
     *
     * @return string
     */
    public function getTableName()
    {
        return DB_PREFIX . 'element_version_metaset_items';
    }

    /**
     * Return identifiers
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->_identifiers;
    }

    /**
     * Return key field
     *
     * @return string
     */
    public function getKeyField()
    {
        return 'key';
    }

    /**
     * Return value field
     *
     * @return string
     */
    public function getValueField()
    {
        return 'value';
    }
}