<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ResumeService;
use App\Controller\Validations\ErrorExceptions;

class ResumeController extends AbstractController
{
    private ResumeService $resumeService;
    
    public function __construct(ResumeService $resumeService) {
        $this->resumeService = $resumeService;
    }
    
    #[Route('/resumo/{ano}/{mes}', name: 'resume')]
    public function index(int $ano, int $mes): JsonResponse
    {
        if ($ano < 1970 || $ano > date('Y') || $ano == 0) {
            return ErrorExceptions::badRequestBuilder('ano informado é invalido');
        }
        
        if ($mes > 12 || $mes <= 0) {
            return ErrorExceptions::badRequestBuilder('mês informado é invalido');
        }
        
        $resumeDTO = $this->resumeService->createTableView($mes, $ano);
        return new JsonResponse($resumeDTO);
    }
}