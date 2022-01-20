<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\DTO\IncomingDTO;
use Symfony\Component\HttpFoundation\Request;
use App\Service\IncomingService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function save(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent());
        $dataReceita = \DateTime::createFromFormat('d/m/Y', $payload->data)->format('Y-m-d');
        $incomingDTO = new IncomingDTO($payload->descricao, $payload->valor, $dataReceita);
        $incomingSaved = $this->incomingService->save($incomingDTO->converterDTOToEntity());
        
        if ($incomingSaved == null) {
            return new JsonResponse([
                'mensagem' => 'Já existe uma receita com a descrição registrada nesse mês',
                'status' => 'BAD_REQUEST',
                'code' => 400
            ], 400);
        }
        
        return new JsonResponse($incomingSaved, 201);
    }
}