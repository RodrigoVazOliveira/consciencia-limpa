<?php
namespace App\Enumeration;

enum CategoryEnum: string
{
    case ALIMENTACAO = 'Alimentação';
    case SAUDE = 'Saúde';
    case MORADIA = 'Moradia';
    case TRANSPORTE = 'Transporte';
    case EDUCACAO = 'Educação';
    case LAZER = 'Lazer';
    case IMPREVISTOS = 'Imprevistos';
    case OUTROS = 'Outras';
}