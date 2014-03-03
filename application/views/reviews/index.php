<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of index
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>

<article>
    <h1><?php echo $title; ?></h1>
    <div>
        <?php echo form_open(current_url()); ?>
        <div class="filters">
            <div id="rating_filter_radios">
                Web:
                <?php echo $web_field_filter;?>
                <?php echo nbs(8);?>
                Rating:
                <?php echo $rating_filter;?>
            </div>
        </div>
        <div>
            <?php if($reviews) { ?>
                <div>
                    <p><?php echo $total_rows;?> reviews found</p>
                </div>
                <div class="pagination">
                <?php echo $pagination;?>
                </div>
                <table class="thin_table">
                    <tr>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Product name</th>
                        <th>Product sku</th>
                        <th>Date</th>
                        <th>WEB</th>
                    </tr>
                <?php foreach ($reviews as $review) {?>
                    <tr>
                        <td><?php echo get_rating_stars($review->rating); ?></td>
                        <td><?php echo stripslashes($review->comment); ?></td>
                        <td style="width:30%;">
                            <?php echo htmlentities($review->product_name); ?>
                            <br>
                            <?php echo $review->link; ?>
                        </td>
                        <td style="width:20%;">
                            <?php echo $review->product_sku; ?>
                            <?php 
                                    if($review->product_sku != $review->provider_product_sku && !empty($review->provider_product_sku))
                                    {
                                        ?>
                                            
                                            (Original provider SKU : <?php echo $review->provider_product_sku; ?>)
                            
                                        <?php
                                    }
                                    
                            ?>
                        </td>
                        <td style="width:150px;"><?php echo $review->created; ?></td>
                        <td class="web <?php echo strtolower($review->web);?>">
                            <?php echo $review->web; ?>
                        </td>
                    </tr>
                    
            
                <?php } ?>
                </table>
                <div class="pagination">
                <?php echo $pagination;?>
                </div>
                <div>
                    <p><?php echo $total_rows;?> reviews found</p>
                </div>
            <?php } else { ?>
            Reviews not found
            <?php } ?>
        </div>
        </form>
    </div>
</article>
<script>
    $(function(){
        
        $('#rating_filter_radios').buttonset();

        $("#rating_filter_radios input").click(function(){
           $("form").submit();
        });

        $('#web_fields_list').change(function(){
            $("form").submit();             
        });

        $('#web_fields_list').combobox({
            select: function( event, ui ) {
                $("form").submit(); 
            }
        });
        
    });
</script>