<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\DTO\IncomingDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[Route('/receitas')]
class IncomingController extends AbstractController
{   
    private LoggerInterface $logger;
    
    function __construct(LoggerInterface $logger) {
        $this->logger = $logger;
    }
    
    #[Route(methods: ['POST'], name: 'incoming_save')]
    public function save(Request $request,  ValidatorInterface $validator): Response
    {
        $payload = json_decode($request->getContent(), true);
        $this->validationPayload($payload, 'descricao');
        $this->validationPayload($payload, 'valor');
        $this->validationPayload($payload, 'data');
        
        $dataReceita = \DateTime::createFromFormat('d/m/Y', $payload['data'])->format('Y-m-d');
        $incomingDTO = new IncomingDTO($payload['descricao'], $payload['valor'], $dataReceita);
        
        
        $validatoinErrors = $validator->validate($incomingDTO);
        
        if (count($validatoinErrors) > 0) {
            $this->logger->error("ocorreu um erro na validacao");
            $errorsString = (string) $validatoinErrors;
            return new Response($errorsString);
        }
        
        $this->logger->info('save() - iniciar gravacao da receita ');
        $response = new Response();
        $response->setStatusCode(Response::HTTP_CREATED);
        $response->setContent($incomingDTO->__toString());
        
        return $response;
    }
    
    private function validationPayload($payload, $name) 
    {
        if (!isset($payload[$name])) {
            throw new BadRequestHttpException('A '. $name .' não foi informada!');
        }
    }
}
    