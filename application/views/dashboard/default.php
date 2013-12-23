<article>
    <h1><?php echo $title;?></h1>
    <?php //print_r($orders); ?>
    <form id="dashboard-form" method="post" action="<?php echo base_url().'index.php/dashboard/page/';?>">
        <?php $filter = $this->input->post("filter");?>
        <div class="filters">
            <div class="ui-widget">
                <input type="button" id="create_order" value="Crear Pedido" onclick="AJAX_add('<?php echo base_url().'index.php/dashboard/create_order';?>')"/>
                <label for="search">Buscar: </label>
                <input id="search" type="text" name="filter[search]" value="<?php echo $filter['search'];?>" />
                <label for="combobox">Web: </label>
                <?php echo $web_fields_list; ?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="combobox2">Estado: </label>
                <select id="combobox2" name="filter[procesado]">
                    <?php getStatusOptions($filter['procesado']); ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Buscar" />
                <label for="combobox3">Cambiar a: </label>
                <select id="combobox3" name="filter[change_to]">
                    <?php getStatusOptions(); ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input class="cambiar_todos" type="submit" value="Cambiar Todos" />
            </div>
        </div>
        <div>
            <p><?php echo $total_orders;?> orders found</p>
        </div>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="orders">
            <tr>
                <th>Action</th>
                <th>Id</th>
                <th>Pedido</th>
                <th>Info</th>
                <th>Nombre</th>
                <th>Fechaentrada</th>
                <th>Direccion</th>
                <th>Pais</th>
                <th>Procesado</th>
                <th>Ingresos</th>
                <th>Web</th>
                <th>Comentarios</th>
                <th>R</th>
                <th>Tracking</th>
            </tr>
            <?php foreach ($orders as $order) { ?>
            <?php 
            
                $info = '';
                        
                if($order->warehouse_sales == true)
                {
                    $info = '<b class="stokoni">&nbsp;Stokoni&nbsp;</b>';
                }
                
                if($order->have_errors == true)
                {
                    $info = '<b class="error_icon" title="Order have an error. Please ask Support.">&nbsp;</b>';
                }
            
                $procesado_class = strtolower($order->procesado);

                if (strpos($order->procesado, 'ENVIADO_') !== false) {
                    $procesado_class = 'enviado';
                }

                if (strpos($order->procesado, 'PREPARACION_') !== false) {
                    $procesado_class = 'preparacion';
                }

            ?>
            
            <tr id="<?php echo $order->id;?>">
                <!-- Action -->
                <td>
                    <a href="#" class="edit" onclick="edit(<?php echo $order->id;?>);return false;"></a>
                </td>
                <!-- Id -->
                <td><?php echo $order->id ;?></td>
                <!-- Pedido -->
                <td class="bold"><?php echo $order->pedido ;?></td>
                <!-- Info -->
                <td class="bold"><?php echo $info;?></td>
                <!-- Nombre -->
                <td class="nombre"><?php echo  htmlentities($order->nombre) ;?></td>
                <!-- Fechaentrada -->
                <td><?php echo $order->fechaentrada ;?></td>
                <!-- Direccion -->
                <td class="direccion"><?php echo htmlentities($order->direccion) ;?></td>
                <!-- Pais -->
                <td class="pais"><?php echo $order->pais ;?></td>
                <!-- Procesado -->
                <td class="procesado <?php echo $procesado_class;?>"><?php echo $order->procesado ;?></td>
                <!-- Ingresos -->
                <td class="ingresos ingreso"><?php echo number_format($order->ingresos, 2);?>&euro;</td>
                <!-- Web -->
                <td class="web <?php echo strtolower($order->web);?>"><?php echo $order->web ;?></td>
                <!-- Comentarios -->
                <td class="comentarios">
                    <?php if (!empty($order->comentarios)) { ?>
                    <a href="#" onclick="open_modal_with_content(this.getAttribute('title'));return false;" class="comment" title="<?php echo htmlentities($order->comentarios);?>"></a>
                    <?php } ?>                
                </td>
                <!-- Recurrent buyer -->
                <td><?php echo $order->total_number ;?></td>
                <!-- Tracking -->
                <td class="tracking"><?php echo $order->tracking ;?></td>
            </tr>
            <input type="hidden" name="orders_ids[]" value="<?php echo $order->id;?>" />
            
            <?php } ?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <div>
            <p><?php echo $total_orders;?> orders found</p>
        </div>
    </form>    
</article>

<div id="dialog-modal" title="Basic modal dialog">
</div>
<div id="dialog-confirm" title="">
</div>
<div id="modal_window"></div>