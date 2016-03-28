<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * UserController Controller layer responsible for redirecting to appropriate target and returning REST-compliance return
 * UserController edge to deal with FOS_User entity
 */
class UserController extends Controller {

    /**
     * get logged in user token
     * Name : rest_get_user
     * Method : GET
     * Scheme : ANY
     * Host : ANY
     * Path : /authenticate/user.{_format}
     * using jms serializer any supported format of result can be returned
     * 
     * @return array authentication token for current logged in user as value of key 'token'
     * @View()
     */
    public function getUserAction() {
        $securitycontext = $this->get('security.context');
        $jwtManagerService = $this->get('lexik_jwt_authentication.jwt_manager');

        $user = $securitycontext->getToken()->getUser();
        $token = $jwtManagerService->create($user);
        return array(
            'token' => $token
        );
    }
}
