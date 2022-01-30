<?php
namespace App\DTO;

class OutgoingCategoryValueDTO implements \JsonSerializable
{

    private $nomeCategoria;

    private $valorTotalDespesa;

    public function __construct()
    {}

    /**
     *
     * @return mixed
     */
    public function getNomeCategoria()
    {
        return $this->nomeCategoria;
    }

    /**
     *
     * @return mixed
     */
    public function getValorTotalDespesa()
    {
        return $this->valorTotalDespesa;
    }

    /**
     *
     * @param mixed $nomeCategoria
     */
    public function setNomeCategoria($nomeCategoria)
    {
        $this->nomeCategoria = $nomeCategoria;
    }

    /**
     *
     * @param mixed $valorTotalDespesa
     */
    public function setValorTotalDespesa($valorTotalDespesa)
    {
        $this->valorTotalDespesa = $valorTotalDespesa;
    }

    public function jsonSerialize()
    {
        return [
            'nome_categoria' => $this->nomeCategoria,
            'valor_total_despesa' => $this->valorTotalDespesa
        ];
    }
    
    public function __toString() 
    {
        return json_encode($this);
    }
}

