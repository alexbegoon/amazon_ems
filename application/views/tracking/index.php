<article>
    <h1><?php echo $title;?></h1>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/tracking/edit_template/es';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/Spain.png';?>" alt="Edit Spanish Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/tracking/edit_template/es';?>">
                <span>Edit Spanish Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/tracking/edit_template/en';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/United Kingdom(Great Britain).png';?>" alt="Edit English Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/tracking/edit_template/en';?>">
                <span>Edit English Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/tracking/edit_template/de';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/Germany.png';?>" alt="Edit Deutsch Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/tracking/edit_template/de';?>">
                <span>Edit Deutsch Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/tracking/edit_template/fr';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/France.png';?>" alt="Edit French Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/tracking/edit_template/fr';?>">
                <span>Edit French Email</span>
            </a>
        </div>
    </div>
    <br>
    <?php if (!empty($error) && $error != ' ') { ?>
    <div id="ajax-msg" style="display: none;">
        <?php echo $error;?>
    </div>
    <?php } ?>
    <div class="tracking_wrapper">
        <div class="tracking_div_1">
            <h2>Trackings Generales</h2>
            <?php echo form_open(base_url().'index.php/tracking/get_order/', array('id' => 'tracking_form_1'));?>
                <div>
                    <label for="pedido">Número de pedido</label>
                    <input id="pedido" type="text" name="pedido" />
                </div>
                <div>
                    <input type="submit" value="Buscar" />
                </div>
            </form>
        </div>
        <div class="tracking_div_2">
            <h2>Que no se te escape ni uno...</h2>
            <?php echo form_open_multipart(base_url().'index.php/upload/gls_tracking_file/', array('id' => 'tracking_form_2'));?>
            <input type="file" name="userfile" size="20" value="Seleccionar archivo" />
            <br>
            <br>
            <input type="submit" value="Enviar" />
            </form>
        </div>
    </div>
    <?php if(!empty($summary)) { ?>
    <div>
        <div>
            <br>
            <?php if($is_amazon_file_exists) { ?>
                <a href="<?php echo base_url().'index.php/tracking/get_amazon_tracking_file/' ?>">Download amazonTracking-<?php echo date('Y-m-d', time());?>.txt file</a>
            <?php } ?>
            <br>
            <p>
                A list with the tracking reads of <?php echo $upload_data['file_name']; ?> file
            </p>
            <table class="thin_table">
                <tr>
                    <th>#</th>
                    <th>Pedido</th>
                    <th>State</th>
                    <th>Nombre</th>
                    <th>Tracking</th>
                </tr>
                <?php $i=0; ?>
                <?php foreach ($summary['parse_file'] as $row) { ?>
                <?php $i++; ?>
                <tr>
                    <td><?php echo $i?></td>
                    <td class="bold"><?php echo $row['pedido']?></td>
                    <td><?php echo $row['state']?></td>
                    <td><?php echo htmlentities($row['nombre'])?></td>
                    <td><?php echo $row['tracking']?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div>
            <br>
            <p>
                A list of the tracking send to our websites
            </p>
            <table class="thin_table">
                <tr>
                    <th>#</th>
                    <th>Pedido</th>
                    <th>Web</th>
                    <th>State</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Tracking</th>
                </tr>
                <?php $i=0; ?>
                <?php foreach ($summary['tracking_send'] as $row) { ?>
                <?php $i++; ?>
                <?php if(isset($row['conflict'])) { ?>
                    <?php if($row['conflict']) { ?>
                    <tr class="conflict">
                    <?php } else { ?>
                    <tr class="resolved">
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                <?php } ?>        
                    <td><?php echo $i?></td>
                    <td class="bold"><?php echo $row['pedido']?></td>
                    <td><?php echo $row['web']?></td>
                    <td><?php echo $row['state']?></td>
                    <td><?php echo htmlentities($row['nombre'])?></td>
                    <td><?php echo $row['correo']?></td>
                    <td><?php echo $row['tracking']?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
        <div>
            <br>
            <p>
                A list of the orders that go on the amazonTracking-<?php echo date('Y-m-d', time());?>.txt file
            </p>
            <table class="thin_table">
                <tr>
                    <th>#</th>
                    <th>Pedido</th>
                    <th>Date</th>
                    <th>Tracking</th>
                </tr>
                <?php $i=0; ?>
                <?php foreach ($summary['amazon_file'] as $row) { ?>
                <?php $i++; ?>
                <tr>
                    <td><?php echo $i?></td>
                    <td class="bold"><?php echo $row['order-id']?></td>
                    <td><?php echo $row['ship-date']?></td>
                    <td><?php echo $row['tracking-number']?></td>
                </tr>
                <?php } ?>
            </table>
            <br>
            <?php if($is_amazon_file_exists) { ?>
                <a href="<?php echo base_url().'index.php/tracking/get_amazon_tracking_file/' ?>">Download amazonTracking-<?php echo date('Y-m-d', time());?>.txt file</a>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    
    
</article>
<script>
    $(function() {
        if (!$.isNumeric($('#pedido').val())){
            $('#pedido').addClass('wrong');
            $('#pedido').focus();
            return false;
        } else {
            $(this).removeClass('wrong');
        } 
    });
    $(function() {
        $("#ajax-msg").fadeIn();
    });
</script>
<div id="modal_window"></div>