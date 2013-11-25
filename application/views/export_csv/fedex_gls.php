<article>
    <h1><?php echo $title;?></h1>
    <?php if(!empty($summary)) { ?>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/export_csv/'.$method;?>">
                <img src="<?php echo base_url().'assets/imgs/Button-Download-icon.png';?>" alt="<?php echo $title;?>" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/export_csv/'.$method;?>">
                <span><?php echo humanize($title);?></span>
            </a>
        </div>
    </div>
    <div>
        <p>
            Total de pedidos a procesar en el fichero: <?php echo count($summary);?>
        </p>
        <p>
            Introducir en la tabla de ingresos
        </p>
        <table class="thin_table">
            <tr>
                <th>Indice</th>
                <th>Pedido numero</th>
                <th>Estado</th>
                <th>Pagina</th>
                <th>Comentario</th>
                <th>Ingreso</th>
                <th>Coste</th>
            </tr>
            <?php $i=0;?>
            <?php foreach($summary as $item) { ?>
            <?php $i++;?>
                <tr>
                    <td><?php echo $i;?></td>
                    <td class="bold"><?php echo $item->pedido;  ?></td>
                    <td>PROCESADO</td>
                    <td><?php echo $item->web;?></td>
                    <td></td>
                    <td class="ingreso"><?php echo number_format($item->ingresos,2);?>&euro;</td>
                    <td class="gasto"><?php echo number_format($item->gasto,2);   ?>&euro;</td>
                </tr>    
            <?php } ?>  
        </table>
    </div>
    <div id="modal_window"></div>
    
    <?php } else { ?>
    <div>
        <p>Have no such orders.</p>
    </div>
    <?php } ?>
</article>