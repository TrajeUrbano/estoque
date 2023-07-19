<html>
<?php
include 'config.php';
$sql = mysqli_query($conexao, "SELECT * FROM produtos");
$cnt = 0;
?>
<script>
    var skus = [];
    var skuDisponivel = false;
    <?php
    while ($rest = mysqli_fetch_array($sql)) {
        $cnt += 1;
        $skuPd = $rest["sku"];
    ?>

        skus.push('<?php echo $skuPd; ?>');

    <?php
    } 
    
    //
    //
    //
    //
    if(isset($_GET['erro'])){
        $erro = $_GET["erro"];
        if($erro == 1){
            echo "window.alert('O SKU informado já está ocupado, tente outro');";
        }elseif($erro == 2){
            echo "window.alert('Algum Dado Não Foi preenchido, revise!');";
        }
    }
    
    ?>
</script>

<head>
    <title>Adicionar Produto</title>
    <style>
        body {
            background-color: rgb(210, 210, 210);
            padding-left: 10px;
        }

        #linha-master {
            display: flex;
            align-items: end;
            margin-left: 180px;
        }

        table {
            box-shadow: 0px 0px 20px 7px rgba(80, 80, 80, 0.2);
            height: 400px;
            font-family: 'Mukta', sans-serif;
            letter-spacing: 2px;
            margin-left: 40px;
        }

        td {
            -webkit-touch-callout: none;
            /* iPhone OS, Safari */
            -webkit-user-select: none;
            /* Chrome, Safari 3 */
            -khtml-user-select: none;
            /* Safari 2 */
            -moz-user-select: none;
            /* Firefox */
            -ms-user-select: none;
            /* IE10+ */
            user-select: none;
            /* Possível implementação no futuro */
            font-size: 20px;
            padding: 3px !important;
            /*border-right: 1px solid #000;*/
            margin: 0px;
            border-spacing: 0px;
        }

        .col {
            display: grid;
        }

        .lin {
            display: flex;
        }

        td input {
            height: 50px;
            background-color: transparent;
            border: 0px;
        }

        #titulo-pd {
            margin: 5px;
            height: 42px;
            border: 0px;
            padding-left: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-family: 'Mukta', sans-serif;
            letter-spacing: 2px;
            font-size: 18px;
            font-weight: bolder;
        }

        #btnsCategoria {
            margin-left: 40px;
        }

        #btnsCategoria button {
            padding: 14px;
            width: 100px;
            border: 0px;
            border-radius: 5px;
            margin: 3px;
            background-color: rgb(50, 50, 50);
            cursor: pointer;
            transition: background-color, 200ms;
            color: #fff;
        }

        #btnsCategoria button:hover {
            background-color: rgb(0, 0, 0);
        }

        #imagem-fundo {
            background-color: rgb(220, 220, 220);
        }

        #imagem-pd {
            height: 400px;
            width: 320px;
            background-image: url("imagens/icon-imagem.png");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 18%;
            cursor: pointer;
            margin-top: 8px;
            margin-left: 6px;
            margin-right: 6px;
        }

        #textos {
            margin-left: 180px;
        }

        .textos-inputs {
            height: 40px;
            margin: 2px;
            margin-top: 40px;
            border: 0px;
            padding: 5px;
        }
        #alerta-sku{
            font-size: 13px;
            font-weight: bolder;
            font-family: 'Mukta', sans-serif;
        }
    </style>

</head>

<body>
    <?php include "partes/cabecalho.php" ?>
    <br><br>


    <form method="POST" enctype="multipart/form-data" action="incluir-pd.php">
        <div id="linha-master">
            <div id="col1" class="col">
                <input type="text" name="titulo-pd" placeholder="Titulo do Produto" id="titulo-pd">
                <div id="imagem-fundo">
                    <input type="file" name="pic" accept="image/*" id="imagem-pd">
                </div>
            </div>
            <div id="col2" class="col">
                <div id="btnsCategoria" class="lin">
                    <button onClick="mudarPara()">Calça</button>
                    <button onClick="mudarPara()">Jaqueta</button>
                    <button onClick="mudarPara()">Bermuda</button>
                    <button onClick="mudarPara()">Camiseta</button>
                </div>
                <table id="tabela">
                </table>
            </div>
        </div>
        <div class="lin" id="textos">
            <div class="col"><input type="number" class="textos-inputs" name="sku" placeholder="Sku" id="sku" style="width: 70px;" onchange="validarSku()"><span id="alerta-sku"></span></div>
            <input type="text" class="textos-inputs" name="preco_custo" placeholder="Preço de Custo">
            <input type="text" class="textos-inputs" name="preco_revenda" placeholder="Preço de Revenda Aproximado">
        </div>
        <input type="hidden" name="json_valores" id="json_valores">
        <button type="submit">ADD</button>

    </form>


