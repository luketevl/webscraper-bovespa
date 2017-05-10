<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	<title>Example scraper Bovespa</title>
</head>
<body>



	<?php
include_once('./simple_html_dom.php');
$codCvm = (isset($_GET['codCvm']) && !empty($_GET['codCvm'])) ? $_GET['codCvm'] : 0;

$infoEmpresa = 'http://bvmf.bmfbovespa.com.br/pt-br/mercados/acoes/empresas/ExecutaAcaoConsultaInfoEmp.asp?CodCVM='. $codCvm .'&ViewDoc=0';

$infoEventosCorporativos = 'http://bvmf.bmfbovespa.com.br/cias-listadas/empresas-listadas/ResumoEventosCorporativos.aspx?codigoCvm='. $codCvm .'&idioma=pt-br';

$infoEmpresaData = file_get_html($infoEmpresa);

$infoEventosCorporativosData = file_get_html($infoEventosCorporativos);

 // echo $infoEventosCorporativosData->plaintext;

// GET data (Dados da Companhia)
$bovespaInfoDados = $infoEmpresaData->find('#accordionDados .ficha tbody tr');

$bovespaSegmentoLabel = trim($infoEventosCorporativosData->find('[id*=onteudo_lblSegmento]', 0)->plaintext);
$bovespaSegmentoValor = trim($infoEventosCorporativosData->find('[id*=onteudo_lblSegmentoValor]', 0)->plaintext);


// ############ TRADUCAO SEGMENTO###########

if(strtoupper($bovespaSegmentoValor) == 'NM'){
	$bovespaSegmentoValor = "Novo Mercado";
}
else if(strtoupper($bovespaSegmentoValor) == 'N1'){
	$bovespaSegmentoValor = "Nível 1 de Governança Corporativa ";
}
else if(strtoupper($bovespaSegmentoValor) == 'N2'){
	$bovespaSegmentoValor = "Nível 2 de Governança Corporativa";
}
else if(strtoupper($bovespaSegmentoValor) == 'MA'){
	$bovespaSegmentoValor = "Bovespa Mais";
}
else if(strtoupper($bovespaSegmentoValor) == 'M2'){
	$bovespaSegmentoValor = "Bovespa Mais Nível 2";
}
else if(strtoupper($bovespaSegmentoValor) == 'MB'){
	$bovespaSegmentoValor = "Traditional Org. OTC";
}
else if(strtoupper($bovespaSegmentoValor) == 'BOLSA'){
	$bovespaSegmentoValor = "Traditional";
}
// ##################


$bovespaWidgetEmpresas = $infoEmpresaData->find('#widget-empresas', 0);

echo ($bovespaWidgetEmpresas->plaintext);
foreach($bovespaWidgetEmpresas as $elements){?>
       <iframe id="tradingview_3096e" name="tradingview_3096e" src="<?= $elements->src;?>" width="100%" height="900" frameborder="0" allowtransparency="true" scrolling="no" allowfullscreen="" style="display:block;"></iframe>

<?php }


// GET DATA (POSICAO ACIONARIA) 

$bovespaPosicaoAcionaria 			= $infoEmpresaData->find('#divPosicaoAcionaria', 0);
$bovespaPosicaoAcionariaTitle 		= $bovespaPosicaoAcionaria->find('h3', 0)->plaintext;

$bovespaPosicaoAcionariaTable 		= $bovespaPosicaoAcionaria->find('table', 0);
$bovespaPosicaoAcionariaThead 		= $bovespaPosicaoAcionariaTable->find('thead th');

$bovespaPosicaoAcionariaTbody 		= $bovespaPosicaoAcionariaTable->find('tbody tr');

$bovespaPosicaoAcionariaLegenda 	= $bovespaPosicaoAcionaria->find('p.legenda', 0)->plaintext;


if(!empty($bovespaInfoDados)){ ?>
	<section>
			
		<h3>Dados da Companhia</h3>
		<table class="table table-responsive table-condensed infoDados">
			<tbody>
			<tr>
				<td><?=trim($bovespaSegmentoLabel); ?></td>
				<td><?=$bovespaSegmentoValor ?></td>
			</tr>
				<?php 	foreach($bovespaInfoDados as $elements){?>
							<tr>
								<?php 	foreach($elements->find('td') as $element){ ?>
											<td><?=$element->plaintext; ?></td>

								<?php } ?>	
							</tr>

				<?php } ?>
				<tr>
				</tr>
			</tbody>
		</table>
	</section>
<?php }



if(!empty($bovespaPosicaoAcionariaTbody)){ ?>
	<section>
			
		<h3><?= $bovespaPosicaoAcionariaTitle ?></h3>
		<table class="table table-responsive table-striped table-condensed">
			<thead>
				<tr> 
				<?php 	foreach($bovespaPosicaoAcionariaThead as $element){ ?>
							<th><?= $element->plaintext; ?></th>

				<?php } ?>
				</tr>
			</thead>
			<tbody>
			
				<?php 	foreach($bovespaPosicaoAcionariaTbody as $elements){?>
							<tr>
								<?php 	foreach($elements->find('td') as $element){ ?>
											<td><?=$element->plaintext; ?></td>

								<?php } ?>	
							</tr>

				<?php } ?>
				<tr>
				</tr>
			</tbody>
		</table>
		<span><?= $bovespaPosicaoAcionariaLegenda ?></span>
	</section>
<?php } ?>


  <iframe id="tradingview_3096e" name="tradingview_3096e" src="http://br.advfn.com/bolsa-de-valores/bovespa/ambev-ABEV3/grafico/tempo-real?mode=html5" width="100%" height="900" frameborder="0" allowtransparency="true" scrolling="no" allowfullscreen="" style="display:block;"></iframe>


</body>
</html>
