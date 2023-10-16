<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <label for="textoVetorIndice">Pesquise pelo id do desenvolvedor(Vetor de indice)</label>
        <input type="text" id="textoVetorIndice" name="textoVetorIndice">
        <input type="submit" name="vetorIndice" value="buscar">
    </form>
    </br>
    <form method="post">
        <label for="textoArvoreIndice">Pesquise pelo email do desenvolvedor(Arvore binaria)</label>
        <input type="text" id="textoArvoreIndice" name="textoArvoreIndice">
        <input type="submit" name="arvoreIndice" value="buscar">
    </form>
    </br>
    <form method="post">
        <label for="textoArquivoNome">Pesquise pelo nome do jogo (Arquivo de indice)</label>
        <input type="text" id="textoArquivoNome" name="textoArquivoNome">
        <input type="submit" name="arquivoNome" value="buscar">
    </form>
    </br>
    <form method="post">
        <label for="textoArquivoID">Pesquise pelo ID do jogo (Arquivo de indice)</label>
        <input type="text" id="textoArquivoID" name="textoArquivoID">
        <input type="submit" name="arquivoID" value="buscar">
    </form>
    </br>
    <form method="post">
        <input type="submit" name="aplicativosGratis" value="listar aplicativos gratis">
    </form>
    </br>
    <form method="post">
        <input type="submit" name="aplicativosAvaliados" value="listar aplicativos mais avaliados">
    </form>
    </br>

</br> 
</body>
</html>
<?php
session_start();
ini_set('memory_limit', '4096M');
require_once('criaIndices.php');
require_once('manipulaIndices.php');

$arquivo = new CriaIndices(false, 20, false);
$pesquisa = new ManipulaIndices();

if (isset($_POST['vetorIndice'])) {
    echo $pesquisa->pesquisaIndiceNoVetor($_POST['textoVetorIndice']);
    return;
}

if (isset($_POST['arquivoNome'])) {
    echo $pesquisa->pesquisaIndicePorNome($_POST['textoArquivoNome']);
    return;
}

if (isset($_POST['arquivoID'])) {
    echo $pesquisa->pesquisaIndicePorId($_POST['textoArquivoID']);
    return;
}

if (isset($_POST['arvoreIndice'])) {
    echo $pesquisa->pesquisaIndiceArvore($_POST['textoArvoreIndice']);
    return;
}

if (isset($_POST['aplicativosGratis'])) {
    $pesquisa->listaAplicativosGratuitos(20000);
    return;
}

if (isset($_POST['aplicativosAvaliados'])) {
    $pesquisa->listaAplicativosMaisAvaliados(20000);
    return;
}

$arquivo->executar();
$pesquisa->montaVetorIndice($arquivo->getVetor());
$pesquisa->montaArvoreIndice($arquivo->getArvore());
echo 'indices criados<br/><br/>';
?>
