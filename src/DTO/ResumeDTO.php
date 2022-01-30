<?php
namespace App\DTO;

class ResumeDTO implements \JsonSerializable
{

    private $valorTotalReceita;

    private $valorTotalDespesa;

    private $saldoFinal;

    private $valorDespesaPorCategoria;

    public function __construct()
    {}

    /**
     *
     * @return mixed
     */
    public function getValorTotalReceita()
    {
        return $this->valorTotalReceita;
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
     * @return mixed
     */
    public function getValorDespesaPorCategoria()
    {
        return $this->valorDespesaPorCategoria;
    }

    /**
     *
     * @param mixed $valorTotalReceita
     */
    public function setValorTotalReceita($valorTotalReceita)
    {
        $this->valorTotalReceita = $valorTotalReceita;
    }

    /**
     *
     * @param mixed $valorTotalDespesa
     */
    public function setValorTotalDespesa($valorTotalDespesa)
    {
        $this->valorTotalDespesa = $valorTotalDespesa;
    }

    /**
     *
     * @param mixed $valorDespesaPorCategoria
     */
    public function setValorDespesaPorCategoria($valorDespesaPorCategoria)
    {
        $this->valorDespesaPorCategoria = $valorDespesaPorCategoria;
    }

    /**
     *
     * @return mixed
     */
    public function getSaldoFinal()
    {
        return $this->saldoFinal;
    }

    /**
     *
     * @param mixed $saldoFinal
     */
    public function setSaldoFinal($saldoFinal)
    {
        $this->saldoFinal = $saldoFinal;
    }

    public function jsonSerialize()
    {
        return [
            'valorTotalReceita' => $this->valorTotalReceita,
            'valorTotalDespesa' => $this->valorTotalDespesa,
            'saldoFinal' => $this->saldoFinal,
            'valorPorCategoriaDeDespesa' => $this->valorDespesaPorCategoria
        ];
    }

    public function __toString()
    {
        return json_encode($this);
    }
}

