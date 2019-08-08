<?php
namespace Cart\ServiceFactory\Controller;

use Psr\Container\ContainerInterface;
use Cart\Filter\CartFilter;
use Cart\Controller\CartController;
use Product\Model\ProductTable;
use Cart\Model\CartTable;
use Cart\Model\CartItemTable;
use Cart\Service\CartService;
use Cart\Service\CartItemService;
use Product\Service\ProductService;
use Shipping\Service\ShippingService;
use Customer\Model\CustomerTable;
use Cart\Model\Cart;
use Cart\Model\CartItem;
use Auth\Service\TokenService;

class CartControllerFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $container = $container->getServiceLocator(); // remove if zf3
        $cartFilter = $container->get(CartFilter::class);
        $productTable = $container->get(ProductTable::class);
        $cartTable = $container->get(CartTable::class);
        $cartItemTable = $container->get(CartItemTable::class);
        $cartService = $container->get(CartService::class);
        $cartItemService = $container->get(CartItemService::class);
        $productService = $container->get(ProductService::class);
        $shippingService = $container->get(ShippingService::class);
        $customerTable = $container->get(CustomerTable::class);
        $cart = new Cart;
        $cartItem = new CartItem;
        $tokenService = $container->get(TokenService::class);

        return new CartController(
            $cartFilter,
            $productTable,
            $cartTable,
            $cartItemTable,
            $cartService,
            $cartItemService,
            $productService,
            $shippingService,
            $customerTable,
            $cart,
            $cartItem,
            $tokenService
        );
    }
}
