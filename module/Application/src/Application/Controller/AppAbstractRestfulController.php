<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

/**
 * Class AppAbstractRestfulController
 * @package
 */
abstract class AppAbstractRestfulController extends AbstractRestfulController
{
    protected $collectionOptions = array();
    protected $resourceOptions = array();
    protected $eventIdentifier = 'CommonController';

    /**
     * @param $status_code
     * @param $message
     * @param null $type
     * @param null $title
     * @return ApiProblemResponse
     */
    public function createResponse($status_code, $message, $type = null, $title = null)
    {
        return new ApiProblemResponse(
            new ApiProblem($status_code, $message, $type, $title)
        );
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function create($data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param mixed $id
     * @return mixed|ApiProblemResponse
     */
    public function delete($id)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @return mixed|ApiProblemResponse
     */
    public function deleteList($data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param mixed $id
     * @return mixed|ApiProblemResponse
     */
    public function get($id)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @return mixed|ApiProblemResponse
     */
    public function getList()
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param null $id
     * @return mixed|ApiProblemResponse
     */
    public function head($id = null)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @return mixed|ApiProblemResponse
     */
    public function options()
    {
        $response = $this->getResponse();

        // if in Options array, Allow
        $response->getHeaders()->addHeaderLine('Access-Control-Allow-Methods', implode(',', $this->_getOptions()));

        return $response;
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        if ($this->params()->fromRoute($this->getIdentifierName(), false)) {
            return $this->resourceOptions;
        }

        return $this->collectionOptions;
    }

    /**
     * @param $id
     * @param $data
     * @return array|ApiProblemResponse
     */
    public function patch($id, $data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function replaceList($data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function patchList($data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return mixed|ApiProblemResponse
     */
    public function update($id, $data)
    {
        return $this->methodNotAllowedError();
    }

    /**
     * Method to authenticate request based on given module / process codes
     *
     * @param        $moduleCode
     * @param string $processCode
     * @return mixed
     */
    /*public function checkIfAuthorized($moduleCode, $processCode = '')
    {
        $AuthService = $this->getServiceLocator()->get('Auth\Service\AuthService');
        $AuthService->checkIfAuthorized($moduleCode, $processCode);
        return $AuthService->isAuthorized;
    }*/

    /**
     * Method to authenticate request based on given access token
     *
     * @return mixed
     */
    /*public function checkIfAuthenticated()
    {
        $AuthService = $this->getServiceLocator()->get('Auth\Service\AuthService');
        $AuthService->checkIfAuthenticated();
        return $AuthService->isAuthenticated;
    }*/

    public function invalidArgumentError($message = '')
    {
        return $this->createResponse(409, ($message ? $message : "Oops! Something went wrong! Help us improve your experience by sending an error report"));
    }

    public function resourceNotFoundError($message = '')
    {
        return $this->createResponse(404, ($message ? $message : "Resource not found"));
    }

    public function methodNotAllowedError()
    {
        return $this->createResponse(405, $this->request->getMethod() . " method not allowed.");
    }

    public function invalidCredentialsError()
    {
        return $this->createResponse(401, 'Login Error');
    }

    public function validationError($errors = null)
    {
        return $this->createResponse(412, array("validationErrors" => ($errors ? $errors : "Validation Failed.")));
    }

    public function invalidAccessError()
    {
        return $this->createResponse(403, 'Access Code Error');
    }
}
