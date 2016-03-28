<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * OrganizationController Controller layer responsible for redirecting to appropriate target and returning REST-compliance return
 * OrganizationController edge to deal with Organizations
 */
class OrganizationController extends Controller
{
    /**
     * get All available organizations
     * Name : rest_get_organizations
     * Method : GET
     * Scheme : ANY
     * Host : ANY
     * Path : /rest/organizations.{_format}
     * using jms serializer any supported format of result can be returned
     * 
     * @return array organizations array
     * @View()
     */
    public function getOrganizationsAction()
    {
        $organizationsService = $this->container->get("app.organizationService");
        return $organizationsService->getOrganizations();
    }
}
