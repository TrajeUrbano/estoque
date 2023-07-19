<?php session_start();
$_SESSION["pagina_atual"] = "edicao-livre.php";
?>
<?php include "config.php" ?>
<?php
//SALVANDO REFRESH NA TABELA dados_session
$date = date("Y-m-d H:i:s");
$dateComparativa = date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));
date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));

if (!isset($_SESSION["session_id"])) {
    $query_verificar_session_pre = mysqli_query($conexao, "SELECT * FROM dados_sessao ORDER BY id DESC LIMIT 1") or die("Erro ao verificar reload");
    while ($save_pre = mysqli_fetch_array($query_verificar_session_pre)) {
        if ($dateComparativa < $save_pre["data_hora"]) {
            echo "ha uma sessão recente - ";
            if ($save_pre["ultimo_status_salvo"] == false) {
                echo "<script>window.alert('Provavelmente há algum usuário online!!!')</script>";
            } else {
                echo "<script>window.alert('Há algum usuário FAZENDO ALTERAÇÕES NO ESTOQUE')</script>";
            }
        }
    }

    $query_session = mysqli_query($conexao, "INSERT INTO dados_sessao (data_hora, ultimo_status_salvo) VALUES ('${date}', false);") or die("Erro ao registrar reload");
    $query_verificar_session = mysqli_query($conexao, "SELECT * FROM dados_sessao ORDER BY id DESC LIMIT 1") or die("Erro ao verificar reload");
    while ($save = mysqli_fetch_array($query_verificar_session)) {
        $_SESSION["session_id"] = $save["id"];
    }
} else {
    $query_verificar_sesion_pre = mysqli_query($conexao, "SELECT * FROM dados_sessao ORDER BY id DESC LIMIT 20") or die("Erro ao verificar reload");
    while ($save_pre = mysqli_fetch_array($query_verificar_sesion_pre)) {
        if ($save_pre["id"] !== $_SESSION["session_id"]) {
            if ($dateComparativa < $save_pre["data_hora"]) {
                if ($save_pre["ultimo_status_salvo"] == false) {
                    echo "<script>window.alert('Provavelmente há algum usuário online!!!')</script>";
                } else {
                    echo "<script>window.alert('Há algum usuário fazendo alterações no Estoque!!! VOCÊ SERÁ REDIRECIONADO PARA O MODO LEITURA!!')</script>";
                    echo "<script>window.location.href='leitura.php'</script>";
                }
            }
        }
    }
    $id_session = $_SESSION['session_id'];
    $qry_atualizar_session = mysqli_query($conexao, "UPDATE dados_sessao SET data_hora = '$date', ultimo_status_salvo = false WHERE id = $id_session");
}





?>
<html lang="pt-br">

