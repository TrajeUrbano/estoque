<?php
/*if(!isset($_SESSION["link"])){
    session_start();
    $_SESSION["link"];

}*/
include "scripts-paginas-padrao.php";
?>

<style>
    .btns-painel-ferramentas {
        border: 2px solid #000;
        padding: 10px;
        border: 0px;
        background-color: transparent;
        color: rgb(220, 220, 220);
        cursor: pointer;
        font-family: 'Mukta', sans-serif;
        letter-spacing: 1px;
        font-weight: bolder;
    }

    #btn-salvar-menu {
        border-radius: 4px;
        color: rgb(220, 220, 220);
        font-size: 17px;
    }

    #btn-salvar-menu:hover {
        color: rgb(80, 230, 250);
    }

    .btns-painel-ferramentas:hover {
        color: rgb(80, 230, 250);
    }

    #painel-de-ferramentas {
        /*background: #e8e9eb;*/
        background: rgb(30, 30, 30);
        padding-top: 0px;
        padding-bottom: 7px;

    }

    #painel-de-ferramentas img {
        position: relative;
        top: 15px;
    }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet">
<script type="text/javascript" src="variaveis_globais.js"></script>
<div id="painel-de-ferramentas">
    <li>
        <img src="imagens/LOGO.png" height="50px">
        &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
        <button class="btns-painel-ferramentas" onclick="salvar('#')" id="btn-salvar-menu" title="Modo onde você pode Editar cada Quantidade livremente Digitando os valores"><i class="fa fa-save"></i></button>
        &nbsp;&nbsp; &nbsp;&nbsp;
        <button class="btns-painel-ferramentas" title="Adicionar Produto (SKU)" onclick="salvar('criar-pd.php')"><i class="fa fa-plus" aria-hidden="true"></i> Add Produto</button>
        <button class="btns-painel-ferramentas" title="Modo Principal onde o sistema vai retirando unidades do produto" onclick="salvar('index.php')"><i class="fa fa-flag" aria-hidden="true"></i> Modo Vendas</button>
        <button class="btns-painel-ferramentas" title="Ver Relatórios" onclick="salvar('relatorios.php')"><i class="fa fa-bar-chart" aria-hidden="true"></i> Relatório</button>
        <button class="btns-painel-ferramentas" title="Modo onde o sistema adiciona unidades de cada produto ao invéz de retirar" onclick="salvar('modo-devolucao.php')"><i class="fa fa-dropbox" aria-hidden="true"></i> Modo Devolução</button>
        <button class="btns-painel-ferramentas" title="Modo onde você pode Editar cada Quantidade livremente Digitando os valores" onclick="salvar('edicao-avancada.php')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edição Avançada</button>
        <button class="btns-painel-ferramentas" title="Modo onde você pode Editar cada Quantidade livremente Digitando os valores" onclick="salvar('conferencia.php')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Editar Estoques</button>
        <button class="btns-painel-ferramentas" title="Modo onde você pode Editar cada Quantidade livremente Digitando os valores" onclick="salvar('leitura.php')">Modo Leitura</button>
        <button id="icon-notificacao-cab" onclick="exibirMenuNotificacoes()"><i class="fa fa-bell" aria-hidden="true" style="font-size: 15px; color: rgb(180,110,20);"></i></button>

    </li>
</div>
<div id="menu-notificacoes" class="menu-notificacoes">
    <button id="btn_fechar_menu_notificacao" onclick="fecharMenuNotificacoes()">X</button>
    <ul id="col-notificacoes">

    </ul>
</div>
<script>
    fecharMenuNotificacoes

    function fecharMenuNotificacoes() {
        document.getElementById("menu-notificacoes").setAttribute("class", "menu-notificacoes-escondido");
    }

    function exibirMenuNotificacoes() {

        necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
        podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
        document.getElementById("menu-notificacoes").setAttribute("class", "menu-notificacoes-visivel");
    }

    function removerNotificacao(index) {

        necessarioSalvar = true; //MARCA QUE É NECESSÁRIO SALVAR O SISTEMA
        podeSalvar = false; //MARCA PARA NÃO SALVAR NESTE MOMENTO, SÓ NO PROXIMO LOOP
        abrirPd(notificacoesAtivas[index]['id']);
        ativarNotificacaoPd();
    }

    function menuNotificacoes() {
        var colNotificacoes = document.getElementById("col-notificacoes");
        colNotificacoes.innerHTML = "";
        var listaNotificacoes = [];
        for (let index = 0; index < notificacoesAtivas.length; index++) {
            if (notificacoesAtivas[index]["ativo"] == true) {
                var ntfSku = document.createElement("span");
                var ntfFechar = document.createElement("button");
                var ntfAbrir = document.createElement("button");
                var bRow = document.createElement("br");

                ntfSku.innerHTML = dadosEstoque[notificacoesAtivas[index]["id"]]["sku"];
                ntfFechar.innerHTML = "Remover";
                ntfFechar.setAttribute("class", "btnFecharNotificacaoMenu");
                ntfAbrir.innerHTML = "ver";
                ntfAbrir.setAttribute("class", "btnVerPdNotificacaoMenu");
                ntfAbrir.setAttribute("onclick", "abrirPd(" + notificacoesAtivas[index]['id'] + ")");
                ntfFechar.setAttribute("onclick", "removerNotificacao(" + index + ")");
                listaNotificacoes[index] = document.createElement("li");
                listaNotificacoes[index].appendChild(ntfSku);
                listaNotificacoes[index].appendChild(bRow);
                listaNotificacoes[index].appendChild(ntfAbrir);
                listaNotificacoes[index].appendChild(ntfFechar);
                colNotificacoes.appendChild(listaNotificacoes[index]);
                //listaNotificacoes[index].setAttribute()
            }

        }
    }
    menuNotificacoes();
</script>