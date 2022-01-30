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
use App\DTO\IncomingDTO;
use App\DTO\OutgoingDTO;

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
        
        if ($outgoing instanceof JsonResponse) {
            return $outgoing;
        }
        
        try {
            $outgoing = $this->outgoinService->save($outgoing);
            return new JsonResponse($outgoing, 201);
        } catch (\RuntimeException $ex) {
            return ErrorExceptions::badRequestBuilder($ex->getMessage());
        }
    }
    
    #[Route(methods: ['GET'], name: 'outming_get_all')]
    function getAll(Request $request): JsonResponse
    {
        $this->logger->info('getAll - listando as despesas');
        $outgoings = $this->verifyGetAllFilterDescription($request);
        return new JsonResponse(OutgoingDTO::convertEntityListToListDTO($outgoings));
    }
    
    
    #[Route('/{id}',methods: ['GET'], name: 'outming_get_by_id')]
    function getById(int $id): JsonResponse
    {
        $this->logger->info('getById - buscar despesa por id: '.$id);
        try {
            $outgoing = $this->outgoinService->findById($id);
            return new JsonResponse(OutgoingDTO::convertEntityToDTO($outgoing));
        } catch (\RuntimeException $ex) {
            return ErrorExceptions::badRequestBuilder($ex->getMessage());
        }
    }
    
    #[Route('/{ano}/{mes}',methods: ['GET'], name: 'outming_get_all_by_month')]
    function getAllByMonth(int $ano, int $mes): JsonResponse
    {
        if ($ano < 1970 || $ano > date('Y') || $ano == 0) {
            return ErrorExceptions::badRequestBuilder('ano informado é invalido');
        }
        
        if ($mes > 12 || $mes <= 0) {
            return ErrorExceptions::badRequestBuilder('mês informado é invalido');
        }
        
        $this->logger->info("getAllByMonth - ano: $ano, mês: $mes");
        $outgoings = $this->outgoinService->getAllByMonth($mes, $ano);
        
        return new JsonResponse(OutgoingDTO::convertEntityListToListDTO($outgoings));
    }
    
    #[Route('/{id}',methods: ['PUT'], name: 'outming_update')]
    function update(int $id, Request $request): JsonResponse
    {
        $this->logger->info('update - atualizar despesa por id: '.$id.' despesa: '.$request->getContent());
        $validationJson = new ValidationJson($this->validator, json_decode($request->getContent()));
        $outgoing = $validationJson->createOutgoingWithPayload();
        
        if ($outgoing instanceof JsonResponse) {
            return $outgoing;
        }
        
        try {
            $outgoingUpdate = $this->outgoinService->update($id, $outgoing);
            return new JsonResponse($outgoingUpdate);
        } catch (\RuntimeException $ex) {
            return ErrorExceptions::badRequestBuilder($ex->getMessage());
        }
    }
    
    #[Route('/{id}',methods: ['DELETE'], name: 'outming_delete_by_id')]
    function deleteById(int $id):JsonResponse
    {
        $this->logger->info('deleteById - id: '.$id);
        try {
            $this->outgoinService->deleteById($id);
            return new JsonResponse('', 204);
        } catch (\RuntimeException $ex) {
            $this->logger->error('deleteById - erro na requisição - message: '.$ex->getMessage());
            return ErrorExceptions::badRequestBuilder($ex->getMessage());
        }
    }
    
    private function verifyGetAllFilterDescription(Request $request)
    {
        $this->logger->info("verifyGetAllFilterDescription - verificar se possui filtro");
        if ($request->query->get('descricao') != null) {
            $description = $request->query->get('descricao');
            $this->logger->info("verifyGetAllFilterDescription - existe filtro ". $description);
            return $this->outgoinService->getAllByDescription($description);
        }
        
        return $this->outgoinService->getAll();
    }
}