</body>
<script>function salvar(linkDirecionado) {
            //NÃO SALVA NADA
            //FUNÇÃO REALIZADA APENAS PARA REDIRECIONAR PARA A PAGINA CORETA
            window.location.href = linkDirecionado;
        }
    var categoriaId = 0;
    var novoPdJson = [
        [{
                "tam": "38",
                "qtd": 0,
                "local": "Maldivas 1 e 2",
            },
            {
                "tam": "40",
                "qtd": 0,
                "local": "Maldivas 1 e 2",
            },
            {
                "tam": "42",
                "qtd": 0,
                "local": "Maldivas 1 e 2",
            },
            {
                "tam": "44",
                "qtd": 0,
                "local": "Maldivas 1 e 2",
            },
            {
                "tam": "46",
                "qtd": 0,
                "local": "Maldivas 1 e 2",
            }
        ]
        ];
    var TamanhosLin = [];
    var tamanhosCol = [];
    var quantidadesCol = [];
    var localCol = [];
    var inputs = [];

    function validarSku() {
        var valor = "pd"+document.getElementById("sku").value;
        for (let i = 0; i < skus.length; i++) {
            if (valor !== skus[i]) {
                skuDisponivel = true;
            } else {
                skuDisponivel = false;
                break;
            }

        }
        if(skuDisponivel == false){
            document.getElementById("alerta-sku").innerHTML = "SKU indisponível";
            document.getElementById("alerta-sku").style.color = "red";
        }else{
            document.getElementById("alerta-sku").innerHTML = "SKU Disponível";
            document.getElementById("alerta-sku").style.color = "green";
        }
    }

    function alterarValorTam(qualInput, colIndice, valor) {
        if(qualInput == "qtd"){
            novoPdJson[0][colIndice][qualInput] = parseInt(valor, 10);
        }else{
            novoPdJson[0][colIndice][qualInput] = valor;
        }
       
        novoPdJsonString = JSON.stringify(novoPdJson);
        json_valores.value = novoPdJsonString;
    }
    function selecionarInput(input, index){
        inputs[input][index].select();
    }
    function textoInput(qualInput, colIndice, valorInicial) {
        if (inputs[qualInput] == null) {
            inputs[qualInput] = [];
        }
        inputs[qualInput][colIndice] = document.createElement("input");
        inputs[qualInput][colIndice].setAttribute("type", "text");
        inputs[qualInput][colIndice].setAttribute("value", valorInicial);
        inputs[qualInput][colIndice].setAttribute("class", "inputsTabela");
        inputs[qualInput][colIndice].setAttribute("onfocus", "selecionarInput('"+qualInput+"' ,'"+colIndice+"')");
        inputs[qualInput][colIndice].onchange = function() {
            alterarValorTam(qualInput, colIndice, inputs[qualInput][colIndice].value)
        };
    }
    for (let tam = 0; tam < novoPdJson[0].length; tam++) {
        //
        //NOME DO TAMANHO LOOPING
        TamanhosLin[tam] = document.createElement("tr");
        tamanhosCol[tam] = document.createElement("td");
        textoInput("tam", tam, novoPdJson[0][tam]["tam"]);
        tamanhosCol[tam].appendChild(inputs["tam"][tam]);
        //
        //QUANTIDADE DE CADA TAMANHO LOOPING
        quantidadesCol[tam] = document.createElement("td");
        textoInput("qtd", tam, novoPdJson[0][tam]["qtd"]);
        quantidadesCol[tam].appendChild(inputs["qtd"][tam]);
        //
        //LOCAL DE CADA TAMANHO
        localCol[tam] = document.createElement("td");
        textoInput("local", tam, novoPdJson[0][tam]["local"]);
        localCol[tam].appendChild(inputs["local"][tam]);
        //
        //

        TamanhosLin[tam].appendChild(tamanhosCol[tam]);
        TamanhosLin[tam].appendChild(quantidadesCol[tam]);
        TamanhosLin[tam].appendChild(localCol[tam]);
        if (tam % 2 === 0) {
            TamanhosLin[tam].style.backgroundColor = "rgb(240,240,240)";
        } else {

        }
        document.getElementById("tabela").appendChild(TamanhosLin[tam]);
    }
</script>
<script>
    'use strict'

    let photo = document.getElementById('imagem-pd');
    let file = document.getElementById('imagem-pd');

    photo.addEventListener('click', () => {
        file.click();
    });

    file.addEventListener('change', () => {

        if (file.files.length <= 0) {
            return;
        }

        let reader = new FileReader();

        reader.onload = () => {
            photo.style.background = "URL(" + reader.result + ")";
            photo.style.backgroundRepeat = "no-repeat";
            photo.style.backgroundSize = "contain";
        }

        reader.readAsDataURL(file.files[0]);
    });
</script>

</html>