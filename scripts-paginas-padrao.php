<div id="menu-navegacao-pdts">
    <script>
        var notificacoesAtivas = [];
    </script>
<?php
        $query_produtos = mysqli_query($conexao, "SELECT * FROM produtos order by sku asc");
        $idSequencial = [];
        $contadorSequencia = 0;
        $listagem = [];
        $xList = 0;
        $produtosAlterados = array();
        while ($results_pdt = mysqli_fetch_array($query_produtos)) {
            $nome = $results_pdt['nome'];
            $sku = str_pad($results_pdt['sku'] , 3 , '0' , STR_PAD_LEFT);
            $id = $results_pdt['id'];
            $idSequencial[$contadorSequencia] = $id;
            $tabelas_json = $results_pdt['tabelas_json'];
            $imagem = $results_pdt["imagem"];
            $custo = $results_pdt["preco_compra"];
            $venda = $results_pdt["preco_revenda"];
            $notificacao = $results_pdt["notificacao"];
            if(!empty($notificacao)){
               ?><script>notificacoesAtivas[<?php echo $contadorSequencia; ?>] = {"id":<?php echo $id; ?>,"ativo":<?php echo $notificacao; ?>};</script>
               <?php
            }else{
                ?><script>notificacoesAtivas[<?php echo $contadorSequencia; ?>] = {"id":<?php echo $id; ?>,"ativo":0};</script><?php
            }
        ?>
            <script>
                imagens[<?php echo $id; ?>] = document.createElement("img");
                imagens[<?php echo $id; ?>].src = '<?php echo $imagem; ?>';
                pdsPorId["<?php echo $sku; ?>"] = <?php echo $id; ?>;
                idProdutosSequencial[<?php echo $contadorSequencia; ?>] = <?php echo $id; ?>;
                estoque[<?php echo $id; ?>] = JSON.parse(<?php echo $tabelas_json; ?>);
                dadosEstoque[<?php echo $results_pdt['id'] ?>] = {"nome": "<?php echo $nome; ?>", "sku": "<?php echo $sku; ?>", "imagem": "<?php echo $imagem; ?>", "custo": "<?php echo $custo; ?>", "venda": "<?php echo $venda; ?>"};
                
            </script>
            <button onclick="abrirPd(<?php echo $results_pdt['id'] ?>)" class="btns-localizar-pds" id="btn-abrir-pdt-<?php echo $results_pdt['id'] ?>"><?php echo  $sku; ?></button>
        <?php
            
            $contadorSequencia += 1;
        }
        ?>
        
</div>
