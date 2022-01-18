<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/revenues")
 * @author thomas
 *
 */
class RevenueController extends AbstractController
{
    /**
     * @Route("/home", methods={"GET"})
     */
    public function index(): Response
    {
        return new Response('Olá mundo!');
    }
    
    public static function getSubscribedServices(): array
    {
        return array();
    }
}