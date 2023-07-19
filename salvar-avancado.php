<?php session_start(); ?>
<?php
include "config.php";
$sql = mysqli_query($conexao, "SELECT * FROM produtos");
$skuDisponivel = true;
function invalidoRedirect($erro)
{
    header("location: edicao-avancada.php?erro=" . $erro);
}
if ($_FILES['pic']["size"] > 0) {
    function compressImage($source_path, $destination_path, $quality)
    {
        $info = getimagesize($source_path);

        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source_path);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source_path);
        }elseif ($info['mime'] == 'image/webp') {
            $image = imagecreatefromwebp($source_path);
        }

        imagejpeg($image, $destination_path, $quality);

        return $destination_path;
    } 
    $sku = $_POST['sku'];
    $preco_custo = $_POST['preco_custo'];
    $preco_revenda = $_POST['preco_revenda'];
    $json_valores = $_POST["json_valores"];
    $imagem = $_FILES["pic"];
    $titulo_pd = $_POST["titulo-pd"];
    $data = date('Y-m-d');
    $permissao = false;
    while ($rest = mysqli_fetch_array($sql)) {
        if ($rest["sku"] == $sku && $rest["id"] !== $_SESSION["pd_aberto"]) {
            $skuDisponivel = false;
            invalidoRedirect(1);
            $permissao = false;
            break;
        }else{
            $permissao = true;
        }
    }

    if($permissao == true){
            $ext = strtolower(substr($_FILES['pic']['name'], -4)); //Pegando extensão do arquivo
    $imagem_nome = date("Y.m.d-H.i.s") . $ext; //Definindo um novo nome para o arquivo
    $dir = './imagens/'; //Diretório para uploads 
    compressImage($_FILES['pic']['tmp_name'], $dir . $imagem_nome, 11);
     //Fazer upload do arquivo
    $sql_img = mysqli_query($conexao, "UPDATE produtos SET tabelas_json = '\'" . $json_valores . "\'', nome = '" . $titulo_pd . "', preco_compra = '" . $preco_custo . "', preco_revenda = '" . $preco_revenda . "', sku = '". $sku = $_POST['sku'] . "', imagem = '{$dir}{$imagem_nome}', data_criacao = '" . $data . "' WHERE ID='" . $_SESSION["pd_aberto"] . "'");
    }

?>
    <html>

    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet">
        <style>
            body {
                background-color: #45c677;
                color: #fff;
                font-family: 'Mukta', sans-serif;
                letter-spacing: 2px;
            }

            #add-new-btn {
                background-color: black;
                border: 0px;
                color: #fff;
                padding: 12px;
                width: 170px;
                cursor: pointer;
                border-radius: 10px;

            }

            #ver-btn {
                background-color: #3d498c;
                border: 0px;
                color: #fff;
                padding: 12px;
                width: 170px;
                border-radius: 10px;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <div align="center">
            <h1>Alterações Salvas com Sucesso</h1>
            <br>
            <a href="criar-pd.php"><button id="add-new-btn">Adicionar novo Pd</button></a>
            <a href="index.php"><button id="ver-btn">Ver Estoque</button></a>
        </div>
    </body>

    </html>
<?php
    //echo ("Imagen enviada com sucesso!");

} else {
    $sku = $_POST['sku'];
    $preco_custo = $_POST['preco_custo'];
    $preco_revenda = $_POST['preco_revenda'];
    $json_valores = $_POST["json_valores"];
    $imagem = $_FILES["pic"];
    $titulo_pd = $_POST["titulo-pd"];
    $data = date('Y-m-d');
    $permissao = false;
    while ($rest = mysqli_fetch_array($sql)) {
        if ($rest["sku"] == $sku && $rest["id"] !== $_SESSION["pd_aberto"]) {
            $skuDisponivel = false;
            invalidoRedirect(1);
            $permissao = false;
            break;
        }else{
            $permissao = true;
        }
    }

    if ($permissao == true) {
        $sql_img = mysqli_query($conexao, "UPDATE produtos SET tabelas_json = '\'" . $json_valores . "\'', nome = '" . $titulo_pd . "', preco_compra = '" . $preco_custo . "', preco_revenda = '" . $preco_revenda . "', sku = '". $sku = $_POST['sku'] . "', data_criacao = '" . $data . "' WHERE ID='" . $_SESSION["pd_aberto"] . "'") or die("Ocorreu algum erro ao realizar a alteração");
    }    
    ?>
    <html>

    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet">
        <style>
            body {
                background-color: #45c677;
                color: #fff;
                font-family: 'Mukta', sans-serif;
                letter-spacing: 2px;
            }

            #add-new-btn {
                background-color: black;
                border: 0px;
                color: #fff;
                padding: 12px;
                width: 170px;
                cursor: pointer;
                border-radius: 10px;

            }

            #ver-btn {
                background-color: #3d498c;
                border: 0px;
                color: #fff;
                padding: 12px;
                width: 170px;
                border-radius: 10px;
                cursor: pointer;
            }
        </style>
    </head>

    <body>
        <div align="center">
            <h1>Alterações Salvas com Sucesso</h1>
            <br>
            <a href="criar-pd.php"><button id="add-new-btn">Adicionar novo Pd</button></a>
            <a href="index.php"><button id="ver-btn">Ver Estoque</button></a>
        </div>
    </body>

    </html>
<?php
}
