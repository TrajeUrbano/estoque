<?php session_start();
$_SESSION["pagina_atual"] = "index.php";

?>


<?php include "config.php" ?>

<?php
//SALVANDO REFRESH NA TABELA dados_salvamento
$date = date("Y-m-d H:i:s");
$dateComparativa = date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));
date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));
$dateUltimoSave = "uol";
if (!isset($_SESSION["session_id"])) {
    $query_verificar_session_pre = mysqli_query($conexao, "SELECT * FROM dados_sessao ORDER BY id DESC LIMIT 1") or die("Erro ao verificar reload");
    while ($save_pre = mysqli_fetch_array($query_verificar_session_pre)) {

        if ($dateComparativa < $save_pre["data_hora"]) {
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
$query_verificar_last_save = mysqli_query($conexao, "SELECT * FROM dados_sessao ORDER BY data_hora DESC LIMIT 1") or die("Erro ao verificar salvamento");
while ($last_save = mysqli_fetch_array($query_verificar_last_save)) {
    $dateUltimoSave = $last_save["data_hora"];
    $dataUltimoSalvamento = date_parse($dateUltimoSave);
    echo '<div id="ultimo_save" align="center">
        ' . $dataUltimoSalvamento["day"] . '/' . $dataUltimoSalvamento["month"] . '/' . $dataUltimoSalvamento["year"] . ' - ' . $dataUltimoSalvamento["hour"] . ':' . $dataUltimoSalvamento["minute"] . ':' . $dataUltimoSalvamento["second"] . '
        </div>';
}
if (isset($_SESSION["desfazer"]) && !empty($_SESSION["desfazer"])) {
?>
    <script>
        var acoesDesfazer = JSON.parse('<?php echo $_SESSION["desfazer"]; ?>');
    </script>
<?php
} else {
?>
    <script>
        var acoesDesfazer = {};
    </script>
<?php
}
if (isset($_SESSION["refazer"]) && !empty($_SESSION["refazer"])) {
?>
    <script>
        var acoesRefazer = JSON.parse('<?php echo $_SESSION["refazer"]; ?>');
    </script>
<?php
} else {
?>
    <script>
        var acoesRefazer = {};
    </script>
<?php
}
if (isset($_SESSION["dadosNavegacao"]) && !empty($_SESSION["dadosNavegacao"])) {
?>
    <script>
        var dadosNavegacao = JSON.parse('<?php echo $_SESSION["dadosNavegacao"]; ?>');
    </script>
<?php
} else {
?>
    <script>
        var dadosNavegacao = {
            "indiceDesfazer": 0,
            "indiceRefazer": 0
        };
    </script>
<?php
}


?>
<html lang="pt-br">

<head>
    <title>Modo Vendas</title>
    <script>
        function loopSalvarAutomaticamente() {
            if (podeSalvar == true) {
                if (necessarioSalvar == true) {
                    salvar("modo-devolucao.php");
                } else {
                    window.location.reload();
                }
            } else {
                podeSalvar = true;
            }

            setTimeout(loopSalvarAutomaticamente, 10000);
        }
        setTimeout(loopSalvarAutomaticamente, 10000);
    </script>
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
    <style>
        #indicativo-modo {
            color: #fff;
            background-color: rgb(230, 70, 50);
            padding: 5px;
            margin: 38px;
            position: relative;
            top: 5px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div id="div-procurar-pd">
        <input type="text" id="procurar-pd">
        <button id="procurar-pesquisa-btn"><i class="fa fa-search"></i></button>
        <h3>Aperte "Shift" para fechar ou "Enter" Para pesquisar</h3>
        <div id="busca_opcoes">
        </div>
    </div>


    <br>
    <?php include "partes/cabecalho.php" ?>
    <?php include "partes/busca_opcoes.php" ?>
    <span id="indicativo-modo">Modo Devolução</span>

    <div id="painel-edicao-pdt">
        <div style="display:flex;">
            <h3 id="titulo-pdt"></h3>
            <h3 id="indicador-pdt"></h3>
        </div>
        <br>
        <div id="linha-master">
            <div id="form-imagem">
                <div id="imagem-pd"></div>
            </div>
            <div id="painel-de-tabelas"></div>
            <form method="POST" id="formulario-salvamento" action="salvar.php">
                <input type="hidden" value="" name="notificacoes" id="dados-notificacoes">
                <input type="hidden" value="" name="dadosNavegacao" id="dados-navegacao">
                <input type="hidden" value="" name="desfazerArray" id="desfazer-array">
                <input type="hidden" value="" name="refazerArray" id="refazer-array">
                <input type="hidden" value="" name="salvamento" id="valores-salvamento">
                <input type="hidden" value="" name="pdAberto" id="session_input_pdAberto">
                <input type="hidden" value="" name="linkDirecionado" id="link_direcionado_input">
                <button onclick="salvar('modo-devolucao.php')" id="btn-salvar"><i class="fa fa-save" style="font-size:22px;"></i></button>
            </form>
        </div>
        <br><br>
        <script>
            var valorescomercioVisivel = false;

            function verValores() {
                    necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                    podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                if (valorescomercioVisivel == false) {
                    valorescomercioVisivel = true;
                    document.getElementById("valores-comercio").style.color = "rgb(50,50,50)";
                } else {
                    valorescomercioVisivel = false;
                    document.getElementById("valores-comercio").style.color = "transparent";
                }
            }
        </script>
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
        var indiceDesfazer = dadosNavegacao["indiceDesfazer"];
        var indiceRefazer = dadosNavegacao["indiceRefazer"];;


        function addDesfazer(pdId, tab, indice) {
            acoesDesfazer[indiceDesfazer] = {};
            acoesDesfazer[indiceDesfazer]["pdId"] = pdId;
            acoesDesfazer[indiceDesfazer]["tab"] = tab;
            acoesDesfazer[indiceDesfazer]["indice"] = indice;
            indiceDesfazer += 1;
            dadosNavegacao["indiceRefazer"] = indiceRefazer;
            dadosNavegacao["indiceDesfazer"] = indiceDesfazer;
            document.getElementById("dados-navegacao").value = JSON.stringify(dadosNavegacao);
            document.getElementById("desfazer-array").value = JSON.stringify(acoesDesfazer);
            document.getElementById("refazer-array").value = JSON.stringify(acoesRefazer);
        }

        function addRefazer(pdId, tab, indice) {
            acoesRefazer[indiceRefazer] = {};
            acoesRefazer[indiceRefazer]["pdId"] = pdId;
            acoesRefazer[indiceRefazer]["tab"] = tab;
            acoesRefazer[indiceRefazer]["indice"] = indice;
            indiceRefazer += 1;
            dadosNavegacao["indiceRefazer"] = indiceRefazer;
            dadosNavegacao["indiceDesfazer"] = indiceDesfazer;
            document.getElementById("refazer-array").value = JSON.stringify(acoesRefazer);
            document.getElementById("desfazer-array").value = JSON.stringify(acoesDesfazer);
        }

        function refazer() {
            if (indiceRefazer > 0) {

                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                indiceRefazer -= 1;
                abrirPd(acoesRefazer[indiceRefazer]["pdId"]);
                crosstabSelecionado = acoesRefazer[indiceRefazer]["tab"];
                indiceVerticalTamanhosSelecionados = acoesRefazer[indiceRefazer]["indice"];
                json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"] += 1;
                colunaQtd[crosstabSelecionado][indiceVerticalTamanhosSelecionados].innerHTML = json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"];
                totalQuantidadeEstoque[crosstabSelecionado] += 1;
                rodapeValorTotal[crosstabSelecionado].innerHTML = (totalQuantidadeEstoque[crosstabSelecionado]);
                animarTamanhoAlterado(indiceVerticalTamanhosSelecionados);
                indiceVerticalTamanhosSelecionados -= 1;
                addDesfazer(acoesRefazer[indiceRefazer]["pdId"], acoesRefazer[indiceRefazer]["tab"], acoesRefazer[indiceRefazer]["indice"]);
            }
        }

        function desfazer() {
            if (indiceDesfazer > 0) {
                necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
                indiceDesfazer -= 1;
                abrirPd(acoesDesfazer[indiceDesfazer]["pdId"]);
                crosstabSelecionado = acoesDesfazer[indiceDesfazer]["tab"];
                indiceVerticalTamanhosSelecionados = acoesDesfazer[indiceDesfazer]["indice"];
                json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"] -= 1;
                colunaQtd[crosstabSelecionado][indiceVerticalTamanhosSelecionados].innerHTML = json_estoque[crosstabSelecionado][indiceVerticalTamanhosSelecionados]["qtd"];
                totalQuantidadeEstoque[crosstabSelecionado] -= 1;
                rodapeValorTotal[crosstabSelecionado].innerHTML = (totalQuantidadeEstoque[crosstabSelecionado]);
                animarTamanhoAlterado(indiceVerticalTamanhosSelecionados);
                indiceVerticalTamanhosSelecionados -= 1;
                addRefazer(acoesDesfazer[indiceDesfazer]["pdId"], acoesDesfazer[indiceDesfazer]["tab"], acoesDesfazer[indiceDesfazer]["indice"]);
            }
        }
        //
        //
        //
        function ativarNotificacaoPd() {
            necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
            podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            const indexPd = notificacoesAtivas.findIndex(object => {
                return object.id === pdAberto;
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
                //Seta Direita
                if (crosstabSelecionado < (totalTabs - 1)) {
                    necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
                    podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
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
            if (indiceVerticalTamanhosSelecionados < botaoRemover[crosstabSelecionado].length - 1) {
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
        var totalValorCusto = 0;

        var notificacao = [];
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
        var linhaRodape = [];
        var linhaRodape = [];
        var rodapeTd1 = [];
        var rodapeValorTotal = [];
        var pdSelecionado = document.getElementById("btn-abrir-pdt-" + idProdutosSequencial[0]);

        //CRIACAO LINHA CABCALHO

        function removerTamanho(indice, pdId, tab) {
            necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
            podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            necessarioSalvar = true;
            crosstabSelecionado = tab;
            indiceVerticalTamanhosSelecionados = indice;
            json_estoque[crosstabSelecionado][indice]["qtd"] += 1;
            colunaQtd[crosstabSelecionado][indice].innerHTML = json_estoque[crosstabSelecionado][indice]["qtd"];
            totalQuantidadeEstoque[crosstabSelecionado] += 1;
            rodapeValorTotal[crosstabSelecionado].innerHTML = (totalQuantidadeEstoque[crosstabSelecionado]);
            animarTamanhoAlterado(indice);
            addDesfazer(pdId, tab, indice);
            acoesRefazer = [];
        }

        function animarTamanhoAlterado(indice) {
            var crossTemporario = crosstabSelecionado;
            colunaQtd[crossTemporario][indice].style.color = "red";
            setTimeout(() => {
                colunaQtd[crossTemporario][indice].style.color = "rgb(0,0,0)";
            }, 1000);
        }

        function abrirPd(id) {
            crosstabSelecionado = 0;
            necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
            podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
            pdAberto = id;
            totalValorCusto = 0;
            document.getElementById("session_input_pdAberto").value = id;
            totalTabs = 0;
            indiceVerticalTamanhosSelecionados = 0;
            pdSelecionado.style.background = "rgb(240,240,240)";
            pdSelecionado.style.color = "#000";
            pdSelecionado = document.getElementById("btn-abrir-pdt-" + id);
            pdSelecionado.style.background = "#000";
            pdSelecionado.style.color = "#fff";
            idSequencialPdAberto = idProdutosSequencial.indexOf(id);

            linkLi = "https://app.lojaintegrada.com.br/painel/catalogo/produto/buscar?q=" + dadosEstoque['sku'] + "&listagem=alfabetica&filtro=";


            json_estoque = estoque[id];
            totalDeTamanhos = Object.size(json_estoque[0]);
            document.getElementById("painel-de-tabelas").innerHTML = "";
            json_dados_extras = dadosEstoque[id];
            document.getElementById("imagem-pd").innerHTML = "";
            document.getElementById("imagem-pd").appendChild(imagens[id]);
            document.getElementById("titulo-pdt").innerHTML = json_dados_extras["nome"];
            document.getElementById("indicador-pdt").innerHTML = json_dados_extras["sku"];
            document.getElementById("valor-venda").innerHTML = json_dados_extras["venda"].toLocaleString('pt-br', {
                style: 'currency',
                currency: 'BRL'
            });
            document.getElementById("valor-custo").innerHTML = json_dados_extras["custo"].toLocaleString('pt-br', {
                style: 'currency',
                currency: 'BRL'
            });


            linha = [];

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


                    linha[tab][i] = document.createElement("tr");
                    colunaTam[tab][i] = document.createElement("td");
                    colunaQtd[tab][i] = document.createElement("td");
                    colunaLocal[tab][i] = document.createElement("td");
                    botaoRemover[tab][i] = document.createElement("button");
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

                    linha[tab][i].appendChild(botaoRemover[tab][i]);

                    botaoRemover[tab][i].innerHTML = colunaTam[tab][i].innerHTML = json_estoque[tab][i]["tam"];
                    botaoRemover[tab][i].setAttribute("class", "botao-remover1");
                    botaoRemover[tab][i].setAttribute("onclick", "removerTamanho(" + i + ", " + id + "," + tab + ",)");
                    tabela[tab].appendChild(linha[tab][i]);

                }

                document.getElementById("painel-de-tabelas").appendChild(tabela[tab]);
                totalValorCusto = totalValorCusto + (totalQuantidadeEstoque[tab] * json_dados_extras["custo"]);

                rodapeValorTotal[tab].innerHTML = "" + totalQuantidadeEstoque[tab];
                linhaRodape[tab].appendChild(rodapeTd1[tab]);
                linhaRodape[tab].appendChild(rodapeValorTotal[tab]);
                tabela[tab].appendChild(linhaRodape[tab]);
                totalTabs += 1;
            }
            const indexPdNotificacao = notificacoesAtivas.findIndex(object => {
                return object.id === pdAberto;
            });
            if (notificacoesAtivas[indexPdNotificacao]["ativo"] == true) {
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell notificacao-ativa");
            } else {
                document.getElementById("icon-notificacao").setAttribute("class", "fa fa-bell");
            }
            document.getElementById("total-custo").innerHTML = totalValorCusto.toLocaleString('pt-br', {
                style: 'currency',
                currency: 'BRL'
            });
            document.getElementById("btn-abrir-pdt-" + id).scrollIntoView();
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