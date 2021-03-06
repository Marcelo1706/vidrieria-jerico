<section class="content">
<div class="btn-group pull-right">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-download"></i> Imprimir <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
    <li><a onclick="create_pdf()" id="makepdf2" class=""><i class="fa fa-download"></i>Factura</a>
  </ul>
</div>
<h1>Resumen de Venta</h1>
<?php if(isset($_GET["id"]) && $_GET["id"]!=""):?>
<?php
$sell = SellData::getById($_GET["id"]);
$operations = OperationData::getAllProductsBySellId($_GET["id"]);
$total = 0;
?>
<?php
if(isset($_COOKIE["selled"])){
  foreach ($operations as $operation) {
//    print_r($operation);
    $qx = OperationData::getQByStock($operation->product_id,StockData::getPrincipal()->id);
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

?>
<div class="box box-primary">
<table class="table table-bordered">
<?php if($sell->person_id!=""):
    $client = $sell->getPerson();
    $nombre_cliente = $client->name." ".$client->lastname;
  elseif($sell->client_name!=""):
    $nombre_cliente = $sell->client_name;
?>
<tr>
  <td style="width:150px;">Cliente</td>
  <td><?php echo $nombre_cliente; ?></td>
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
<br>
<div class="box box-primary">
<table class="table table-bordered table-hover">
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
  <td>$ <?php echo number_format($operation->price_out,2,".",",") ;?></td>
  <td><b>$ <?php echo number_format($operation->q*$operation->price_out,2,".",",");$total+=$operation->q*$operation->price_out;?></b></td>
</tr>
<?php
  }
  ?>
</table>
</div>
<br><br>
<div class="row">
<div class="col-md-4">
<div class="box box-primary">
<table class="table table-bordered">
  <tr>
    <td><h4>Descuento:</h4></td>
    <td><h4>$ <?php echo number_format($sell->discount,2,'.',','); ?></h4></td>
  </tr>
  <tr>
    <td><h4>Subtotal:</h4></td>
    <td><h4>$ <?php echo number_format($total,2,'.',','); ?></h4></td>
  </tr>
  <tr>
    <td><h4>Total:</h4></td>
    <td><h4>$ <?php echo number_format($total-  $sell->discount,2,'.',','); ?></h4></td>
  </tr>
</table>
</div>

<?php if($sell->person_id!=""):
$credit=PaymentData::sumByClientId($sell->person_id)->total;

?>
<div class="box box-primary">
<table class="table table-bordered">
  <tr>
    <td><h4>Saldo anterior:</h4></td>
    <td><h4>$ <?php echo number_format($credit-$total,2,'.',','); ?></h4></td>
  </tr>
  <tr>
    <td><h4>Saldo Actual:</h4></td>
    <td><h4>$ <?php echo number_format($credit,2,'.',','); ?></h4></td>
  </tr>
</table>
</div>
<?php endif;?>
</div>
</div>






<script type="text/javascript">
        function thePDF() {

var columns = [
//    {title: "Reten", dataKey: "reten"},
    {title: "Codigo", dataKey: "code"}, 
    {title: "Cantidad", dataKey: "q"}, 
    {title: "Nombre del Producto", dataKey: "product"}, 
    {title: "Precio unitario", dataKey: "pu"}, 
    {title: "Total", dataKey: "total"}, 
//    ...
];


var columns2 = [
//    {title: "Reten", dataKey: "reten"},
    {title: "", dataKey: "clave"}, 
    {title: "", dataKey: "valor"}, 
//    ...
];

var rows = [
  <?php foreach($operations as $operation):
  $product  = $operation->getProduct();
  ?>

    {
      "code": "<?php echo $product->id; ?>",
      "q": "<?php echo $operation->q; ?>",
      "product": "<?php echo $product->name; ?>",
      "pu": "$ <?php echo number_format($operation->price_out,2,".",","); ?>",
      "total": "$ <?php echo number_format($operation->q*$operation->price_out,2,".",","); ?>",
      },
 <?php endforeach; ?>
];

var rows2 = [
<?php if($sell->person_id!=""):
$person = $sell->getPerson();
?>

    {
      "clave": "Cliente",
      "valor": "<?php echo $person->name." ".$person->lastname; ?>",
      },
<?php elseif($sell->client_name != ""): ?>
    {
      "clave": "Cliente",
      "valor": "<?php echo $sell->client_name; ?>",
      },
<?php endif; ?>
    {
      "clave": "Atendido por",
      "valor": "<?php echo $user->name." ".$user->lastname; ?>",
      },

];

var rows3 = [

    {
      "clave": "Descuento",
      "valor": "$ <?php echo number_format($sell->discount,2,'.',',');; ?>",
      },
    {
      "clave": "Subtotal",
      "valor": "$ <?php echo number_format($sell->total,2,'.',',');; ?>",
      },
    {
      "clave": "Total",
      "valor": "$ <?php echo number_format($sell->total-$sell->discount,2,'.',',');; ?>",
      },
];


// Only pt supported (not mm or in)
var doc = new jsPDF('p', 'pt');
        doc.setFontSize(26);
        doc.text("NOTA DE VENTA", 40, 65);
        doc.setFontSize(14);
        doc.text("Fecha: <?php echo $sell->created_at; ?>", 40, 80);
//        doc.text("Operador:", 40, 150);
//        doc.text("Header", 40, 30);
  //      doc.text("Header", 40, 30);

doc.autoTable(columns2, rows2, {
    theme: 'grid',
    overflow:'linebreak',
    styles: {
        fillColor: [100, 100, 100]
    },
    columnStyles: {
        id: {fillColor: 255}
    },
    margin: {top: 100},
    afterPageContent: function(data) {
//        doc.text("Header", 40, 30);
    }
});


doc.autoTable(columns, rows, {
    theme: 'grid',
    overflow:'linebreak',
    styles: {
        fillColor: [100, 100, 100]
    },
    columnStyles: {
        id: {fillColor: 255}
    },
    margin: {top: doc.autoTableEndPosY()+15},
    afterPageContent: function(data) {
//        doc.text("Header", 40, 30);
    }
});

doc.autoTable(columns2, rows2, {
    theme: 'grid',
    overflow:'linebreak',
    styles: {
        fillColor: [100, 100, 100]
    },
    columnStyles: {
        id: {fillColor: 255}
    },
    margin: {top: doc.autoTableEndPosY()+15},
    afterPageContent: function(data) {
//        doc.text("Header", 40, 30);
    }
});
//doc.setFontsize
//img = new Image();
//img.src = "liberacion2.jpg";
//doc.addImage(img, 'JPEG', 40, 10, 610, 100, 'monkey'); // Cache the image using the alias 'monkey'
doc.setFontSize(20);
doc.setFontSize(12);
doc.text("Generado por el Sistema de inventario", 40, doc.autoTableEndPosY()+25);
doc.save('venta-<?php echo date("d-m-Y h:i:s",time()); ?>.pdf');
//doc.output("datauri");

        }
    </script>

<script>
  $(document).ready(function(){
  //  $("#makepdf").trigger("click");
  });
</script>

<!-- Nueva funci??n porque no pude eliminar la otra xD -->
<script>
function create_pdf(){ 
  var doc = new jsPDF('p', 'mm', [215, 140]);
  doc.setFontSize(8);
  <?php
  $header = "
    doc.text('".$sell->created_at."', 80, 55);
  ";
  if($sell->person_id!=""){
    $person = $sell->getPerson();
    $header .= "
    doc.text('".$person->name." ".$person->lastname."', 45, 60);
    doc.text('".$person->no."', 45, 66);
    doc.text('".$person->address1."', 30, 72);
    ";
  } else if ($sell->client_name!=""){
    $header .= "doc.text('".$sell->client_name."', 45, 60);";
  }
  $header .= "
    doc.text('".$user->name." ".$user->lastname."', 45, 77);
  ";
  $page_break = "doc.addPage();";

  # Crear un bucle que cada 13 productos me imprima $header
  $i = 0;
  $total = 0;
  $page_total = 0;
  $y_axis = 91;
  $j = 0;
  foreach($operations as $operation){
    $product = $operation->getProduct();
    
    if($i == 0){
      echo $header;
    }
    if($i % 13 == 0 && $i != 0 || $i == count($operations)-1){
      if($i == count($operations)-1){
        $page_total += $operation->q*$operation->price_out;
      }
      $iva = $page_total * 0.13;
      echo "doc.text('$".number_format($page_total-$iva,2,".",",")."', 115, 166);";
      echo "doc.text('$".number_format($iva,2,".",",")."', 115, 172);";
      echo "doc.text('$".number_format($page_total,2,".",",")."', 115, 178);";
      echo "doc.text('$".number_format($page_total,2,".",",")."', 115, 196);";
      if($i % 13 == 0 && $i != count($operations)-1){
        echo $page_break;
        echo $header;
        $page_total = 0;
        $y_axis = 91;
        $j = 0;
      }
    }

    $page_total += $operation->q*$operation->price_out;
    $row_pos = $y_axis+($j*5.6);
    echo "doc.text('".$operation->q."', 10, $row_pos);";
    echo "doc.text('".$product->name."', 20, $row_pos);";
    echo "doc.text('$".number_format($operation->price_out,2,".",",")."', 80, $row_pos);";
    echo "doc.text('$".number_format($operation->q*$operation->price_out,2,".",",")."', 115, $row_pos);";
    

    $i++;
    $j++;
  }

  ?>
  // Final, guardar el documento
  doc.save('venta-<?php echo $sell->created_at; ?>.pdf');
}
</script>




<?php else:?>
  501 Internal Error
<?php endif; ?>
</section>