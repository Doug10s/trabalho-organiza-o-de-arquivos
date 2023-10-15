<?php

require_once('genericArquivo.php');
require_once('node.php');

/**
 * Classe responsavel por criar todas as estrutura de indices
 * 
 * 
 */


class CriaIndices extends genericArquivo {
    public $vetorIndice = [];
    public $arvoreIndice;
    public $teste;
    public $numLinhas;
    public $indiceVetor;
    private $root;
    private $criarArquivos;


    /**
     * se passar true no testar, sera criado um arquivo com o numero de linhas passada. 
     * 
     * se passar true no criarArquivos, sera criado os arquivos de indices novamente.
     */
    function __construct($testar, $numLinhas, $criarArquivos) {
        $this->teste = $testar;
        $this->criarArquivos = $criarArquivos;
        if ($this->teste) {
            $this->numLinhas = $numLinhas;
        }        
    }

    
    public function executar() {
        if (file_exists('indiceNome.bin') && $this->criarArquivos) {
            unlink('indiceNome.bin');
        }
        if (file_exists('indiceId.bin') && $this->criarArquivos ) {
            unlink('indiceId.bin');
        }

        $this->criaEstruturaIndices();
    }


    /**
     * Responsavel por criar todas as estruturas de indices.
     */

    private function criaEstruturaIndices() {
        $handle = fopen("dadosBinario.bin","r");
        if ($this->criarArquivos) {
            $arqNome = fopen('indiceNome.bin', "wb");
            $arqId = fopen('indiceId.bin', "wb");
        }

        $controlaLinha = 11856;
        $i = 0;
        if ($handle) {
            while (!feof($handle)) {
                set_time_limit(0);
                $i++;
                fseek($handle, $controlaLinha);
                $row = fgets($handle, 11856); 
                $row = $this->binaryToString($row);

                $id = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['id']);
                    
                if (!empty($id) && $this->criarArquivos) {
                    $indice = $this->montaIndice($id, $controlaLinha);
                    $indice = $this->ajustaTamanho($indice, genericArquivo::TAMANHO_DA_STRING['id']);
                        
                    fwrite($arqId, $indice);
                }

                $nome = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['nome']);
                if (!empty($nome) && $this->criarArquivos) {
                    $indice = $this->montaIndice($nome, $controlaLinha);
                    $indice = $this->ajustaTamanho($indice, genericArquivo::TAMANHO_DA_STRING['nome']);
                        
                    fwrite($arqNome, $indice);
                    
                }
                $this->criaVetorIndice($row, $controlaLinha);
                $this->criaArvoreIndice($row, $controlaLinha);

                if ($this->teste && $this->numLinhas == $i) {
                    return;
                }

                $controlaLinha += 11856;
            }
        }   
        fclose($handle);
        if ($this->criarArquivos) {
            fclose($arqId);
            fclose($arqNome);
        }
    }
    
    /**
     * Responsavel por criar o vetor de indices
     * 
     */
    private function criaVetorIndice($row, $controlaLinha) {
        $id = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['developerId']);

        if (!empty($id)) {    
            if (array_key_exists($id, $this->vetorIndice)) {
                $this->vetorIndice[$id] .= $controlaLinha . ';';
            }  else {
                $this->vetorIndice[$id] = $controlaLinha . ';';
            }

        }
    }


    /**
     * Responsavel por criar a arvore de indices
     */
    private function criaArvoreIndice($row, $controlaLinha) {
        $email = $this->buscaId($row, genericArquivo::INFORMACAO_PESQUISADA['developerEmail']);
        if (!empty($email)) {    
            $email = $this->montaIndiceArvore($email, $controlaLinha);
            $this->insertData($email);
        }
    }


    private function montaIndice($id, $linha) {
        return $id . ';' . $linha . '\n';
    }

    private function montaIndiceArvore($id, $linha) {
        return $id . ';' . $linha;
    } 

    public function ajustaTamanho($row, $tamanhoCampo) {
        return str_pad($row, $tamanhoCampo, ' ');
    }

    public function getArvore() {
        return $this->root;
    }

    public function getVetor() {
        return $this->vetorIndice;
    }



    /**
     * Codigo para criar a arvore binaria.
     * 
     */

    private function height($node)
    {
        if ($node == null) {
            return 0;
        }
        return $node->height;
    }

    private function updateHeight($node)
    {
        $node->height = 1 + max($this->height($node->left), $this->height($node->right));
    }

    private function balanceFactor($node)
    {
        if ($node == null) {
            return 0;
        }
        return $this->height($node->left) - $this->height($node->right);
    }

    private function rightRotate($y)
    {
        $x = $y->left;
        $T = $x->right;

        $x->right = $y;
        $y->left = $T;

        $this->updateHeight($y);
        $this->updateHeight($x);

        return $x;
    }

    private function leftRotate($x)
    {
        $y = $x->right;
        $T = $y->left;

        $y->left = $x;
        $x->right = $T;

        $this->updateHeight($x);
        $this->updateHeight($y);

        return $y;
    }

    public function insert($root, $data)
    {
        if ($root == null) {
            return new Node($data);
        }

        if ($data < $root->data) {
            $root->left = $this->insert($root->left, $data);
        } elseif ($data > $root->data) {
            $root->right = $this->insert($root->right, $data);
        } else {
            return $root; // Duplicates are not allowed
        }

        // Update height of current node
        $this->updateHeight($root);

        // Get the balance factor of this node
        $balance = $this->balanceFactor($root);

        // Left Heavy (LL or LR rotation)
        if ($balance > 1) {
            // Left Left Case (LL rotation)
            if ($data < $root->left->data) {
                return $this->rightRotate($root);
            }
            // Left Right Case (LR rotation)
            else {
                $root->left = $this->leftRotate($root->left);
                return $this->rightRotate($root);
            }
        }
        // Right Heavy (RR or RL rotation)
        if ($balance < -1) {
            // Right Right Case (RR rotation)
            if ($data > $root->right->data) {
                return $this->leftRotate($root);
            }
            // Right Left Case (RL rotation)
            else {
                $root->right = $this->rightRotate($root->right);
                return $this->leftRotate($root);
            }
        }

        return $root;
    }

    public function insertData($data)
    {
        $this->root = $this->insert($this->root, $data);
    }
  

}

    

