


<link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200&display=swap" rel="stylesheet">

<style type="text/css">
	#busca_opcoes{
		display: none;
		max-height: 220px;
		overflow-y: scroll;
	}
	.div-sugestao-pesquisa{
		margin:  0 auto;
		padding: 0 auto;
		cursor: pointer;
		display: flex;
    	align-items: center; //centraliza horizontalmente
    	justify-content: center;
	}
	.div-sugestao-pesquisa:hover{
		background-color: rgba(50, 50, 50, 0.1);
	}
	.nome-pd-busca{
		font-weight: 100;
letter-spacing: 2px;
	}
</style>
<script>
	
	var pdtsSugeridos = [];
function buscarPd(id){
	abrirPd(id);
	campoBuscaAtiva = false;
    document.getElementById("div-procurar-pd").style.display = "none";
	}
function sugerirOpcoes(chave){

	pdtsSugeridos = [];
	for (var i = 0; i < dadosEstoque.length; i++) {
		if(typeof dadosEstoque[i] !== "undefined"){
			if(dadosEstoque[i]["nome"].toLowerCase().indexOf(chave.toLowerCase()) !== -1 || dadosEstoque[i]["sku"].toLowerCase().indexOf(chave.toLowerCase()) !== -1){
				pdtsSugeridos.push(i);
		}
		}
	}


  //
  //
  //CONSTRUÇÃO VISUAL
  //VV VV VV VV VV VV 
  //
  //

	var divBuscaOpcoes = document.getElementById("busca_opcoes");
	divBuscaOpcoes.innerHTML = "";
	divBuscaOpcoes.style.display = "block";
	var pdsku = [];
	var colTxts = []
	var divLine = [];
	var img = [];
	var nome = [];
for (var i = 0; i < pdtsSugeridos.length; i++) {
	divLine[i] = document.createElement("div");
	divLine[i].setAttribute("onclick", "buscarPd(pdtsSugeridos["+i+"])");
	divLine[i].setAttribute("class", "div-sugestao-pesquisa");
	img[i] = document.createElement("img");
	nome[i] = document.createElement("span");
	pdsku[i] = document.createElement("span");
	colTxts[i] = document.createElement("div");
	img[i].setAttribute("src", dadosEstoque[pdtsSugeridos[i]]["imagem"]);
	img[i].style.width = "46px";
	img[i].style.margin = "5px";
	nome[i].innerHTML = dadosEstoque[pdtsSugeridos[i]]["nome"].substring(0,24) + "...";
	nome[i].setAttribute("class", "nome-pd-busca");
	//nome[i].style.fontWeight = "100";
	divLine[i].style.display = "flex";
	colTxts[i].style.display = "grid";
	divLine[i].appendChild(img[i]);
	colTxts[i].appendChild(nome[i]);
	colTxts[i].appendChild(pdsku[i]);
	divLine[i].appendChild(colTxts[i]);
	pdsku[i].innerHTML = dadosEstoque[pdtsSugeridos[i]]["sku"];
	divBuscaOpcoes.appendChild(divLine[i]);
}
}
</script>