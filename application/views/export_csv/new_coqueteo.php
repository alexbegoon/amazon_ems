<?php
/**
 * Description of new_coqueteo
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title;?></h1>
    <?php echo form_open($button_link);?>
    <div class="filters">
        <?php if( isset($filters) ) :?>
        <?php foreach ($filters as $filter) {
            echo $filter;
        }?>
        <?php endif;?>
    </div>
    <input type="submit" value="<?php echo $button_name;?>" />
        
    <?php echo form_close();?>
</article>
<script>
    $(function(){
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
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                },
                onClose: function(){
                    
                    $('#date_picker, #date_picker_2').attr('required','required');
                    
                    $('#radios input').removeAttr('checked');
                    $( "#radios" ).buttonset('refresh');
                    
                    if( $('#date_picker').val() == '' && $('#date_picker').val() == '' )
                    {
                        $('#date_picker, #date_picker_2').removeAttr('required');
                    }
                    
                }
            });
    });
</script>