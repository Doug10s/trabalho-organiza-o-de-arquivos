<?php
require_once('genericArquivo.php');
require_once('node.php');

/**
 * Classe responsavel por manipular os indices
 * 
 */

class ManipulaIndices extends genericArquivo{


/**
 * Monta o vetor de indice na SESSION, assim nao perdemos a informacao a cada pesquisa
 */
public function montaVetorIndice(array $vetorIndice) {
    $_SESSION['vetor'] = '';
    $_SESSION['vetor'] = $vetorIndice;
}

public function montaArvoreIndice(Node $arvore) {
    $_SESSION['arvore'] = '';
    $_SESSION['arvore'] = serialize($arvore);  
}


/**
 * Funcao que realiza a pesquisa no arquivo de indices
 */
private function pesquisaIndice($informacao, $arquivo, $index) {
    $handle = fopen($arquivo, "r");
    $controlaLinha = 0;

    if ($handle) {
        while (!feof($handle)) {
             fseek($handle, $controlaLinha);
             $row = fgets($handle, genericArquivo::TAMANHO_DA_STRING[$index]); 
             $controlaLinha+=genericArquivo::TAMANHO_DA_STRING[$index];

             if ($informacao == $this->processaArquivoIndice($row, genericArquivo::INFORMACAO_PESQUISADA_INDICES[$index])) {
                 return $row;
            }
         }
     } 
     
     return false;
 }


/**
 * @text texto a ser quebrado
 * 
 */
private function processaArquivoIndice($text, $index) {
    $string = explode(';', $text);
    if (array_key_exists($index, $string)) {
        return $string[$index];
    }
    return false;
}

/**
 * Responsavel por realizar a busca no arquivo dadosBinario;
 * 
 */

public function pesquisaDados($linha) {
        $handle = fopen("dadosBinario.bin","r");

        fseek($handle, $linha);
        $row = fgets($handle, 11856); 
        return $this->binaryToString($row);
}

private function removerUltimosCaracteres($string, $numCaracteres) {
    if ($string) {
        $novaString = substr(str_replace(' ', '', $string), 0, -$numCaracteres);
        return $novaString;
    }

    return false;
}


/**
 * Funcao para pesquisar indice no arquivo por Id
 * 
 */
public function pesquisaIndicePorId($informacao) {
    if ($informacao == '') {
        return;
    }
      
    $row = $this->pesquisaIndice($informacao, 'indiceId.bin', 'id');

    if ($row) {
        $posicao = $this->removerUltimosCaracteres($this->processaArquivoIndice($row, 1), 2);
        return $this->pesquisaDados($posicao);
    }  
}

/**
 * Funcao para pesquisar indice no arquivo por nome
 * 
 */
public function pesquisaIndicePorNome($informacao) {
    if ($informacao == '') {
        return;
    }

    $row = $this->pesquisaIndice($informacao, 'indiceNome.bin', 'nome');

   if ($row) {
    $posicao = $this->removerUltimosCaracteres($this->processaArquivoIndice($row, 1), 2);
    return $this->pesquisaDados($posicao);
    }  

}


/**
 * funcao para pesquisar indices no vetor
 */
public function pesquisaIndiceNoVetor($posicao) {
    if (array_key_exists($posicao, $_SESSION['vetor'])) {
        return $this->pesquisaDados((int) $_SESSION['vetor'][$posicao]);
    }
}

/**
 * funcao para pesquisar indices na arvore
 */
public function pesquisaIndiceArvore($informacao) {
    if ($informacao == '') {
        return;
    }

   return $this->pesquisaDados((int) $this->searchData($informacao));


}

public function search($root, $data) {
    if ($root == null || $this->limpaData($root->data, 0) == $data) {
        return  $this->limpaData($root->data, 1);
    }

    if ($data < $this->limpaData($root->data, 0)) {
        return $this->search($root->left, $data);
    }

    return $this->search($root->right, $data);
}

public function searchData($data)
{   
    return $this->search(unserialize($_SESSION['arvore']), $data);
}

public function limpaData($row, $posicaoRetornada) {
    $row = explode(';', $row);

    return $row[$posicaoRetornada];
}

public function listaAplicativosGratuitos($control) {
    $handle = fopen("dadosBinario.bin","r");
    $controlaLinha = 0;
    $i = 0;
    if ($handle) {
        while (!feof($handle)) {
            $i++;
            set_time_limit(0);
            fseek($handle, $controlaLinha);
            $row = fgets($handle, 11856); 
            $row = $this->binaryToString($row);

            $free = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['free']);
            if ($free == 'True') {
                echo $row . '<br><br>';
            }

            if ($i == ($control)) {
                return;
            }

            $controlaLinha += 11856;
        }
    }
}

public function listaAplicativosMaisAvaliados($control) {
    $handle = fopen("dadosBinario.bin","r");
    $controlaLinha = 0;
    $i = 0;

    if ($handle) {
        while (!feof($handle)) {
            set_time_limit(0);
            $i++;
            fseek($handle, $controlaLinha);
            $row = fgets($handle, 11856); 
            $row = $this->binaryToString($row);

            $price = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['RatingCount']);

            if ((int) $price >= 2000) {
                echo $row . '<br><br>';
            }

            if ($i == $control) {
                return;
            }


            $controlaLinha += 11856;
        }
    }
}


}
