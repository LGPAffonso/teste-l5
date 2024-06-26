<?php


function responseHelper($status, $mensagem, $data = null)
{
    $response = [
        'cabeçalho' => ['status' => $status, 'mensagem' => $mensagem],
        'retorno' => $data,
    ];
    return $response;
}

function valid_cpf($cpf)
{
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/^([0-9])\1+$/', $cpf)) {
        return false;
    }

    $digit = substr($cpf, 0, 9);

    for ($j = 10; $j <= 11; $j++) {
        $sum = 0;
        for ($i = 0; $i < $j - 1; $i++) {
            $sum += ($j - $i) * ((int) $digit[$i]);
        }

        $summod11 = $sum % 11;
        $digit[$j - 1] = $summod11 < 2 ? 0 : 11 - $summod11;
    }

    return $digit[9] == ((int)$cpf[9]) && $digit[10] == ((int)$cpf[10]);
}

function valid_cnpj($cnpj)
{
    $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);
    if (strlen($cnpj) != 14)
        return false;
    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
        return false;
    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }
    $resto = $soma % 11;
    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

function convert_valor($valor){

    $valor= str_replace('.','',$valor);
    $valor= str_replace(',','.',$valor);

    return $valor; 
}
