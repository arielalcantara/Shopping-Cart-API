<?php
namespace Product\Controller;

use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;
use Product\Model\ProductTable;

class ProductController extends AppAbstractRestfulController
{
    private $productTable;

    public function __construct(
        ProductTable $productTable
    ) {
        $this->productTable = $productTable;
    }

    public function getList()
    {
        $products = $this->productTable->fetchAllProducts();

        return new JsonModel($products);
    }

    public function get($product_id)
    {
        $product = $this->productTable->fetchProduct($product_id);

        if (!$product) {
            return $this->createResponse(404, 'Product does not exist');
        }

        return new JsonModel([
            'success' => true,
            'data' => $product->getArrayCopy()
        ]);
    }
}
