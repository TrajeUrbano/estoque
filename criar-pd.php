<?php session_start(); ?>
<html>
<?php
include 'config.php';
$sql = mysqli_query($conexao, "SELECT * FROM produtos");
$cnt = 0;
?>
<script>
    var skus = [];
    var skuDisponivel = false;
    var pdEditavel = [];
    <?php
    while ($rest = mysqli_fetch_array($sql)) {
        $cnt += 1;
        $skuPd = str_pad($rest['sku'] , 3 , '0' , STR_PAD_LEFT);
    ?>

        skus.push('<?php echo $skuPd; ?>');

    <?php


    }

    //
    //
    //
    //
    if (isset($_GET['erro'])) {
        $erro = $_GET["erro"];
        if ($erro == 1) {
            echo "window.alert('O SKU informado já está ocupado, tente outro');";
        } elseif ($erro == 2) {
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

            font-family: 'Mukta', sans-serif;
            color: rgb(60, 60, 60);
        }

        #linha-master {
            display: flex;
            align-items: end;
            margin-left: 40px;
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
            
            width: 150px;
        }

        #margem-pai {
            margin-left: 40px;
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
            margin-left: 45px;
        }

        .textos-inputs {
            height: 40px;
            margin: 2px;
            margin-top: 40px;
            border: 0px;
            padding: 5px;
        }

        #alerta-sku {
            font-size: 13px;
            font-weight: bolder;
            font-family: 'Mukta', sans-serif;
        }
        #botao-add-tabela{
            font-size: 22px;
            background-color: rgb(50,50,50);
            color: #fff;
            border-radius: 20px;
            padding-left: 15px;
            padding-right: 15px;
            padding-top: 3px;
            padding-bottom: 3px;
            font-family: 'Mukta', sans-serif;
            cursor: pointer;
            margin: 5px;
        }
        #espaco-tabelas{
            display: flex;
        }
        input{
            color: rgb(40,40,40);
            font-family: 'Mukta', sans-serif;
            font-size: 15px;
        }
    </style>

</head>

<body>
    <?php include "partes/cabecalho_sem_navegacao.php" ?>
    <br><br>
    <div id="margem-pai">
        <div id="btnsCategoria" class="lin">
            <button onClick="mudarPara('calca')">Calça</button>
            <button onClick="mudarPara('jaqueta')">Jaqueta</button>
            <button onClick="mudarPara('calca')">Bermuda</button>
            <button onClick="mudarPara('jaqueta')">Camiseta</button>
            <button onClick="mudarPara('plus size')">Macacão ++</button>
        </div>



        <form method="POST" enctype="multipart/form-data" action="incluir-pd.php">
            <div id="linha-master">
                <div id="col1" class="col">
                    &nbsp;&nbsp;&nbsp;&nbsp;Titulo:
                    <input type="text" name="titulo-pd" placeholder="Titulo do Produto" id="titulo-pd">
                    <div id="imagem-fundo">
                        <input type="file" name="pic" accept="image/*" id="imagem-pd">
                    </div>
                </div>
                <div id="col2" class="col">
                    <div id="espaco-tabelas">

                    </div>

                </div>
                <div id="botao-add-tabela" onclick="addTabela()" title="Adicionar Tabela">+</div>
            </div>
            <div class="lin" id="textos">
                <span class="textos-inputs"> Sku: </span>
                <div class="col"><input type="number" class="textos-inputs" name="sku" placeholder="Sku" id="sku" style="width: 70px;" onchange="validarSku()"><span id="alerta-sku"></span></div>
                <span class="textos-inputs">Custo: </span><input type="text" class="textos-inputs" name="preco_custo" id="preco_custo" placeholder="Preço de Custo">
                <span class="textos-inputs">Venda: </span><input type="text" class="textos-inputs" name="preco_revenda" id="preco_revenda" placeholder="Preço de Revenda Aproximado">
            </div>
            <input type="hidden" name="json_valores" id="json_valores">
            <button type="submit">Salvar Edições</button>

        </form>


    </div>
