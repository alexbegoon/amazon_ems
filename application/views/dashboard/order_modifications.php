<?php
/**
 * Description of order_modifications
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open(base_url().'index.php/dashboard/order_modifications/');?>
    <div class="filters">
        <label for="date_picker">Date from: </label>
        <input type="text" name="date_from" value="" id="date_picker">
        <label for="date_picker_2">Date to: </label>
        <input type="text" name="date_to" value="" id="date_picker_2">
        <input type="submit" value="Buscar">
    </div>
    <div>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
        <table class="thin_table">
            <tr>
                <th>Order ID</th>
                <th>SKU</th>
                <th>Action</th>
                <th>User Name</th>
                <th>Date</th>
                <th>View/Edit</th>
            </tr>
            <?php foreach($modifications_list as $row): ?>
            <tr>
                <td><?php echo $row->order_id; ?></td>
                <td><?php echo $row->product_sku; ?></td>
                <td><?php echo $row->action == '1' ? 'Added' : 'Removed'; ?></td>
                <td><?php echo $this->ion_auth->user($row->user_id)->row()->first_name .
            ' '.
            $this->ion_auth->user($row->user_id)->row()->last_name;?></td>
                
                <td><?php echo $row->created_on; ?></td>
                <td><a href="#" class="edit" onclick="edit(<?php echo $row->order_id;?>);return false;"></a></td>
            </tr>
            <?php endforeach;?>
        </table>
        <div class="pagination">
        <?php echo $pagination;?>
        </div>
    </div>
    <?php echo form_close();?>
</article>
<script type="text/javascript">

    $(function(){
        $('#providers_list').combobox();

        // Datepicker
                $('#date_picker, #date_picker_2').datepicker({
                    dateFormat: 'yy-mm-dd',
                    onSelect: function(  ) {
                        var dateFrom = $('#date_picker').datepicker("getDate");
                        var dateTo   = $('#date_picker_2').datepicker("getDate");
                        var rMin = new Date(dateFrom); 
                        var rMax = new Date(dateTo);
                        if(this.id == 'date_picker')
                        {
                            $('#date_picker_2').datepicker("option","minDate",new Date(rMin.getTime() + 86400000));
                            $('#date_picker').datepicker("option","maxDate",rMin);
                        }
                        else
                        {
                            $('#date_picker_2').datepicker("option","minDate",rMax);
                            $('#date_picker').datepicker("option","maxDate",new Date(rMax.getTime() - 86400000));
                        }

                        $('#date_picker, #date_picker_2').attr('required','required');
                    },
                    onClose: function(){

                        $('#date_picker, #date_picker_2').attr('required','required');

                        if( $('#date_picker').val() == '' && $('#date_picker').val() == '' )
                        {
                            $('#date_picker, #date_picker_2').removeAttr('required');
                        }

                    }
                });
    });

</script>