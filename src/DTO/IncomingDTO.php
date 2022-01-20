<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Incoming;

class IncomingDTO implements \JsonSerializable
{

    #[Assert\NotBlank(message: 'a descricao nao foi informado!')]
    private $descricao;

    #[Assert\NotBlank(message: 'o valor não foi informado!')]
    private $valor;

    #[Assert\NotBlank(message: 'A data nao foi informado!')]
    #[Assert\Date(message: 'a data informada não é valida!')]
    private $data;

    
    public function __construct($descricao, $valor, $data)
    {
        $this->descricao = $descricao;
        $this->valor = $valor;
        $this->data = $data;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function getData()
    {
        return $this->data;
    }

    public function __toString()
    {
        return json_encode($this);
    }
    
    public function converterDTOToEntity():Incoming 
    {
        $incoming = new Incoming();
        $incoming->setDescription($this->descricao)
        ->setValue($this->valor)
        ->setDate(new \DateTime($this->data));
        
        return $incoming;
    }
    
    public static function convertListIncomingToListIncomingDTO($incomings) 
    {
        $incomingDTOs = array();
        
        foreach ($incomings as $incoming)
        {
            array_push($incomingDTOs, 
                new IncomingDTO($incoming->getDescription(), $incoming->getValue(), $incoming->getDate()));
        }
        
        return $incomingDTOs;
    }

    public function jsonSerialize()
    {
        return [
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'data' => $this->data->format('d/m/Y')
        ];
    }
}