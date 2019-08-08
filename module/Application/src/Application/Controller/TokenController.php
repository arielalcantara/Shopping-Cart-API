<?php
/**
 * Zend Framework (http=>//framework.zend.com/)
 *
 * @link      http=>//github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http=>//www.zend.com)
 * @license   http=>//framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Auth\Service\TokenService;
use Application\Controller\AppAbstractRestfulController;
use Zend\View\Model\JsonModel;

class TokenController extends AppAbstractRestfulController
{
    private $TokenService;
    public function __construct(
        TokenService $TokenService
    ) {
        $this->TokenService = $TokenService;
    }

    public function getList()
    {
        $token = $this->TokenService->generateToken(['customer_id' => 10001, 'name' => 'Test']);
        return new JsonModel(array('token' => $token));
    }

}
