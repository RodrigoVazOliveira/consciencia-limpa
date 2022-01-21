<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\DTO\IncomingDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Service\IncomingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Incoming;

#[Route('/receitas')]
class IncomingController extends AbstractController
{

    private LoggerInterface $logger;

    private IncomingService $incomingService;

    function __construct(LoggerInterface $logger, IncomingService $incomingService)
    {
        $this->logger = $logger;
        $this->incomingService = $incomingService;
    }

    #[Route(methods: ['POST'], name: 'incoming_save')]
    function save(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent());
        $responseErrors = $this->responseWithErrorsInputNotNull($this->verifyDataIncoming($payload));
        
        if ($responseErrors != null) {
            return $responseErrors;
        }
        
        $incomingSaved = $this->incomingService->save($this->createIncomingWithPayload($payload));
        
        if ($incomingSaved == null) {
            return $this->badRequestBuilder('Já existe uma receita com a descrição registrada nesse mês');
        }
        
        return new JsonResponse($incomingSaved, 201);
    }
    
    #[Route(methods: ['GET'], name: 'incoming_get_all')]
    function getAll():JsonResponse
    {
        $incomings = $this->incomingService->getAll();
        $incomingDTOs = IncomingDTO::convertListIncomingToListIncomingDTO($incomings);
        return new JsonResponse($incomingDTOs);
    }
    
    #[Route('/{id}', methods: ['GET'], name: 'incoming_find_by_id')]
    function findIncomingDetails(int $id): JsonResponse
    {
        try {
            $incoming = $this->incomingService->findById($id);
            return new JsonResponse(IncomingDTO::convertEntityToDTO($incoming));
        } catch (\RuntimeException $ex) {
            return $this->badRequestBuilder($ex->getMessage());
        }
    }
    
    #[Route('/{id}', methods: ['PUT'], name: 'incoming_update')]
    function updateIncomingById(int $id, Request $request): JsonResponse 
    {
        $payload = json_decode($request->getContent());
        $responseErrors = $this->responseWithErrorsInputNotNull($this->verifyDataIncoming($payload));
        
        if ($responseErrors != null) {
            return $responseErrors;
        }
        
        try {
            $incoming = $this->incomingService->update($id, $this->createIncomingWithPayload($payload));
            return new JsonResponse($incoming);
        } catch (\RuntimeException $ex) {
            return $this->badRequestBuilder($ex->getMessage());
        }
    }
    
    private function createIncomingWithPayload($payload):Incoming
    {
        $this->logger->info('createIncomingWithPayload - payload: '. json_encode($payload));
        $dataReceita = \DateTime::createFromFormat('d/m/Y', $payload->data)->format('Y-m-d');
        $incomingDTO = new IncomingDTO($payload->descricao, $payload->valor, $dataReceita);
        return $incomingDTO->converterDTOToEntity();
    }
    
    private function verifyDataIncoming($payload) 
    {
        $errors = array();
        
        if (!isset($payload->descricao)):
            array_push($errors, 'campo descrição não informado');
        endif;
        
        if (!isset($payload->valor)):
            array_push($errors, 'campo valor não informado');
        endif;
        
        if (!isset($payload->data)):
            array_push($errors, 'campo data não informado');
        endif;
        
        return $errors;
    }
    
    private function responseWithErrorsInputNotNull($errors):?JsonResponse
    {
        if (count($errors) == 0):
            return null;
        endif;
        
        return new JsonResponse([
            'status' => 'BAD_REQUEST',
            'code' => 400,
            'errors' => $errors
        ], 400);
    }
    
    private function badRequestBuilder($message): JsonResponse
    {
        return new JsonResponse([
            'mensagem' => $message,
            'status' => 'BAD_REQUEST',
            'code' => 400
        ], 400);
    }
    
}