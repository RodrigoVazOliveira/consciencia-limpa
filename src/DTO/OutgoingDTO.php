<?php
namespace App\DTO;

use App\Entity\Category;
use App\Entity\Outgoing;
use Symfony\Component\Validator\Constraints as Assert;

class OutgoingDTO implements \JsonSerializable
{
    # [Assert\NotBlank(message: 'a descricao nao foi informado!')]
    # [Assert\Length(max: 255, maxMessage: 'a descricao deve ter no máximo 255 caracteres!')]
    private $descricao;

    # [Assert\NotBlank(message: 'o valor não foi informado!')]
    private $valor;
    
    # [Assert\NotBlank(message: 'A data nao foi informado!')]
    # [Assert\Date(message: 'a data informada não é valida!')]
    private $data;
    private $categoria;

    public function __construct($descricao, $valor, $data, $categoria)
    {
        $this->descricao = $descricao;
        $this->valor = $valor;
        $this->data = $data;
        $this->categoria = $categoria;
    }
    
    public function converterDTOToEntity(): Outgoing
    {
        $category = new Category();
        $category->setName($this->categoria);
        
        $outgoing = new Outgoing();
        $outgoing->setDescription($this->descricao)
        ->setValue($this->valor)
        ->setDate(new \DateTime($this->data))
        ->setCategory($category);
        
        return $outgoing;
    }

    public static function convertEntityListToListDTO($outgoings)
    {
        $outgoingDTOs = array();
        
        foreach ($outgoings as $outgoing)
        {
            array_push($outgoingDTOs, self::convertEntityToDTO($outgoing));
        }
        
        return $outgoingDTOs;
    }
    
    public static function convertEntityToDTO(Outgoing $outgoing)
    {
        return new OutgoingDTO($outgoing->getDescription(), $outgoing->getValue(), $outgoing->getDate(), $outgoing->getCategory()->getName());
    }

    public function jsonSerialize()
    {
        return [
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'data' => $this->data->format('d/m/Y'),
            'categoria' => $this->categoria
        ];
    }
}

