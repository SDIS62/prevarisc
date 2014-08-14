<?php

class Application_Controller_Helper_SearchPaginatorAdapter implements Zend_Paginator_Adapter_Interface
{
    /**
     * Array
     *
     * @var array
     */
    protected $_array = null;

    /**
     * Item count
     *
     * @var integer
     */
    protected $_count = null;

    /**
     * Constructor.
     *
     * @param array $array Array to paginate
     */
    public function __construct(array $data, $count)
    {
        $this->_array = $data;
        $this->_count = $count;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return array_slice($this->_array, 0, $itemCountPerPage);
    }

    /**
     * Returns the total number of rows in the array.
     *
     * @return integer
     */
    public function count()
    {
        return $this->_count;
    }
}