<?php session_start();
$_SESSION["pagina_atual"] = "index.php";
?>


<?php include "config.php" ?>
<html lang="pt-br">

<head>
    <title>Modo Leitura</title>
    <script>
    </script>    
    <style>
    #indicativo-modo {
        color: #fff;
        background-color: rgb(50,150,150);
        padding: 5px;
        margin: 38px;
        position: relative;
        top: 5px;
    }
    #valores-comercio{
        max-width: 210px;
    }
    </style>
    <link rel="stylesheet" type="text/css" href="estilo.css" />
    <meta charset="utf-8">
    <script>
        Object.prototype.size = function(obj) {
            let size = 0,
                key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) {
                    size++
                };
            };
            return size;
        };
        var idProdutosSequencial = [];
        var estoque = [];
        var dadosEstoque = [];
        var pdsPorId = [];
        var imagens = [];
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div id="div-procurar-pd">
        <input type="text" id="procurar-pd">
        <button id="procurar-pesquisa-btn"><i class="fa fa-search"></i></button>
        <h3>Aperte "Ctrl" + "Shift" para fechar ou "Enter" Para pesquisar</h3>
        <div id="busca_opcoes">
        </div>
    </div>


    <br>
    <?php include "partes/cabecalho.php" ?>
    <?php include "partes/busca_opcoes.php" ?>
    <span id="indicativo-modo">Modo Leitura</span>
    <div id="painel-edicao-pdt">
        <div style="display:flex;">
            <h3 id="titulo-pdt"></h3>
            <h3 id="indicador-pdt"></h3>
        </div>
        <div id="linha-master">
        <div id="form-imagem">
                <div id="imagem-pd"></div>
            </div>
            <div id="painel-de-tabelas"></div>
            
            <form method="POST" id="formulario-salvamento" action="salvar.php">
                <input type="hidden" value="" name="salvamento" id="valores-salvamento">
                <input type="hidden" value="" name="pdAberto" id="session_input_pdAberto">
                <input type="hidden" value="" name="linkDirecionado" id="link_direcionado_input">
            </form>
        </div>
        <br><br>
        <script>
            var valorescomercioVisivel = false;
            function verValores(){
                if( valorescomercioVisivel == false){
                    valorescomercioVisivel = true;
                document.getElementById("valores-comercio").style.color="rgb(50,50,50)";
                }else{
                valorescomercioVisivel = false;
                document.getElementById("valores-comercio").style.color="transparent";
                }
            }
        </script>
        <div id="valores-comercio" onclick="verValores()">

        <b>Custo:</b>
        <span  id="valor-custo" name="valor-custo" type="text"></span>
        <b>Venda:</b>
        <span  id="valor-venda" name="valor-venda" type="text"></span>
        </div>
    </div>
    <script>
        var campoBuscaAtiva = false;
        var idSequencialPdAberto;
        var indiceVerticalTamanhosSelecionados = 0;
        /*
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        SISTEMA DE DESFAZER E REFAZER
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        */
        var acoesDesfazer = [];
        var acoesRefazer = [];
        var indiceDesfazer = 0;
        var indiceRefazer = 0;

        function addDesfazer(pdId, tab, indice) {
            acoesDesfazer[indiceDesfazer] = [];
            acoesDesfazer[indiceDesfazer]["pdId"] = pdId;
            acoesDesfazer[indiceDesfazer]["tab"] = tab;
            acoesDesfazer[indiceDesfazer]["indice"] = indice;
            indiceDesfazer += 1;
        }

        function addRefazer(pdId, tab, indice) {
            acoesRefazer[indiceRefazer] = [];
            acoesRefazer[indiceRefazer]["pdId"] = pdId;
            acoesRefazer[indiceRefazer]["tab"] = tab;
            acoesRefazer[indiceRefazer]["indice"] = indice;
            indiceRefazer += 1;
        }

        function refazer() {
            if (indiceRefazer > 0) {

                indiceRefazer -= 1;
                abrirPd(acoesRefazer[indiceRefazer]["pdId"]);
                crosstabSelecionado = acoesRefazer[indiceRefazer]["tab"];
                indiceVerticalTamanhosSelecionados = acoesRefazer[indiceRefazer]["indice"];
                json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"] -= 1;
                colunaQtd[crosstabSelecionado][indiceVerticalTamanhosSelecionados].innerHTML = json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"];
                totalQuantidadeEstoque[crosstabSelecionado] -= 1;
                rodapeValorTotal[crosstabSelecionado].innerHTML = (totalQuantidadeEstoque[crosstabSelecionado]);
                animarTamanhoAlterado(indiceVerticalTamanhosSelecionados);
                indiceVerticalTamanhosSelecionados -= 1;
                addDesfazer(acoesRefazer[indiceRefazer]["pdId"], acoesRefazer[indiceRefazer]["tab"], acoesRefazer[indiceRefazer]["indice"]);
            }
        }

        function desfazer() {
            if (indiceDesfazer > 0) {
                indiceDesfazer -= 1;
                abrirPd(acoesDesfazer[indiceDesfazer]["pdId"]);
                crosstabSelecionado = acoesDesfazer[indiceDesfazer]["tab"];
                indiceVerticalTamanhosSelecionados = acoesDesfazer[indiceDesfazer]["indice"];
                json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"] += 1;
                colunaQtd[crosstabSelecionado][indiceVerticalTamanhosSelecionados].innerHTML = json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"];
                totalQuantidadeEstoque[crosstabSelecionado] += 1;
                rodapeValorTotal[crosstabSelecionado].innerHTML = (totalQuantidadeEstoque[crosstabSelecionado]);
                animarTamanhoAlterado(indiceVerticalTamanhosSelecionados);
                indiceVerticalTamanhosSelecionados -= 1;
                addRefazer(acoesDesfazer[indiceDesfazer]["pdId"], acoesDesfazer[indiceDesfazer]["tab"], acoesDesfazer[indiceDesfazer]["indice"]);
            }
        }

        /*
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        -----------------------------
        */
        var pressedCtrl = false;
        //Quando uma tecla for liberada verifica se é o CTRL para notificar que CTRL não está pressionado
        document.onkeyup = function(e) {
            if (e.which == 17)
                pressedCtrl = false;
        }
        // Quando alguma tecla for pressionada:
        // Primeiro if - verifica se é o CTRL e avisa que CTRL está pressionado
        // Segundo if - verifica se a tecla é o "s" (keycode 83) para executar a ação
        document.onkeydown = function(e) {
            if (e.which == 17)
                pressedCtrl = true;
            if (e.which == 83 && pressedCtrl == true) {
                //Aqui vai o código e chamadas de funções para o ctrl+s
                //alert("CTRL + S pressionados");
            }
            if (e.which == 90 && pressedCtrl == true) {
                //"Ctrl + Z"
                desfazer();
            }
            if (e.which == 89 && pressedCtrl == true) {
                //"Ctrl + Y"
                refazer();
            }
            if (e.which == 16 && pressedCtrl == true) {
                //Tecla Shift
                if (campoBuscaAtiva == false) {
                    campoBuscaAtiva = true;
                    var divPesquisa = document.getElementById("div-procurar-pd");
                    var btnPesquisa = document.getElementById("procurar-pesquisa-btn");
                    var campoBusca = document.getElementById("procurar-pd");
                    var textoPesquisa = document.getElementById("procurar-pd").value;


                    campoBusca.addEventListener('keyup', (event) => {
                        podeSalvar = false;
                    textoPesquisa = document.getElementById("procurar-pd").value;
                        sugerirOpcoes(textoPesquisa);
                    
                    })


                    divPesquisa.style.display = "block";
                    document.getElementById("procurar-pd").focus();
                    document.getElementById("procurar-pd").value = "";
                    btnPesquisa.onclick = function() {

                        if (estoque[textoPesquisa] !== null) {
                            abrirPd(pdsPorId[textoPesquisa]);
                        }
                    }
                } else {
                    campoBuscaAtiva = false;
                    document.getElementById("div-procurar-pd").style.display = "none";

                }





            }
        }
        document.querySelector('body').addEventListener('keydown', function(event) {
            var tecla = event.keyCode;
            
            

            if (tecla == 37) {
                //Seta Esquerda
                if (crosstabSelecionado > 0) {
                    crosstabSelecionado -= 1;
                    subir();
                    descer();
                    for (var i = 0; i < botaoRemover[crosstabSelecionado + 1].length; i++) {
                        botaoRemover[crosstabSelecionado + 1][i].setAttribute("class", "botao-remover1");
                    }
                }




            }
            if (tecla == 39) {
                //Seta Direita
                if (crosstabSelecionado < (totalTabs - 1)) {
                    crosstabSelecionado += 1;
                    subir();
                    descer();
                    for (var i = 0; i < botaoRemover[crosstabSelecionado - 1].length; i++) {
                        botaoRemover[crosstabSelecionado - 1][i].setAttribute("class", "botao-remover1");
                    }
                }




            }
            if (tecla == 13) {
                //Tecla ENTER

                if (campoBuscaAtiva == true) {
                    var textoPesquisa = document.getElementById("procurar-pd").value;
                    if (estoque[textoPesquisa] !== null) {

                        document.getElementById("div-procurar-pd").style.display = "none";
                        campoBuscaAtiva = false;
                        if (pdsPorId[textoPesquisa] !== undefined) {
                            abrirPd(pdsPorId[textoPesquisa]);
                        }


                    }
                }






            } else if (tecla == 33) {
                // Page Up
                if (idSequencialPdAberto > 0) {
                    abrirPd(idProdutosSequencial[idSequencialPdAberto - 1]);
                }
            } else if (tecla == 38) {
                // seta pra CIMA
                subir();
            } else if (tecla == 34) {
                // Page Down
                if (idSequencialPdAberto < (idProdutosSequencial.length - 1)) {
                    abrirPd(idProdutosSequencial[idSequencialPdAberto + 1]);
                }
            } else if (tecla == 40) {
                // seta pra BAIXO
                descer();

            }

        });

        function subir() {

            if (indiceVerticalTamanhosSelecionados > 0) {
                indiceVerticalTamanhosSelecionados -= 1;
                for (var i = 0; i < botaoRemover[crosstabSelecionado].length; i++) {
                    botaoRemover[crosstabSelecionado][i].setAttribute("class", "botao-remover1");
                }
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].setAttribute("class", "botao-remover-selecionado");
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].focus();
            }
        }

        function descer() {
            if (indiceVerticalTamanhosSelecionados < botaoRemover[crosstabSelecionado].length-1) {
                indiceVerticalTamanhosSelecionados += 1;
                for (var i = 0; i < botaoRemover[crosstabSelecionado].length - 1; i++) {
                    botaoRemover[crosstabSelecionado][i].setAttribute("class", "botao-remover1");
                }
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].setAttribute("class", "botao-remover-selecionado");
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].focus();
            }
        }

    function salvar(linkDirecionado) {
            //NÃO SALVA NADA
            //FUNÇÃO REALIZADA APENAS PARA REDIRECIONAR PARA A PAGINA CORETA
            window.location.href = linkDirecionado;
        }

        var totalDeTabelas = 0;
        var totalDeTamanhos = 0;
        var totalQuantidadeEstoque = [];



        var indiceClicado = 0;
        var totalTabs = 0;
        var tabela = [];
        var crosstabSelecionado = 0;
        var linha = [];
        var colunaTam = [];
        var colunaQtd = [];
        var colunaLocal = [];
        var linhaCabecalho = [];
        var cabTd1 = [];
        var cabTd2 = [];
        var cabTd3 = [];
        var linhaRodape = [];
        var linhaRodape = [];
        var rodapeTd1 = [];
        var rodapeValorTotal = [];
        var pdSelecionado = document.getElementById("btn-abrir-pdt-" + idProdutosSequencial[0]);

        //CRIACAO LINHA CABCALHO

        function animarTamanhoAlterado(indice) {
            var crossTemporario = crosstabSelecionado;
            colunaQtd[crossTemporario][indice].style.color = "red";
            setTimeout(() => {
                colunaQtd[crossTemporario][indice].style.color = "rgb(0,0,0)";
            }, 1000);
        }

        function abrirPd(id) {
            document.getElementById("session_input_pdAberto").value = id;
            totalTabs = 0;
            indiceVerticalTamanhosSelecionados = 0;
            pdSelecionado.style.background = "rgb(240,240,240)";
            pdSelecionado.style.color = "#000";
            pdSelecionado = document.getElementById("btn-abrir-pdt-" + id);
            pdSelecionado.style.background = "#000";
            pdSelecionado.style.color = "#fff";
            idSequencialPdAberto = idProdutosSequencial.indexOf(id);

            json_estoque = estoque[id];
            totalDeTamanhos = Object.size(json_estoque[0]);
            document.getElementById("painel-de-tabelas").innerHTML = "";
            json_dados_extras = dadosEstoque[id];
            document.getElementById("imagem-pd").innerHTML = "";
            document.getElementById("imagem-pd").appendChild(imagens[id]);
            document.getElementById("titulo-pdt").innerHTML = json_dados_extras["nome"];
            document.getElementById("indicador-pdt").innerHTML = json_dados_extras["sku"];
            document.getElementById("valor-venda").innerHTML = json_dados_extras["venda"];
            document.getElementById("valor-custo").innerHTML = json_dados_extras["custo"];


            linha = [];

            colunaTam = [];
            colunaQtd = [];

            totalDeTabelas = Object.size(json_estoque);
            totalDeTamanhos = Object.size(json_estoque[0]);
            for (var tab = 0; tab < totalDeTabelas; tab++) {
                totalQuantidadeEstoque[tab] = 0;
                linhaCabecalho[tab] = document.createElement("tr");
                cabTd1[tab] = document.createElement("td");
                cabTd2[tab] = document.createElement("td");
                cabTd3[tab] = document.createElement("td");
                cabTd1[tab].innerHTML = " TAMANHO ";
                cabTd2[tab].innerHTML = " QUANTIDADE ";
                cabTd3[tab].innerHTML = "LOCALIZAÇÃO";
                linhaCabecalho[tab].appendChild(cabTd1[tab]);
                linhaCabecalho[tab].appendChild(cabTd2[tab]);
                linhaCabecalho[tab].appendChild(cabTd3[tab]);
                //CRIACAO LINHA RODAPE COM TOTAL
                linhaRodape[tab] = document.createElement("tr");
                rodapeTd1[tab] = document.createElement("td");
                rodapeTd1[tab].innerHTML = "TOTAL";
                rodapeValorTotal[tab] = document.createElement("td");
                linha[tab] = [];
                colunaTam[tab] = [];
                colunaLocal[tab] = [];
                colunaQtd[tab] = [];
                tabela[tab] = document.createElement("table");
                tabela[tab].appendChild(linhaCabecalho[tab]);
                for (var i = 0; i < totalDeTamanhos; i++) {
                    totalQuantidadeEstoque[tab] = totalQuantidadeEstoque[tab] + json_estoque[[tab]][i]["qtd"];
                    // CREATE ELEMENTS


                    linha[tab][i] = document.createElement("tr");
                    colunaTam[tab][i] = document.createElement("td");
                    colunaQtd[tab][i] = document.createElement("td");
                    colunaLocal[tab][i] = document.createElement("td");
                    //-----------------
                    if (i % 2 === 0) {
                        linha[tab][i].style.backgroundColor = "rgb(240,240,240)";
                    }
                    linha[tab][i].setAttribute("class", "linha-tabela-estoque");
                    colunaTam[tab][i].innerHTML = json_estoque[tab][i]["tam"];
                    colunaTam[tab][i].setAttribute("class", "col-tam");
                    colunaQtd[tab][i].innerHTML = json_estoque[tab][i]["qtd"];
                    colunaLocal[tab][i].innerHTML = json_estoque[tab][i]["local"];
                    linha[tab][i].appendChild(colunaTam[tab][i]);

                    linha[tab][i].appendChild(colunaQtd[tab][i]);
                    linha[tab][i].appendChild(colunaLocal[tab][i]);
                    tabela[tab].appendChild(linha[tab][i]);

                }

                document.getElementById("painel-de-tabelas").appendChild(tabela[tab]);
                rodapeValorTotal[tab].innerHTML = "" + totalQuantidadeEstoque[tab];
                linhaRodape[tab].appendChild(rodapeTd1[tab]);
                linhaRodape[tab].appendChild(rodapeValorTotal[tab]);
                tabela[tab].appendChild(linhaRodape[tab]);
                totalTabs += 1;
            }
            
            document.getElementById("btn-abrir-pdt-"+id).scrollIntoView();
            setTimeout(descer, 10);
        }
    </script>
    <?php
    if (!isset($_SESSION["pd_aberto"])) {
        echo "<script>abrirPd(idProdutosSequencial[0]);</script>";
    } else {
    ?>
        <script>
            abrirPd(<?php echo $_SESSION['pd_aberto']; ?>);
        </script>
    <?php
    }
    ?>
</body>





</html>