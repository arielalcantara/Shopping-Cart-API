<?php
namespace Shipping\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class ShippingTable
{
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchShippings($shipping_method = '')
    {
        $select = $this->tableGateway->getSql()->select()->columns([
            'min_weight',
            'max_weight',
            'shipping_method',
            'shipping_rate'
        ]);

        if ($shipping_method) {
            $select->where(['shipping_method' => $shipping_method]);
        }

        $resultSet = $this->tableGateway->selectWith($select)->getDataSource();
        $resultArray = iterator_to_array($resultSet);

        return $resultArray;
    }

    // public function fetchShippings()
    // {
    //     $select = $this->tableGateway->getSql()->select()->columns([
    //         'min_weight',
    //         'max_weight',
    //         'shipping_method',
    //         'shipping_rate'
    //     ]);
    //     $resultSet = $this->tableGateway->selectWith($select)->getDataSource();
    //     $resultArray = iterator_to_array($resultSet);

    //     return $resultArray;
    // }

    public function fetchShippingMethods()
    {
        $select = $this->tableGateway->getSql()->select()->columns([
            'shipping_method' => new Expression('DISTINCT(shipping_method)')
        ]);
        $resultSet = $this->tableGateway->selectWith($select)->getDataSource();
        $resultArray = iterator_to_array($resultSet);

        return $resultArray;
    }
}
