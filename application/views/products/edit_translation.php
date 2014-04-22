<?php
/**
 * Description of edit_translation
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */

$inputs = array(
    
    'product_name' => array(
        'id'        => 'product_name',
        'name'      => 'product_name',
        'value'     => $product_name,
        'title'     => 'Product name',
        'maxlength' => '255',
        'cols'      => '80',
    ),
    
    'product_desc' => array(
        'id'        => 'product_desc',
        'name'      => 'product_desc',
        'value'     => $product_desc,
        'title'     => 'Product description',
        'cols'      => '80',
    ),
    
    'product_s_desc' => array(
        'id'        => 'product_s_desc',
        'name'      => 'product_s_desc',
        'value'     => $product_s_desc,
        'title'     => 'Product short description',
        'cols'      => '80',
    ),
    
    'meta_desc' => array(
        'id'        => 'meta_desc',
        'name'      => 'meta_desc',
        'value'     => $meta_desc,
        'title'     => 'Meta description',
        'maxlength' => '255',
        'cols'      => '80',
    ),
    
    'meta_keywords' => array(
        'id'        => 'meta_keywords',
        'name'      => 'meta_keywords',
        'value'     => $meta_keywords,
        'title'     => 'Meta keywords',
        'maxlength' => '255',
        'cols'      => '80',
    ),
    
    'custom_title' => array(
        'id'        => 'custom_title',
        'name'      => 'custom_title',
        'value'     => $custom_title,
        'title'     => 'Custom page title',
        'maxlength' => '255',
        'cols'      => '80',
    ),
    
    'slug' => array(
        'id'        => 'slug',
        'name'      => 'slug',
        'value'     => $slug,
        'title'     => 'Slug',
        'maxlength' => '255',
        'cols'      => '80',
    ),
    
);

?>
<div>
    <?php echo form_open(base_url().'index.php/products/save/', 'id="edit_product"');?>
    <div>
        <div>
            <label for="translation_languages">Languages</label>
            <br>
            <?php echo $translation_languages_dropdown;?>
        </div>
        <br>
        <label for="provider_product_name">Provider product name (read only)</label>
        <br>
        <input size="105" name="provider_product_name" id="provider_product_name" readonly="true" value="<?php echo htmlentities($provider_product_name);?>" />
        <br>
        <br>
        <?php foreach ($inputs as $key => $value) : ?>
        <div>
            <?php echo form_label($value['title'], $key);?>
            <br>
            <?php echo form_textarea($value);?>
        </div>
        <br>
        <?php endforeach;?>
        
        <input type="hidden" name="product_id" value="<?php echo $product_id?>" />
        
        <div class="edit-buttons">
            <div>
                <span id="all_saved" class="green" style="display: none!important;">All changes saved.</span>
            </div>
            <input type="button" id="edit-save" value="Save" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <input type="button" id="edit-close" value="Cancel" />
        </div>
    </div>
    <?php echo form_close();?>
</div>
<script>
    Amazoni.save_translation = function(){
        
        // setup some local variables
        var $form = $("#edit_product");
        // let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");
        // serialize the data in the form
        var serializedData = $form.serialize();

        // let's disable the inputs for the duration of the ajax request
        $inputs.prop("disabled", true);
        
        // fire off the request to /form.php
        request = $.ajax({
            url: url_before_index + "index.php/products/save",
            type: "post",
            data: serializedData
        });
        
        // callback handler that will be called regardless
        // if the request failed or succeeded
        request.always(function () {
            // reenable the inputs
            $inputs.prop("disabled", false);
        });
        
        request.done(function (response, textStatus, jqXHR){
            return true;
        });
        
        // callback handler that will be called on failure
        request.fail(function (jqXHR, textStatus, errorThrown){
            alert(textStatus + ': '+ errorThrown);
            return false;
        });
        
    };
    
    Amazoni.get_tinymce = function(){
            
        tinymce.remove();
        tinymce.init({
            mode : 'textareas',
            language : 'es',
            selector: "textarea#product_desc",
            theme: "modern",
            plugins: [
                            "advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                            "save table contextmenu directionality emoticons template paste textcolor"
                      ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",
            height: 250,
            setup: function(editor) {
                editor.on('blur', function(e) {
                    tinymce.triggerSave();
                    Amazoni.save_translation();
                });
            }
         });
    };
    
    $(function(){
        
        Amazoni.get_tinymce();
        
        $('textarea').change(function(){
            tinymce.triggerSave();
            Amazoni.save_translation();
        });       
        
        
        $('#edit-save').click(function(){
            $("#all_saved").show().delay(2000).fadeOut();
        });
        
        $('#translation_languages').combobox({
            select: function( event, ui ){
                
                tinymce.remove();
                
                // setup some local variables
                var $form = $("#edit_product");
                // let's select and cache all the fields
                var $inputs = $form.find("input, select, button, textarea");

                // let's disable the inputs for the duration of the ajax request
                $inputs.prop("disabled", true);
                
                request = $.ajax({
                    url: url_before_index + "index.php/products/get_translation",
                    type: "post",
                    data: {id:'<?php echo $product_id?>',language_code:$("#translation_languages").val()}
                });
                
                request.done(function (response, textStatus, jqXHR){
                    $form.find("textarea").val("");
                    $.each(response, function(key, val) {
                        if(key && val)
                        {
                            $("#edit_product #"+key).val(val);
                        }
                    });
                });
                
                // callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus, errorThrown){
                    console.log(textStatus + ': '+ errorThrown);
                });

                // callback handler that will be called regardless
                // if the request failed or succeeded
                request.always(function () {
                    // reenable the inputs
                    $inputs.prop("disabled", false);
                    Amazoni.get_tinymce();
                });
            }
                
        });
    });
</script>