<?php

/**
 * Funcoes compartilhadas
 */
class genericArquivo {
    public const INFORMACAO_PESQUISADA = [
        'id' => 1,
        'nome' => 0,
        'developerId' => 13,
        'developerEmail' => 15
    ];

    public const TAMANHO_DA_STRING = [
        'id' => 1482,
        'nome' => 1496
    ];

    public const INFORMACAO_PESQUISADA_INDICES = [
        'id' => 0,
        'nome' => 0
    ];


    public function binaryToString($binaryData) {
        $string = '';
        $binaryLength = strlen($binaryData);
        for ($i = 0; $i < $binaryLength; $i += 8) {
            $byte = substr($binaryData, $i, 8);
            $char = pack('H*', base_convert($byte, 2, 16));
            $string .= $char;
        }
        return $string;
    }

    public function buscaId($row, $idDesejado) {
        $array = explode(';', $row);
        
        if (isset($array[$idDesejado])) {
            return  $array[$idDesejado];
        }
    }

}