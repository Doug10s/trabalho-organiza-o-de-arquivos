<?php

// espaco em binario 00100000

function criaArquivoBinario() {
$arq = fopen("dadosBinario.bin","wb");
$handle = fopen("trabalho1.csv", 'r');
$teste = 0;

    if ($handle) {
        while (!feof($handle)) {
            exit;
            $row = fgets($handle);
            $row = removerUltimosCaracteres($row, 12);
            $row .= '\n';

            $row = ajustaTamanho($row);
            $row = stringToBinary($row);

            fwrite($arq, $row);
            unset($row);
          
         }
    }   

    fclose($arq);
    fclose($handle);
}

function ajustaTamanho($row) {
      return str_pad($row, 1482, ' ');
}

function removerUltimosCaracteres($string, $numCaracteres) {
    $novaString = substr($string, 0, -$numCaracteres);
    return $novaString;
}

function stringToBinary($string) {
    set_time_limit(0);
    $chars = str_split($string);
    $binaryData = implode('', array_map(function ($char) {
        return sprintf('%08b', ord($char));
    }, $chars));
    return $binaryData;
}


criaArquivoBinario();

echo 'finalizou!';