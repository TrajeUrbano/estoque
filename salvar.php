<?php session_start(); ?>
<?php include "config.php" ?>

<?php








$linkRedirect = $linkPai;
if (isset($_POST["salvamento"]) && !isset($_POST["valor-custo"])) {
    $dadosNotificacoes = json_decode($_POST["notificacoes"], true);
    
    $salvamento = $_POST["salvamento"];
    $estoqueSalvo = json_decode($_POST["salvamento"], true);
    for ($i = 0; $i < count($estoqueSalvo); $i++) {
        $key = array_search($i, array_column($dadosNotificacoes, 'id'));
        if (isset($estoqueSalvo[$i])) {
            $pdJson =  json_encode($estoqueSalvo[$i]);

            //echo "UPDATE produtos SET tabelas_json = '\'".$pdJson."\'' WHERE ID='".$i."'";
            mysqli_query($conexao, "UPDATE produtos SET tabelas_json = '\'" . $pdJson . "\'', notificacao = ".$dadosNotificacoes[$key]["ativo"]." WHERE ID='" . $i . "'") or die("Erro ao salvar Produto de id $i");
        }
    }
} else if (isset($_POST["salvamento"]) && isset($_POST["valor-custo"])) {
    $salvamento = $_POST["salvamento"];
    $estoqueSalvo = json_decode($_POST["salvamento"], true);
    $titulo = $_POST["titulo-pd"];
    $venda = $_POST["valor-venda"];
    $custo = $_POST["valor-custo"];
    for ($i = 0; $i < count($estoqueSalvo); $i++) {
        if (isset($estoqueSalvo[$i])) {
            $pdJson =  json_encode($estoqueSalvo[$i]);
            //echo "UPDATE produtos SET tabelas_json = '\'".$pdJson."\'', nome = '".$titulo."', preco_compra = '".$custo."', preco_revenda = '".$venda."' WHERE ID='".$i."'";
            mysqli_query($conexao, "UPDATE produtos SET tabelas_json = '\'" . $pdJson . "\'', nome = '" . $titulo . "', preco_compra = '" . $custo . "', preco_revenda = '" . $venda . "' WHERE ID='" . $i . "'") or die("Erro ao salvar Produto no modo Edição Livre de id $i");
        }
    }
}

//SALVANDO REFRESH NA TABELA dados_salvamento
$date = date("Y-m-d H:i:s");
$dateComparativa = date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));
date('Y-m-d H:i:s', strtotime('-2 minutes', strtotime($date)));
$id_session = $_SESSION['session_id'];
$qry_atualizar_session = mysqli_query($conexao, "UPDATE dados_sessao SET data_hora = '$date', ultimo_status_salvo = 1 WHERE id = $id_session") or die("erro ao atualizar sessão");
if (isset($_POST["pdAberto"])) {
    $_SESSION["pd_aberto"] = $_POST["pdAberto"];
}

if (isset($_POST["linkDirecionado"])) {
    $linkRedirect = $_POST["linkDirecionado"];
} else {
    if (isset($_SESSION["pagina_atual"])) {
        $linkRedirect = $_SESSION["pagina_atual"];
    }
}
if (isset($_POST["desfazerArray"])) {
    $_SESSION["desfazer"] = $_POST["desfazerArray"];
}
if (isset($_POST["refazerArray"])) {
    $_SESSION["refazer"] = $_POST["refazerArray"];
}
if (isset($_POST["dadosNavegacao"])) {
    $_SESSION["dadosNavegacao"] = $_POST["dadosNavegacao"];
}

?>

<script>
    window.location.href = <?php echo "'" . $linkPai . $linkRedirect . "'"; ?>;
</script>