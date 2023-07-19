<?php
include "config.php";
$sql = mysqli_query($conexao, "SELECT * FROM produtos");
$skuDisponivel = true;

function invalidoRedirect($erro){
   header("location: criar-pd.php?erro=".$erro);
}
if (!empty($_FILES['pic']) && strlen($_FILES['pic']["name"]) > 0 && !empty($_POST['sku']) && !empty($_POST['preco_custo']) && !empty($_POST['preco_revenda']) && !empty($_POST['json_valores'])  && !empty($_POST['titulo-pd'])) {
   function compressImage($source_path, $destination_path, $quality) {
    $info = getimagesize($source_path);

    if ($info['mime'] == 'image/webp') {
      $image = imagecreatefromwebp($source_path);
  }else if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($source_path);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source_path);
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
   while ($rest = mysqli_fetch_array($sql)) {
      if($rest["sku"] == $sku){
         $skuDisponivel = false;
         invalidoRedirect(1);
         break;
      }
   }


   $ext = strtolower(substr($_FILES['pic']['name'], -4)); //Pegando extensão do arquivo
   $imagem_nome = date("Y.m.d-H.i.s") . $ext; //Definindo um novo nome para o arquivo
   $dir = './imagens/'; //Diretório para uploads 
   compressImage($_FILES['pic']['tmp_name'], $dir . $imagem_nome, 11);
   //move_uploaded_file(); //Fazer upload do arquivo
   $sql_img = mysqli_query($conexao, "INSERT INTO produtos (sku, data_criacao, nome, imagem, categoria_id, anotacoes, preco_revenda, preco_compra, notificacao, tabelas_json) VALUES ('{$sku}', '{$data}', '{$titulo_pd}', '{$dir}{$imagem_nome}', 0,'so teste', '{$preco_revenda}', '{$preco_custo}', 0, '\'".$json_valores."\'')");
      ?>
   <html>
      <head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet"> 
         <style>
            body{
               background-color: #45c677;
               color: #fff;
            font-family: 'Mukta', sans-serif;
            letter-spacing: 2px;
            }
            #add-new-btn{
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
         <div align="center"><h1>Produto Adicionado com sucesso</h1>
         <br>
         <a href="criar-pd.php"><button id="add-new-btn">Adicionar novo Pd</button></a>
         <a href="index.php"><button id="ver-btn">Ver Estoque</button></a>
         </div>
      </body>
   </html>
   <?php
   //echo ("Imagen enviada com sucesso!");
}else if(strlen($_FILES['pic']["name"]) == 0 && !empty($_POST['sku']) && !empty($_POST['preco_custo']) && !empty($_POST['preco_revenda']) && !empty($_POST['json_valores'])  && !empty($_POST['titulo-pd'])){// se imagem estiver vazia
   
   
   $sku = $_POST['sku'];
   $preco_custo = $_POST['preco_custo'];
   $preco_revenda = $_POST['preco_revenda'];
   $json_valores = $_POST["json_valores"];
   $titulo_pd = $_POST["titulo-pd"];
   $data = date('Y-m-d');
   while ($rest = mysqli_fetch_array($sql)) {
      if($rest["sku"] == $sku){
         $skuDisponivel = false;
         invalidoRedirect(1);
         break;
      }
   }
   $sql_img = mysqli_query($conexao, "INSERT INTO produtos (sku, data_criacao, nome, imagem, categoria_id, anotacoes, preco_revenda, preco_compra, notificacao, tabelas_json) VALUES ('{$sku}', '{$data}', '{$titulo_pd}', 'imagem_nao_cadastrada.png', 0,'so teste', '{$preco_revenda}', '{$preco_custo}', 0, '\'".$json_valores."\'')");
   ?>
   <html>
      <head>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet"> 
         <style>
            body{
               background-color: #45c677;
               color: #fff;
            font-family: 'Mukta', sans-serif;
            letter-spacing: 2px;
            }
            #add-new-btn{
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
         <div align="center"><h1>Produto Adicionado com sucesso</h1>
         <br>
         <a href="criar-pd.php"><button id="add-new-btn">Adicionar novo Pd</button></a>
         <a href="index.php"><button id="ver-btn">Ver Estoque</button></a>
         </div>
      </body>
   </html>
   <?php
   //echo ("Imagen enviada com sucesso!");
}

else{
   invalidoRedirect(2);
}
?>
