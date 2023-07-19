<?php include "config.php" ?>
<script>
    // variaveis finais visiveis --- resultados
    var quantidadeTotalUnidade = 0;
    var valorInvestido = 0;
    var valorInvestidoRevenda = 0;
    var lucroBrutoEstimado = 0;

    var imagens = [];
    var pdsPorId = [];
    var idProdutosSequencial = [];
    var estoque = [];
    var dadosEstoque = [];
</script>
<?php
$query_produtos = mysqli_query($conexao, "SELECT * FROM produtos order by sku asc");
$idSequencial = [];
$contadorSequencia = 0;
$produtosAlterados = array();
while ($results_pdt = mysqli_fetch_array($query_produtos)) {
    $nome = $results_pdt['nome'];
    $sku = $results_pdt['sku'];
    $id = $results_pdt['id'];
    $preco_custo = $results_pdt["preco_compra"];
    $preco_revenda = $results_pdt["preco_revenda"];
    $idSequencial[$contadorSequencia] = $id;
    $tabelas_json = $results_pdt['tabelas_json'];
    $imagem = $results_pdt["imagem"];
?>
    <script>
        imagens[<?php echo $id; ?>] = document.createElement("img");
        imagens[<?php echo $id; ?>].src = '<?php echo $imagem; ?>';
        pdsPorId["<?php echo $sku; ?>"] = <?php echo $id; ?>;
        idProdutosSequencial[<?php echo $contadorSequencia; ?>] = <?php echo $id; ?>;
        estoque[<?php echo $id; ?>] = JSON.parse(<?php echo $tabelas_json; ?>);

        dadosEstoque[<?php echo $results_pdt['id'] ?>] = JSON.parse('{"nome": "<?php echo $nome; ?>", "sku": "<?php echo $sku; ?>", "imagem": "<?php echo $imagem; ?>", "preco_custo": "<?php echo $preco_custo; ?>", "preco_revenda": "<?php echo $preco_revenda; ?>"}');
    </script>
<?php
    $contadorSequencia += 1;
}
?>
<html>

