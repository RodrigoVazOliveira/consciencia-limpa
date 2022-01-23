<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Service\OutgoingService;
use App\Controller\Validations\ValidationJson;
use App\Controller\Validations\ErrorExceptions;

#[Route('/despesas', name: 'despesas_')]
class OutgoingController extends AbstractController
{
    private LoggerInterface $logger;
    private ValidatorInterface $validator;
    private OutgoingService $outgoinService;
    
    function __construct(LoggerInterface $logger, ValidatorInterface $validator, OutgoingService $outgoingService)
    {
        $this->logger = $logger;
        $this->validator = $validator;
        $this->outgoinService = $outgoingService;
    }
    
    #[Route(methods: ['POST'], name: 'outming_save')]
    function save(Request $request): JsonResponse 
    {
        $this->logger->info('save - getContent: '.$request->getContent());
        $validationJson = new ValidationJson($this->validator, json_decode($request->getContent()));
        $outgoing = $validationJson->createOutgoingWithPayload();
        
        try {
            $outgoing = $this->outgoinService->save($outgoing);
            return new JsonResponse($outgoing, 201);
        } catch (\RuntimeException $ex) {
            return ErrorExceptions::badRequestBuilder($ex->getMessage());
        }
    }
}