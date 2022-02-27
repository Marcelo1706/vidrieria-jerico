<section class="content">
<div class="btn-group pull-right">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-download"></i> Descargar <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
  <li><a onclick="create_pdf()" id="makepdf2" class=""><i class="fa fa-download"></i>Cotización</a>
  </ul>
</div>
<h1>Cotizacion</h1>
<?php if(isset($_GET["id"]) && $_GET["id"]!=""):?>
<?php
$sell = SellData::getById($_GET["id"]);
$operations = OperationData::getAllProductsBySellId($_GET["id"]);
$total = 0;
?>
<?php
/*
if(isset($_COOKIE["selled"])){
	foreach ($operations as $operation) {
//		print_r($operation);
		$qx = OperationData::getQYesF($operation->product_id);
		// print "qx=$qx";
			$p = $operation->getProduct();
		if($qx==0){
			echo "<p class='alert alert-danger'>El producto <b style='text-transform:uppercase;'> $p->name</b> no tiene existencias en inventario.</p>";			
		}else if($qx<=$p->inventary_min/2){
			echo "<p class='alert alert-danger'>El producto <b style='text-transform:uppercase;'> $p->name</b> tiene muy pocas existencias en inventario.</p>";
		}else if($qx<=$p->inventary_min){
			echo "<p class='alert alert-warning'>El producto <b style='text-transform:uppercase;'> $p->name</b> tiene pocas existencias en inventario.</p>";
		}
	}
	setcookie("selled","",time()-18600);
}
*/
?>
<div class="box box-primary">
<table class="table table-bordered">
<?php if($sell->person_id!=""):
$client = $sell->getPerson();
?>
<tr>
	<td style="width:150px;">Proveedor</td>
	<td><?php echo $client->name." ".$client->lastname;?></td>
</tr>

<?php endif; ?>


<?php if($sell->user_id!=""):
$user = $sell->getUser();
?>
<tr>
	<td>Atendido por</td>
	<td><?php echo $user->name." ".$user->lastname;?></td>
</tr>
<?php endif; ?>
</table>
</div>
<div class="box box-primary">
<br><table class="table table-bordered table-hover">
	<thead>
		<th>Codigo</th>
		<th>Cantidad</th>
		<th>Nombre del Producto</th>
		<th>Precio Unitario</th>
		<th>Total</th>

	</thead>
<?php
	foreach($operations as $operation){
		$product  = $operation->getProduct();
?>
<tr>
	<td><?php echo $product->id ;?></td>
	<td><?php echo $operation->q ;?></td>
	<td><?php echo $product->name ;?></td>
	<td>$ <?php echo number_format($product->price_out,2,".",",") ;?></td>
	<td><b>$ <?php echo number_format($operation->q*$product->price_out,2,".",",");$total+=$operation->q*$product->price_out;?></b></td>
</tr>
<?php
	}
	?>
</table>
</div>
<br><br><h1>Total: $ <?php echo number_format($total,2,'.',','); ?></h1>
	<?php

?>	
<?php else:?>
	501 Internal Error
<?php endif; ?>
</section>
<!-- Nueva función porque no pude eliminar la otra xD -->
<script>
function create_pdf(){ 
  var doc = new jsPDF('p', 'mm', [279, 216]);
  doc.setFontSize(12);
  <?php
  $header = "
    doc.text('".$sell->created_at."', 150, 263);
  ";
  if($sell->person_id!=""){
    $person = $sell->getPerson();
    $header .= "
    doc.text('".$person->name." ".$person->lastname."', 40, 73);
    ";
  }
  $page_break = "doc.addPage();";

  # Crear un bucle que cada 13 productos me imprima $header
  $i = 0;
  $total = 0;
  $page_total = 0;
  $y_axis = 94;
  $j = 0;
  foreach($operations as $operation){
    $product = $operation->getProduct();
    
    if($i == 0){
      echo $header;
    }
    if($i % 22 == 0 && $i != 0 || $i == count($operations)-1){
      if($i == count($operations)-1){
        $page_total += $operation->q*$operation->price_out;
      }
      $iva = $page_total * 0.13;
      echo "doc.text('Total:', 140, 253);";
      echo "doc.text('$".number_format($page_total,2,".",",")."', 175, 253);";
      if($i % 22 == 0 && $i != count($operations)-1){
        echo $page_break;
        echo $header;
        $page_total = 0;
        $y_axis = 91;
        $j = 0;
      }
    }

    $page_total += $operation->q*$operation->price_out;
    $row_pos = $y_axis+($j*7.2);
    echo "doc.text('".$operation->q."', 20, $row_pos);";
    echo "doc.text('".$product->name."', 50, $row_pos);";
    echo "doc.text('$".number_format($operation->price_out,2,".",",")."', 140, $row_pos);";
    echo "doc.text('$".number_format($operation->q*$operation->price_out,2,".",",")."', 175, $row_pos);";
    

    $i++;
    $j++;
  }

  ?>
  // Final, guardar el documento
  doc.save('cotizacion-<?php echo $sell->created_at; ?>.pdf');
}
</script>