<head>
    <title>Relatório Estoque</title>
    <style>
        body{
            background-color: rgb(210, 210, 210);
            font-size: 16px;
        }
        #visualizador{
            margin-left: 10px;
            box-shadow: 0px 0px 20px 10px rgba(100,100,100, 0.2);
            background-color: rgb(218, 215, 215);
            padding: 5px;
        }
        
        .dados {
            margin: 1px;
            display: block;
            color: rgb(40, 50, 40);
            padding: 3px;
            font-family: 'Mukta', sans-serif;
            
        }

        .dadosPd {
            margin: 1px;
            color: rgb(40, 50, 40);
            padding: 4px;
            font-family: 'Mukta', sans-serif;
        }

        .dados1 {
            background-color: rgb(230, 230, 230);
        }

        .dados2 {
            background-color: rgb(245, 245, 245);
        }

        .dados-numeros {
            color: rgb(00, 150, 40);
        }

        #dados-valores-tabela-pds td {
            font-size: 16px;
            padding: 5px;
            padding-left: 15px;
            padding-right: 15px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include "partes/cabecalho_sem_navegacao.php" ?>
    <br><br>
    <div id="visualizador">
        <div id="dados-valores">
            
        </div>
    <div>
        <table id="dados-valores-tabela-pds">
        <tr class="dadosPd dados2">
            <td><b>SKU</b></td>
            <td><b>Nome</b></td>
            <td><b>Quantidade</b></td>
            <td><b>Investimento</b></td>
    </tr>
        </table>
    </div></div>
    
    <script>
    
    function salvar(linkDirecionado) {
            //NÃO SALVA NADA
            //FUNÇÃO REALIZADA APENAS PARA REDIRECIONAR PARA A PAGINA CORETA
            window.location.href = linkDirecionado;
        }
        function criarDado(classe, valor, nome, tag, pref) {
            var dado = document.createElement(tag);
            dado.setAttribute("class", classe);
            dado.innerHTML = nome;
            document.getElementById("dados-valores").appendChild(dado);
            var numero = document.createElement(tag);
            numero.setAttribute("class", "dados-numeros");
            numero.innerHTML = pref + valor;
            dado.appendChild(numero);

            if (valor < 0) {
                numero.style.color = "rgb(150,40,0)";
            }
            //document.getElementById("dados-valores").appendChild(document.createElement("br"));

        }

        function criarDadoPdIndividual(sku, nome, qtdTotal, investimentoTotal, RevendaTotal, LucroEstimadoTotal, indice) {
            var linha = document.createElement("tr");
            if (indice % 2 === 0) {
                linha.setAttribute("class", "dadosPd dados1");
            }else{
            linha.setAttribute("class", "dadosPd dados2");

            }
            var dadoSKU = document.createElement("td");
            dadoSKU.innerHTML = sku;
            linha.appendChild(dadoSKU);
            //
            var dadoNome = document.createElement("td");
            dadoNome.innerHTML = nome;
            linha.appendChild(dadoNome);
            //
            //
            //
            var dadoQtd = document.createElement("td");
            dadoQtd.innerHTML = "(Un) ";
            //
            var numeroQtd = document.createElement("b");
            numeroQtd.setAttribute("class", "dados-numeros");
            numeroQtd.innerHTML = qtdTotal;
            dadoQtd.appendChild(numeroQtd);
            linha.appendChild(dadoQtd);
            //
            //
            //
            var dadoInv = document.createElement("td");
            dadoInv.innerHTML = "";
            //
            var numeroInv = document.createElement("b");
            numeroInv.setAttribute("class", "dados-numeros");
            numeroInv.innerHTML = "R$" + investimentoTotal;
            dadoInv.appendChild(numeroInv);
            linha.appendChild(dadoInv);
            //



            //document.getElementById("dados-valores").appendChild(document.createElement("br"));

            document.getElementById("dados-valores-tabela-pds").appendChild(linha);
        }
        for (let i = 0; i < idProdutosSequencial.length; i++) {

            for (let tabela = 0; tabela < estoque[idProdutosSequencial[i]].length; tabela++) {
                for (let tam = 0; tam < estoque[idProdutosSequencial[i]][tabela].length; tam++) {
                    quantidadeTotalUnidade += estoque[idProdutosSequencial[i]][tabela][tam]["qtd"];
                    valorInvestido = valorInvestido + (estoque[idProdutosSequencial[i]][tabela][tam]["qtd"] * dadosEstoque[idProdutosSequencial[i]]["preco_custo"]);
                    valorInvestidoRevenda = valorInvestidoRevenda + (estoque[idProdutosSequencial[i]][tabela][tam]["qtd"] * dadosEstoque[idProdutosSequencial[i]]["preco_revenda"]);
                }
            }
        }

        //
        //formatações
        valorInvestido = valorInvestido.toLocaleString('pt-br', {
            minimumFractionDigits: 2
        });
        valorInvestidoRevenda = valorInvestidoRevenda.toLocaleString('pt-br', {
            minimumFractionDigits: 2
        });
        lucroBrutoEstimado = lucroBrutoEstimado.toLocaleString('pt-br', {
            minimumFractionDigits: 2
        });
        criarDado("dados dados1", quantidadeTotalUnidade, "Quantidade Total de Produtos: ", "span", "(Un) ");
        criarDado("dados dados2", valorInvestido, "Valor Investido: ", "span", "R$");
        criarDado("dados dados1", valorInvestidoRevenda, "Valor de Venda Total Estimado: ", "span", "R$");
        criarDado("dados dados2", lucroBrutoEstimado, "Lucro Bruto Estimado: ", "span", "R$");
        document.getElementById("dados-valores").appendChild(document.createElement("br"));
        for (let i = 0; i < idProdutosSequencial.length; i++) {
            var QtdTotalDestePd = 0;
            var valorTotalDestePd = 0;
            var valorTotalRevendaPd = 0;
            var lucroTotalPd = 0;
            var nomePd;
            var skuPd;
            for (let tabela = 0; tabela < estoque[idProdutosSequencial[i]].length; tabela++) {
                for (let tam = 0; tam < estoque[idProdutosSequencial[i]][tabela].length; tam++) {
                    nomePd = dadosEstoque[idProdutosSequencial[i]]["nome"];
                    skuPd = dadosEstoque[idProdutosSequencial[i]]["sku"];
                    QtdTotalDestePd += estoque[idProdutosSequencial[i]][tabela][tam]["qtd"];
                    valorTotalDestePd = valorTotalDestePd + (estoque[idProdutosSequencial[i]][tabela][tam]["qtd"] * dadosEstoque[idProdutosSequencial[i]]["preco_custo"]);
                    valorTotalRevendaPd = valorTotalRevendaPd + (estoque[idProdutosSequencial[i]][tabela][tam]["qtd"] * dadosEstoque[idProdutosSequencial[i]]["preco_revenda"]);
                }
            }
            lucroTotalPd = valorTotalRevendaPd - valorTotalDestePd;
            //formatacoes
            valorTotalDestePd = valorTotalDestePd.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            });
            valorTotalRevendaPd = valorTotalRevendaPd.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            });
            lucroTotalPd = lucroTotalPd.toLocaleString('pt-br', {
                minimumFractionDigits: 2
            });
            criarDadoPdIndividual(skuPd, nomePd, QtdTotalDestePd, valorTotalDestePd, valorTotalRevendaPd, lucroTotalPd, i);
        }
    </script>
</body>

</html>