</body>
<script>
    function salvar(linkDirecionado) {
        //NÃO SALVA NADA
        //FUNÇÃO REALIZADA APENAS PARA REDIRECIONAR PARA A PAGINA CORETA
        window.location.href = linkDirecionado;
    }
    var categoriaId = 0;
    var TamanhosLin = [];
    var tamanhosCol = [];
    var quantidadesCol = [];
    var localCol = [];
    var inputs = [];
    var novoPdJson = [
        [{
                "tam": "38",
                "qtd": 0,
                "local": "Local",
            },
            {
                "tam": "40",
                "qtd": 0,
                "local": "Local",
            },
            {
                "tam": "42",
                "qtd": 0,
                "local": "Local",
            },
            {
                "tam": "44",
                "qtd": 0,
                "local": "Local",
            },
            {
                "tam": "46",
                "qtd": 0,
                "local": "Local",
            }
        ]
    ];
    function addTabela(){
        var indiceTabela = novoPdJson.length;
        novoPdJson[indiceTabela] = [];
        for(var i = 0; i < novoPdJson[0].length; i++){
            novoPdJson[indiceTabela][i] = {
                        "tam": novoPdJson[0][i]["tam"],
                        "qtd": novoPdJson[0][i]["qtd"],
                        "local": novoPdJson[0][i]["local"],
                    };
        }
        abrirProduto();
    }
    function mudarPara(categoria) {
        if (categoria == 'calca') {
            novoPdJson = [
                [{
                        "tam": "38",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "40",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "42",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "44",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "46",
                        "qtd": 0,
                        "local": "Local",
                    }
                ]
            ]
        } else if (categoria == 'jaqueta') {
            novoPdJson = [
                [{
                        "tam": "P",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "M",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "G",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "GG",
                        "qtd": 0,
                        "local": "Local",
                    },
                ]
            ]
        } else if (categoria == 'plus size') {
            novoPdJson = [
                [{
                        "tam": "46",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "48",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "50",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "52",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "54",
                        "qtd": 0,
                        "local": "Local",
                    },
                    {
                        "tam": "56",
                        "qtd": 0,
                        "local": "Local",
                    },
                ]
            ]
        }
        abrirProduto();
    }

    function validarSku() {
        var valor = document.getElementById("sku").value;
        for (let i = 0; i < skus.length; i++) {
            if (parseInt(valor) !== parseInt(skus[i])) {
                skuDisponivel = true;
            } else {
                skuDisponivel = false;
                break;
            }

        }
        if (skuDisponivel == false) {
            document.getElementById("alerta-sku").innerHTML = "SKU indisponível";
            document.getElementById("alerta-sku").style.color = "red";
        } else {
            document.getElementById("alerta-sku").innerHTML = "SKU Disponível";
            document.getElementById("alerta-sku").style.color = "green";
        }
    }

    function alterarValorTam(qualInput, colIndice, valor, tab) {
        if (qualInput == "qtd") {
            novoPdJson[tab][colIndice][qualInput] = parseInt(valor, 10);
        } else {
            novoPdJson[tab][colIndice][qualInput] = valor;
        }

        novoPdJsonString = JSON.stringify(novoPdJson);
        json_valores.value = novoPdJsonString;
    }

    function selecionarInput(input, index, tab) {
        inputs[tab][input][index].select();
    }

    function textoInput(qualInput, colIndice, valorInicial, tab) {
        if (inputs[tab] == null) {
            inputs[tab] = [];
            if (inputs[tab][qualInput] == null) {
                inputs[tab][qualInput] = [];
            }
        } else {
            if (inputs[tab][qualInput] == null) {
                inputs[tab][qualInput] = [];
            }
        }
        inputs[tab][qualInput][colIndice] = document.createElement("input");
        inputs[tab][qualInput][colIndice].setAttribute("type", "text");
        inputs[tab][qualInput][colIndice].setAttribute("value", valorInicial);
        inputs[tab][qualInput][colIndice].setAttribute("class", "inputsTabela");
        inputs[tab][qualInput][colIndice].setAttribute("onfocus", "selecionarInput('" + qualInput + "' ,'" + colIndice + "','" + tab + "')");
        inputs[tab][qualInput][colIndice].onchange = function() {
            alterarValorTam(qualInput, colIndice, inputs[tab][qualInput][colIndice].value, tab)
        };
    }
    var tabelaElements = [];

    function abrirProduto() {
        document.getElementById("espaco-tabelas").innerHTML = "";
        for (var tab = 0; tab <= (novoPdJson.length-1); tab++) {
            tabelaElements[tab] = document.createElement("table");
            TamanhosLin[tab] = [];
            tamanhosCol[tab] = [];
            textoInput[tab] = [];
            quantidadesCol[tab] = [];
            localCol[tab] = [];
            for (var tam = 0; tam < novoPdJson[tab].length; tam++) {
                //
                //NOME DO TAMANHO LOOPING
                TamanhosLin[tab][tam] = document.createElement("tr");
                tamanhosCol[tab][tam] = document.createElement("td");
                textoInput("tam", tam, novoPdJson[tab][tam]["tam"], tab);
                tamanhosCol[tab][tam].appendChild(inputs[tab]["tam"][tam]);
                //
                //QUANTIDADE DE CADA TAMANHO LOOPING
                quantidadesCol[tab][tam] = document.createElement("td");
                textoInput("qtd", tam, novoPdJson[tab][tam]["qtd"], tab);
                quantidadesCol[tab][tam].appendChild(inputs[tab]["qtd"][tam]);
                //
                //LOCAL DE CADA TAMANHO
                localCol[tab][tam] = document.createElement("td");
                textoInput("local", tam, novoPdJson[tab][tam]["local"], tab);
                localCol[tab][tam].appendChild(inputs[tab]["local"][tam]);
                //
                //

                TamanhosLin[tab][tam].appendChild(tamanhosCol[tab][tam]);
                TamanhosLin[tab][tam].appendChild(quantidadesCol[tab][tam]);
                TamanhosLin[tab][tam].appendChild(localCol[tab][tam]);
                if (tam % 2 === 0) {
                    TamanhosLin[tab][tam].style.backgroundColor = "rgb(240,240,240)";
                } else {

                }
                tabelaElements[tab].appendChild(TamanhosLin[tab][tam]);
            }
            document.getElementById("espaco-tabelas").appendChild(tabelaElements[tab]);
        }
    }
    abrirProduto();
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