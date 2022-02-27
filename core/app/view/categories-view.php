        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Categorias
          </h1>
          <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
            <li class="active">Here</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">

<div class="row">
	<div class="col-md-12">
<div class="btn-group pull-right">
	<a href="index.php?view=newcategory" class="btn btn-default"><i class='fa fa-th-list'></i> Nueva Categoria</a>
</div>
<div class="clearfix"></div>
<br>
		<?php

		$categories = CategoryData::getAll();
		if(count($categories)>0){
			// si hay usuarios
			?>
<div class="box">
  <div class="box-header">
    <h3 class="box-title">Categorias</h3>

  </div><!-- /.box-header -->
  <div class="box-body">

			<table class="table table-bordered datatable table-hover">
			<thead>
			<th>Nombre</th>
			</thead>
			<?php
			foreach($categories as $category){
				?>
				<tr>
				<td style="width:30px;"><a href="index.php?view=productbycategory&id=<?php echo $category->id;?>" class="btn btn-default btn-xs"><i class="fa fa-th-list"></i> Productos</a> 
				</td>
				<td><?php echo $category->name; ?></td>
				<td style="width:130px;"><a href="index.php?view=editcategory&id=<?php echo $category->id;?>" class="btn btn-warning btn-xs">Editar</a> <a href="index.php?view=delcategory&id=<?php echo $category->id;?>" class="btn btn-danger btn-xs">Eliminar</a></td>
				</tr>
				<?php

			}

?>
			</table>
  </div><!-- /.box-body -->
</div><!-- /.box -->
			
			<?php


		}else{
			echo "<p class='alert alert-danger'>No hay Categorias</p>";
		}


		?>


	</div>
</div>
        </section><!-- /.content -->