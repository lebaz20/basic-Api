<?php

namespace AppBundle\Tests;

use JMS\Serializer\Exception\RuntimeException;

/**
 * AbstractRestTestCase - middle layer between AbstractTestCase on one side and rest test cases on the other side
 */
class AbstractTestCase extends PHPUnit_Framework_TestCase {


    /**
     * get authentication token to authenticate calls to REST edges
     * @param string $actorsLoginCredintials default value RestApiAdmin
     * @return mixed token string needed to authenticate calls to REST edges, boolean false on failure
     */
    public function getAuthenticationToken($actorsLoginCredintials='RestApiAdmin')
    {
        
        $username = $this->loginCredintials[$actorsLoginCredintials]['default']['username'];
        $password = $this->loginCredintials[$actorsLoginCredintials]['default']['password'];
        $this->client->request('GET', $this->router->generate(
                'get_user',
                array(
                    '_format' => 'JSON',
                    )),
                /*$parameters =*/ array(),
                /*$files =*/ array(),
                /*$server =*/ array(
                    'X-Requested-With' => "XMLHttpRequest",
                    'PHP_AUTH_USER' => $username,
                    'PHP_AUTH_PW' => $password)
                );
        $jmsSerializerService = $this->container->get('jms_serializer');
        try{
            $tokenArray = $jmsSerializerService->deserialize(/*$data = */ $this->client->getResponse()->getContent(), /*$type = */ 'array', /*$format = */ 'JSON');
            return $tokenArray['token'];
        } catch (RuntimeException $e) {
            return false;
        }
    }

}
