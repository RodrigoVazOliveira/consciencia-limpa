<?php
namespace App\Controller\Validations;

use App\DTO\IncomingDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationJson
{

    private $payload;
    private object $dto;
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator, $payload)
    {
        $this->validator = $validator;
        $this->payload = $payload;
    }

    public function createIncomingWithPayload()
    {
        $dataReceita = \DateTime::createFromFormat('d/m/Y', $this->payload->data)->format('Y-m-d');
        $responseErrorsNotNull = $this->responseWithErrorsInputNotNull($this->verifyDataDTO());
        
        if ($responseErrorsNotNull != null)
        {
            return $responseErrorsNotNull;
        }
        
        $this->dto = new IncomingDTO($this->payload->descricao, $this->payload->valor, $dataReceita);
        $validate = $this->validateDTO($responseErrorsNotNull);

        if ($validate != null) {
            return $validate;
        }

        return $this->dto->converterDTOToEntity();
    }

    public function createOutgoingWithPayload()
    {
        $dataReceita = \DateTime::createFromFormat('d/m/Y', $this->payload->data)->format('Y-m-d');
        $responseErrorsNotNull = $this->responseWithErrorsInputNotNull($this->verifyDataDTO());
        
        if ($responseErrorsNotNull != null)
        {
            return $responseErrorsNotNull;
        }
        
        $this->dto = new IncomingDTO($this->payload->descricao, $this->payload->valor, $dataReceita);
        $validate = $this->validateDTO($responseErrorsNotNull);
        
        if ($validate != null) {
            return $validate;
        }
        
        return $this->dto->converterDTOToEntityOuting();
    }
    
    private function validateDTO($responseErrorsNotNull): ?JsonResponse
    {
        if ($this->dto == null)
        {
            return ErrorExceptions::badRequestBuilder([
                'erros de campos nulos' => $responseErrorsNotNull
            ]);
        }
        
        $errorsValidations = $this->validator->validate($this->dto);
        $valueValidator = count($errorsValidations);
        if ($valueValidator > 0) :
            return ErrorExceptions::badRequestBuilder([
                'erros de validação' => $this->buildMessageErrorsResponse($valueValidator, $errorsValidations),
                'erros de campos nulos' => $responseErrorsNotNull
            ]);
        endif;

        return null;
    }

    private function buildMessageErrorsResponse($valueValidator, $errorsValidations): array
    {
        $messagesErrors = array();
        for ($i = 0; $i < $valueValidator; $i ++) {
            $inputError = $errorsValidations->get($i);
            array_push($messagesErrors, [
                'campo' => $inputError->getPropertyPath(),
                'mensagem' => $inputError->getMessage()
            ]);
        }

        return $messagesErrors;
    }

    private function verifyDataDTO()
    {
        $errors = array();

        if (! isset($this->payload->descricao)) :
            array_push($errors, 'campo descrição não informado');
        endif;

        if (! isset($this->payload->valor)) :
            array_push($errors, 'campo valor não informado');
        endif;

        if (! isset($this->payload->data)) :
            array_push($errors, 'campo data não informado');
        endif;

        return $errors;
    }

    private function responseWithErrorsInputNotNull($errors): ?JsonResponse
    {
        if (count($errors) == 0) :
            return null;
        endif;

        return new JsonResponse([
            'status' => 'BAD_REQUEST',
            'code' => 400,
            'errors' => $errors
        ], 400);
    }
}

