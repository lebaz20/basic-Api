<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\AbstractTestCase;

/**
 * UserControllerTest - Unit test for actions in UserController
 * @covers UserControllerTest
 *
 */
class UserControllerTest extends AbstractTestCase {

    /**
     * Tests UserController::getUserAction()
     * Tests valid action with JSON format
     * 
     * 
     * @covers UserController::getUserAction
     * 
     * Assert Page is loaded ok
     * Assert token is an array
     * Assert token has key exp
     * Assert token has key iat
     * Assert token has key username
     * Assert token has not expired yet
     * Assert token was created in valid time
     * Assert token username is as expected
     * 
     */
    public function testGetUserActionForValidCase() {
        $token = $this->getAuthenticationToken();
        // client response
        $clientResponse = $this->client->getResponse();

        // Assertions
        // Assert Page is loaded Ok
        $this->assertEquals(200, $clientResponse->getStatusCode());
        $encoder = $this->container->get('lexik_jwt_authentication.jwt_encoder');
        $tokenData = $encoder->decode($token);

        // Assert token is an array
        $this->assertInternalType('array', $tokenData);
        // Assert token has key exp
        $this->assertArrayHasKey('exp', $tokenData);
        // Assert token has key iat
        $this->assertArrayHasKey('iat', $tokenData);
        // Assert token has key username
        $this->assertArrayHasKey('username', $tokenData);
        $now = new \DateTime();
        $nowTimestamp = $now->getTimestamp();
        // Assert token has not expired yet
        $this->assertGreaterThan($nowTimestamp, $tokenData['exp']);
        // Assert token was created in valid time
        $this->assertLessThanOrEqual($nowTimestamp, $tokenData['iat']);

        $username = $this->loginCredintials['RestApiAdmin']['default']['username'];
        // Assert token username is as expected
        $this->assertEquals($username, $tokenData['username']);
    }
}
