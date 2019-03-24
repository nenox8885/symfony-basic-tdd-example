<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations;


class HealthcheckController extends FOSRestController
{
    /**
     * @Annotations\Get(path="/healthcheck")
     */
    public function indexAction()
    {
        return new JsonResponse(['api' => 'ok']);
    }
}
