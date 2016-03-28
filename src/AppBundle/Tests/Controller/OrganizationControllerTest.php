<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\AbstractTestCase;

/**
 * OrganizationControllerTest - Unit test for actions in OrganizationController
 * @covers OrganizationController
 */
class OrganizationControllerTest extends AbstractTestCase {
    
    /**
     * Tests OrganizationController::getOrganizationsAction()
     * Tests valid action with JSON format
     * 
     * 
     * @covers OrganizationController::getOrganizationsAction
     * 
     * Assert Page is loaded ok
     * Assert JSON structure is returned holding all Organizations
     */
    public function testGetOrganizationsActionForJSONFormat() {
        $token = $this->getAuthenticationToken();
        // Route to tested action
        $this->client->request('GET', $this->router->generate(
                'rest_get_organizations',
                array(
                    '_format' => 'JSON',
                    )), 
                /*$parameters =*/ array(), 
                /*$files =*/ array(), 
                /*$server =*/ array(
                    'HTTP_Authorization' =>"Bearer ".$token )
                );
        
        // client response
        $clientResponse = $this->client->getResponse();

        // Assertions
        // Assert Page is loaded Ok
        $this->assertEquals(200, $clientResponse->getStatusCode());
        
        $organizationService = $this->container->get('app.organizationService');
        $expectedOrganizations = $organizationService->getOrganizations();
        $jmsSerializerService = $this->container->get('serializer');
        $jsonOrganizations = $jmsSerializerService->serialize($expectedOrganizations, 'JSON');
        
        // Assert JSON structure is returned holding all organizations
        $this->assertJsonStringEqualsJsonString($jsonOrganizations, $clientResponse->getContent());
    }
}