<head>
    <title>Modo Edição Estoques</title>
    <link rel="stylesheet" type="text/css" href="estilo.css" />
    <style>
        table input {
            height: 30px;
            font-family: 'Mukta', sans-serif;
            font-size: 17px;
            width: 150px;
            margin-top: 0px;
            margin-bottom: 0px;
            border: 0px;
            background-color: transparent;
        }

        .btn-deletar-tab {
            background-color: rgb(0, 0, 0);
            color: #fff;
            width: 38px;
            height: 34px;
            font-size: 17px;
            cursor: pointer;
            border: 0px;
            margin-left: 10px;
            margin-right: 10px;
            transition: background-color, 300ms;
        }

        #indicativo-modo {
            color: #fff;
            background-color: rgb(20, 160, 60);
            padding: 5px;
            margin: 38px;
            position: relative;
            top: 5px;
        }

        .btn-deletar-tab:hover {
            background-color: rgb(180, 0, 0);
        }
    </style>
    <meta charset="utf-8">
    <script>
        function loopSalvarAutomaticamente() {
            if (podeSalvar == true) {
                if (necessarioSalvar == true) {
                    salvar("conferencia.php");
                } else {
                    window.location.reload();
                }
            } else {
                podeSalvar = true;
            }

            setTimeout(loopSalvarAutomaticamente, 20000);
        }
        setTimeout(loopSalvarAutomaticamente, 20000);



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
        <h3>Aperte "Ctrl" para fechar ou "Enter" Para pesquisar</h3>
        <div id="busca_opcoes">
        </div>
    </div>


    <br>
    <?php include "partes/cabecalho.php" ?>
    <?php include "partes/busca_opcoes.php" ?>
    <span id="indicativo-modo">Modo Edição Livre</span>
    <div id="painel-edicao-pdt">
        <br>
        <h3 id="titulo-pdt" name="titulo-pd" type="text"></h3><br>
        <div id="linha-master">
            <div id="form-imagem">
                <div id="imagem-pd"></div>
            </div>
            <div id="painel-de-tabelas"></div>

            <form method="POST" id="formulario-salvamento" action="salvar.php">
                <input type="hidden" value="" name="notificacoes" id="dados-notificacoes">
                <input type="hidden" value="" name="pdAberto" id="session_input_pdAberto">
                <input type="hidden" value="" name="salvamento" id="valores-salvamento">
                <input type="hidden" value="" name="linkDirecionado" id="link_direcionado_input">
                <button onclick="salvar('conferencia.php')" id="btn-salvar"><i class="fa fa-save" style="font-size:22px;"></i></button>
            </form>
            <button id="add-tabela-estoque" onclick="adicionarTab()"><i class="fa fa-plus" aria-hidden="true"></i></button>
        </div> <br><br>
        <div id="linha-2">
            <div id="valores-comercio" onclick="verValores()">

                <b>Custo:</b>
                <span id="valor-custo" name="valor-custo" type="text"></span>
                <b>Venda:</b>
                <span id="valor-venda" name="valor-venda" type="text"></span>
                <b>Total Investido:</b>
                <span id="total-custo" name="total-custo" type="text"></span>
            </div>
            <button id="notificacao-btn-painel" title="É necessário conferir estoque das plataformas" onclick="ativarNotificacaoPd()"><i class="fa fa-bell" aria-hidden="true" style="font-size: 22px;" id="icon-notificacao"></i></button>
        </div>

    </div>
    <script>
        var campoBuscaAtiva = false; // bool que ve se o usuário está pesquisando algum pd
        var necessarioSalvar = false; // bool que verifica se o estoque deve salvar ou só atualizar, a cada 10 segundos ele verifica a necessidade
        var podeSalvar = false; //bool que verifica se o usuário está mechendo no estoque neste exato momento, se sim o sistema não atualiza
        var idSequencialPdAberto;
        var indiceVerticalTamanhosSelecionados = 0;
        var pdAberto = 0;
        document.onkeyup = function(e) {
            if (e.which == 17)
                pressedCtrl = false;
        }
        document.onkeydown = function(e) {
            if (e.which == 17)
                pressedCtrl = true;
            if (e.which == 83 && pressedCtrl == true) {
                //Aqui vai o código e chamadas de funções para o ctrl+s
                //alert("CTRL + S pressionados");
            }
            if (e.which == 16 && pressedCtrl == true) {
                //Tecla Shift
                if (campoBuscaAtiva == false) {
                    podeSalvar = false;
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
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                    crosstabSelecionado -= 1;
                    subir();
                    descer();
                    for (var i = 0; i < botaoRemover[crosstabSelecionado + 1].length; i++) {
                        botaoRemover[crosstabSelecionado + 1][i].setAttribute("class", "botao-remover1");
                    }
                }




            }
            if (tecla == 39) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
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
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                    var textoPesquisa = document.getElementById("procurar-pd").value;
                    if (estoque[textoPesquisa] !== null) {

                        document.getElementById("div-procurar-pd").style.display = "none";
                        campoBuscaAtiva = false;
                        abrirPd(pdsPorId[textoPesquisa]);
                    }
                }





            } else if (tecla == 33) {
                // Page Up
                if (idSequencialPdAberto > 0) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                    abrirPd(idProdutosSequencial[idSequencialPdAberto - 1]);
                }
            } else if (tecla == 38) {
                // seta pra CIMA
                subir();
            } else if (tecla == 34) {
                // Page Down
                if (idSequencialPdAberto < (idProdutosSequencial.length - 1)) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                    abrirPd(idProdutosSequencial[idSequencialPdAberto + 1]);
                }
            } else if (tecla == 40) {
                // seta pra BAIXO
                descer();

            }

        });

        function subir() {

            if (indiceVerticalTamanhosSelecionados > 0) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                indiceVerticalTamanhosSelecionados -= 1;
                for (var i = 0; i < botaoRemover[crosstabSelecionado].length; i++) {
                    botaoRemover[crosstabSelecionado][i].setAttribute("class", "botao-remover1");
                }
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].setAttribute("class", "botao-remover-selecionado");
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].focus();
            }
        }

        function descer() {
            if (indiceVerticalTamanhosSelecionados < botaoRemover[crosstabSelecionado].length) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                indiceVerticalTamanhosSelecionados += 1;
                for (var i = 0; i < botaoRemover[crosstabSelecionado].length - 1; i++) {
                    botaoRemover[crosstabSelecionado][i].setAttribute("class", "botao-remover1");
                }
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].setAttribute("class", "botao-remover-selecionado");
                botaoRemover[crosstabSelecionado][indiceVerticalTamanhosSelecionados].focus();
            }
        }
        //
        //
        //
        function ativarNotificacaoPd() {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            const indexPd = notificacoesAtivas.findIndex(object => {
                return object.id === idPdAberto;
            });
            if (notificacoesAtivas[indexPd]["ativo"] != 1) {
                notificacoesAtivas[indexPd]["ativo"] = 1;
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell notificacao-ativa");
            } else {
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell");
                notificacoesAtivas[indexPd]["ativo"] = 0;
            }
            menuNotificacoes();
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
        function salvar(linkDirecionado) {
            document.getElementById("dados-notificacoes").setAttribute("value", JSON.stringify(notificacoesAtivas));
            var stringFinalEstoqueSalvamento = JSON.stringify(estoque);
            document.getElementById("link_direcionado_input").value = linkDirecionado;
            document.getElementById("valores-salvamento").setAttribute("value", stringFinalEstoqueSalvamento);
            document.getElementById("formulario-salvamento").submit();
        }

        var totalDeTabelas = 0;
        var totalDeTamanhos = 0;
        var totalQuantidadeEstoque = [];


        var idPdAberto = 0;
        var indiceClicado = 0;
        var totalTabs = 0;
        var tabela = [];
        var crosstabSelecionado = 0;
        var linha = [];
        var colunaTam = [];
        var colunaQtd = [];
        var botaoRemover = [];
        var colunaLocal = [];
        var linhaCabecalho = [];
        var cabTd1 = [];
        var cabTd2 = [];
        var cabTd3 = [];
        var td1 = [];
        var td2 = [];
        var td3 = [];
        var linhaRodape = [];
        var rodapeTd1 = [];
        var btnDeletarTab = [];
        var rodapeValorTotal = [];
        var rodapeTd3 = [];
        var pdSelecionado = document.getElementById("btn-abrir-pdt-" + idProdutosSequencial[0]);

        //CRIACAO LINHA CABCALHO
        function adicionarTab() {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            var indiceTabela = estoque[idPdAberto].length;
            estoque[idPdAberto][indiceTabela] = [];
            for (var i = 0; i < estoque[idPdAberto][0].length; i++) {
                estoque[idPdAberto][indiceTabela][i] = {
                    "tam": estoque[idPdAberto][0][i]["tam"],
                    "qtd": estoque[idPdAberto][0][i]["qtd"],
                    "local": estoque[idPdAberto][0][i]["local"],
                };
            }
            abrirPd(idPdAberto);
        }
        ////-------------------------------------
        ////-------------------------------------
        ////-------------------------------------
        ////-------------------------------------
        function deletarTab(tab) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            ntab = tab + 1; {
                var r = confirm("Tem certeza que deseja Deletar a Tabela " + ntab + "?");
                if (r == true) {
                    estoque[idPdAberto].splice(tab, 1);
                    abrirPd(idPdAberto);
                } else {}
                document.getElementById("demo").innerHTML = x;
            }
        }

        function selecionarInput(tipoDado, x, tab) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            if (tipoDado == "tam") {
                colunaTam[tab][x].select();
            } else if (tipoDado == "qtd") {
                colunaQtd[tab][x].select();
            } else if (tipoDado == "local") {
                colunaLocal[tab][x].select();
            }

        }

        function alterarValorTam(tipoDado, colIndice, tab) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            necessarioSalvar = true;
            if (tipoDado == "tam") {
                json_estoque[tab][colIndice][tipoDado] = colunaTam[tab][colIndice].value;
            } else if (tipoDado == "qtd") {
                json_estoque[tab][colIndice][tipoDado] = parseInt(colunaQtd[tab][colIndice].value, 10);

            } else if (tipoDado == "local") {
                json_estoque[tab][colIndice][tipoDado] = colunaLocal[tab][colIndice].value;
            }
        }

        function abrirPd(id) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            idPdAberto = id;
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
            document.getElementById("valor-venda").innerHTML = json_dados_extras["venda"];
            document.getElementById("valor-custo").innerHTML = json_dados_extras["custo"];


            linha = [];
            btnDeletarTab = [];
            colunaTam = [];
            colunaQtd = [];
            botaoRemover = [];

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
                rodapeTd3[tab] = document.createElement("td");
                rodapeValorTotal[tab] = document.createElement("td");
                linha[tab] = [];
                colunaTam[tab] = [];
                colunaLocal[tab] = [];
                botaoRemover[tab] = [];
                colunaQtd[tab] = [];
                tabela[tab] = document.createElement("table");
                tabela[tab].appendChild(linhaCabecalho[tab]);
                for (var i = 0; i < totalDeTamanhos; i++) {
                    totalQuantidadeEstoque[tab] = totalQuantidadeEstoque[tab] + json_estoque[[tab]][i]["qtd"];
                    // CREATE ELEMENTS

                    td1[tab] = [];
                    td2[tab] = [];
                    td3[tab] = [];

                    linha[tab][i] = document.createElement("tr");
                    td1[tab][i] = document.createElement("td");
                    td2[tab][i] = document.createElement("td");
                    td3[tab][i] = document.createElement("td");
                    colunaTam[tab][i] = document.createElement("input");
                    colunaQtd[tab][i] = document.createElement("input");
                    colunaLocal[tab][i] = document.createElement("input");
                    td1[tab][i].appendChild(colunaTam[tab][i]);
                    td2[tab][i].appendChild(colunaQtd[tab][i]);
                    td3[tab][i].appendChild(colunaLocal[tab][i]);

                    //DEFININDO ON FOCUS DOS INPUTS
                    colunaTam[tab][i].setAttribute("onfocus", "selecionarInput('tam' ,'" + i + "' ,'" + tab + "')");
                    colunaQtd[tab][i].setAttribute("onfocus", "selecionarInput('qtd' ,'" + i + "' ,'" + tab + "')");
                    colunaLocal[tab][i].setAttribute("onfocus", "selecionarInput('local' ,'" + i + "' ,'" + tab + "')");
                    //DEFININCO ACAO AO ALTERAR INPUTS

                    colunaTam[tab][i].setAttribute("onchange", "alterarValorTam('tam', '" + i + "', '" + tab + "')");
                    colunaQtd[tab][i].setAttribute("onchange", "alterarValorTam('qtd', '" + i + "', '" + tab + "')");
                    colunaLocal[tab][i].setAttribute("onchange", "alterarValorTam('local', '" + i + "', '" + tab + "')");

                    //-----------------
                    if (i % 2 === 0) {
                        linha[tab][i].style.backgroundColor = "rgb(240,240,240)";
                    }
                    linha[tab][i].setAttribute("class", "linha-tabela-estoque");
                    colunaTam[tab][i].value = json_estoque[tab][i]["tam"];
                    colunaTam[tab][i].setAttribute("class", "col-tam");
                    colunaQtd[tab][i].value = json_estoque[tab][i]["qtd"];
                    colunaLocal[tab][i].value = json_estoque[tab][i]["local"];
                    linha[tab][i].appendChild(td1[tab][i]);
                    linha[tab][i].appendChild(td2[tab][i]);
                    linha[tab][i].appendChild(td3[tab][i]);
                    tabela[tab].appendChild(linha[tab][i]);

                }

                btnDeletarTab[tab] = document.createElement("button");
                btnDeletarTab[tab].setAttribute("class", "btn-deletar-tab");
                btnDeletarTab[tab].innerHTML = "X";
                btnDeletarTab[tab].setAttribute("onclick", "deletarTab(" + tab + ")");
                document.getElementById("painel-de-tabelas").appendChild(tabela[tab]);
                rodapeValorTotal[tab].innerHTML = "" + totalQuantidadeEstoque[tab];
                rodapeTd3[tab].appendChild(btnDeletarTab[tab]);
                linhaRodape[tab].appendChild(rodapeTd1[tab]);
                linhaRodape[tab].appendChild(rodapeValorTotal[tab]);

                linhaRodape[tab].appendChild(rodapeTd3[tab]);
                tabela[tab].appendChild(linhaRodape[tab]);
                totalTabs += 1;
            }
            //////////////////
            const indexPdNotificacao = notificacoesAtivas.findIndex(object => {
                return object.id === idPdAberto;
            });
            if (notificacoesAtivas[indexPdNotificacao]["ativo"] == true) {
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell notificacao-ativa");
            } else {
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell");
            }
            document.getElementById("btn-abrir-pdt-" + id).scrollIntoView();
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