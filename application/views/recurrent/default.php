<article>
    <h1><?php echo $title;?></h1>
    <form id="recurrent-form" method="post" action="<?php echo base_url().'index.php/recurrent/page/';?>">
        <div class="filters">
            <div class="ui-widget">
                <label for="search">Buscar: </label>
                <input id="search" type="text" name="filter[search]" value="<?php echo $filter['search'];?>" />
                <input type="submit" value="Search" />
            </div>
        </div>
        <div>
            <p><?php echo $total_rows;?> buyers found</p>
        </div>
        <?php if (!empty($recurrent_buyers)) { 
            ?>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="recurrent">
            <tr>
                <th>Name</th>
                <th>E-mail</th>
                <th>Number of orders</th>
                <th>Total amount</th>
            </tr>
            <?php if (!empty($recurrent_buyers)) {
                foreach ($recurrent_buyers as $buyer) {
                    ?>
            <tr onclick="showOrders('<?php echo $buyer->correo; ?>');">
                <td><?php echo htmlentities($buyer->nombre); ?></td>
                <td><?php echo $buyer->correo; ?></td>
                <td><?php echo $buyer->total_number; ?></td>
                <td><?php echo $buyer->total_amount; ?></td>
            </tr>
                    <?php
                }
            }?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <div>
            <p><?php echo $total_rows;?> buyers found</p>
        </div>
            <?php
        }?>
    </form>
</article>
<div id="dialog-orders" title="">
</div>