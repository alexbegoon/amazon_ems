//http://www.tinymce.com/wiki.php/Tutorials:TinyMCE_in_a_jQuery_UI_dialog
//http://fiddle.tinymce.com/rsdaab
//
// Prevent jQuery UI dialog from blocking focusin
$(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window").length) {
                e.stopImmediatePropagation();
        }
});


Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
 
var notification_sound = url_before_index + 'assets/sounds/notify'

// Namespace 
window.Amazoni = {};

function capitaliseFirstLetter(string)
{
    string = string.toLowerCase();
    return string.charAt(0).toUpperCase() + string.slice(1);
}

Amazoni.new_message_notification = function(){
    
    $("div.menu_link_new_message").hide().show( 'shake' , function(){
        $('#chatAudio')[0].play();
    });
    
};

Amazoni.update_stock = function(url, provider_name){
    
    var loader = $('#loader_'+provider_name);
    
    $.ajax({
        type: "post",
        url: url,
        beforeSend: function(jqXHR){
            loader.show();
        },
        complete: function(){
            loader.hide();
        },
        success: function(response, code){
            if(response === 'Done')
            {
                alert(provider_name+' stock updated');
            }
        },
        error: function(jqXHR, textStatus, errorThrown ){
            alert(errorThrown);
        }
    });
}

Amazoni.show_provider_statistic = function(provider)
{
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }
    
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
    minHeight: 600,    
    minWidth: 1200,
    width: 1250,
    modal: true,
    title: capitaliseFirstLetter(provider) + ' products statistic history',
    resizable: true,
    buttons: null,
    close: function( event, ui ) {
        $( "#modal_window" ).empty();
    },
    open: function(event, ui) { 
        
        $( "#modal_window" ).append('<div id="ajax-loader" style="display: block;"></div>');
        $("#ajax-loader").css('display', 'block');
        $.ajax({
                type: "POST",
                url: url_before_index + "index.php/products/show_provider_statistic/" + provider
              }).success(function( response ) {
                $("#ajax-loader").css('display', 'none');  
                $( "#modal_window" ).append(response);
              });
        }
    });
}

Amazoni.clear_product_in_order = function(n){
    
    var n = parseInt(n);
    var m = 0;
    
    for (var i=1;i<=10;i++)
    {
        if(i >= n)
        {
            m = i + 1;
            $('input[name=sku'+i+']').val($('input[name=sku'+m+']').val());
            $('input[name=cantidad'+i+']').val($('input[name=cantidad'+m+']').val());
            $('input[name=precio'+i+']').val($('input[name=precio'+m+']').val());
        }
    }    
};

Amazoni.get_order_for_print = function(id){
    
    var order = {};
    
    $.ajax({
            type: "POST",
            url: url_before_index + "index.php/dashboard/get_order_for_printer/" + id
          }).success(function( order ) {
             
            if ($( "#printer_document" ).length === 0)
            {
                $('body').append('<div id="printer_document" class="printer_document"></div>');
            }

            $( "#printer_document" ).empty();
             console.log(order);
             $( "#printer_document" ).html(order.html_of_order);

            window.print();
          });
          
    
};

Amazoni.order_link = function(a){
    
    var link = $(a);
    var order_by = link.attr('id').replace('order_by_','');
    var order_option = 'DESC';
    
    if(Amazoni.order_option == "DESC")
    {
        order_option = "ASC";
    }
    
    $('form').append('<input type="hidden" name="order_by" value="'+order_by+'" />'); 
    $('form').append('<input type="hidden" name="order_option" value="'+order_option+'" />'); 
    $('form').submit();
    
};

Amazoni.get_provider_order = function(id, return_url)
{
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }
    
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
    minHeight: 600,    
    minWidth: 1200,
    width: 1250,
    modal: true,
    title: 'Provider order ID: '+id,
    resizable: true,
    buttons: null,
    close: function( event, ui ) {
        $( "#modal_window" ).empty();
    },
    open: function(event, ui) { 
        
        $( "#modal_window" ).append('<div id="ajax-loader" style="display: block;"></div>');
        $("#ajax-loader").css('display', 'block');
        $.ajax({
                type: "POST",
                url: url_before_index + "index.php/providers/get_order/" + id + "/" + return_url
              }).success(function( response ) {
                $("#ajax-loader").css('display', 'none');  
                $( "#modal_window" ).append(response);
              });
        }
    });
}

Amazoni.confirm_order_sending = function(a)
{
    if ($( "#modal_window_confirm" ).length === 0)
        {
            $('body').append('<div id="modal_window_confirm"></div>');
        }
    
    $( "#modal_window_confirm" ).empty();

    $( "#modal_window_confirm" ).append('<p>Are you really want to send order to provider?</p>');

    $( "#modal_window_confirm" ).dialog({
        resizable: false,
        height:250,
        modal: true,
        title: 'Please confirm',
        buttons: {
          "Continue": function() {
            $( this ).dialog( "close" );
            window.location.href = a.getAttribute("href");
          },
          Cancel: function() {
            $( this ).dialog( "close" );
            $( "#modal_window_confirm" ).empty();
            return false;
          }
        }
      });

      return false;
}

$(function() {
    /* For zebra striping */
    $("table tr:nth-child(odd)").addClass("odd-row");
    /* For cell text alignment */
    $("table td:first-child, table th:first-child").addClass("first");
    /* For removing the last border */
    $("table td:last-child, table th:last-child").addClass("last");
    
    if(Amazoni.order_by)
    {
        if(Amazoni.order_option == "DESC")
        {
            $('#order_by_'+Amazoni.order_by).append('<span style="font-size:13pt">&#8679;</span>');
        }
        else
        {
            $('#order_by_'+Amazoni.order_by).append('<span style="font-size:13pt">&#8681;</span>');
        }
    }
});

//Set all date pickers to have Spanish text.
/* Inicialización en español para la extensión 'UI date picker' para jQuery. */
/* Traducido por Vester (xvester@gmail.com). */
jQuery(function($){
        $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: '&#x3c;Ant',
                nextText: 'Sig&#x3e;',
                currentText: 'Hoy',
                monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                'Jul','Ago','Sep','Oct','Nov','Dic'],
                dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
                dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
                dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['es']);
});

function edit (id) {    
    $.id_edit = id;
    
    if ($( "#dialog-modal" ).length === 0)
    {
        $('body').append('<div id="dialog-modal"></div>');
    }
    
    $( "#dialog-modal" ).empty();
    $( "#dialog-modal" ).append('<div id="ajax-loader"></div>');
    $( "#dialog-modal" ).append('<div id="success-icon"></div>');
    $( "#dialog-modal" ).append('<div id="error-icon"></div>');
    
    $( "#dialog-modal" ).dialog({
    minHeight: 400,    
    minWidth: 1200,
    modal: true,
    title: 'Edit order id '+ id,
    close: function( event, ui ) {
        $( "#dialog-modal" ).empty();
    },
    open: function(event, ui) { 
    
        $("#ajax-loader").css('display', 'block');
        
        $.ajax({
            type: "POST",
            url: url_before_index + "index.php/dashboard/edit/" + id
          }).success(function( msg ) {
            $( "#dialog-modal" ).append(msg);
            $("#ajax-loader").css('display', 'none');
            $("#edit-close").click(function() {
                $('#dialog-modal').dialog('close');
            });
            
            $('body').on('submit', "#edit-form", function (event) {
                // variable to hold request
                var request;
                // bind to the submit event of our form
                
                    // abort any pending request
                    if (request) {
                        request.abort();
                    }

                    // check email
                    if (!IsEmail($("#correo").val()) && $("#correo").val()) {
                        $("#correo").focus().addClass('wrong');
                        return false;
                    }
                    $("#correo").removeClass('wrong');

                    // setup some local variables
                    var $form = $(this);
                    // let's select and cache all the fields
                    var $inputs = $form.find("input, select, button, textarea");
                    // serialize the data in the form
                    var serializedData = $form.serialize();
                    
                    // let's disable the inputs for the duration of the ajax request
                    $inputs.prop("disabled", true);
                    $('.edit-ajax-container').hide();
                    $("#ajax-loader").css('display', 'block');
                    
                    // fire off the request to /form.php
                    request = $.ajax({
                        url: url_before_index + "index.php/dashboard/save",
                        type: "post",
                        data: serializedData
                    });

                    // callback handler that will be called on success
                    request.done(function (response, textStatus, jqXHR){
                        $('.edit-ajax-container').remove();
                        $("#ajax-loader").css('display', 'none');
                        if (response == 1) {
                            $("#success-icon").fadeIn();
                            $("#ajax-msg").append(null);// clear message container
                            update_table_row($.id_edit);
                        } else {
                            $( "#dialog-modal" ).append(response);
                            $("#ajax-msg").fadeIn();
                            $("#edit-close").click(function() {
                                $('#dialog-modal').dialog('close');
                            });
                            //console.log(response);
                        }
                    });

                    // callback handler that will be called on failure
                    request.fail(function (jqXHR, textStatus, errorThrown){
                        $('.edit-ajax-container').remove();
                        $("#ajax-loader").css('display', 'none');
                        console.log(textStatus + ': '+ errorThrown);
                        $("#error-icon").fadeIn();
                        // log the error to the console
    //                    console.error(
    //                        "The following error occured: "+
    //                        textStatus, errorThrown
    //                    );
                    });

                    // callback handler that will be called regardless
                    // if the request failed or succeeded
                    request.always(function () {
                        // reenable the inputs
                        $inputs.prop("disabled", false);
                    });

                    // prevent default posting of form
                    event.preventDefault();
                    // it will stop the event from bubbling through the DOM of your page
                    // many thanks to http://stackoverflow.com/questions/12052132/jquery-mobile-click-event-binding-twice
                    event.stopImmediatePropagation();
                
            });
            
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#dialog-modal" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
            $("#error-icon").fadeIn();
          });
    }
    });
}

function update_table_row(id)
{
    $.ajaxSetup({ cache: false });
    $.getJSON(url_before_index + "index.php/dashboard/get_order/" + id, function(data) {
        $.each(data, function(key, val) {
            if(key && val)
            {
                $('tr#'+id+' td.'+key).html(val);
            }
            if(key == 'tracking')
            {
                $('tr#'+id+' td.'+key).html(val);
            }
            if(key == 'procesado')
            {
                $('tr#'+id+' td.'+key).removeClass().addClass('procesado').addClass(val.toLowerCase());
                
                if(val.indexOf('ENVIADO_')+1 > 0)
                {
                    $('tr#'+id+' td.'+key).removeClass().addClass('procesado enviado');
                }
                if(val.indexOf('PREPARACION_')+1 > 0)
                {
                    $('tr#'+id+' td.'+key).removeClass().addClass('procesado preparacion');
                }
            }
            if(key == 'ingresos')
            {
                $('tr#'+id+' td.'+key).html(parseFloat(val).formatMoney(2, '.', ',') +'€');
            }
            if(key == 'gasto')
            {
                $('tr#'+id+' td.'+key).html(parseFloat(val).formatMoney(2, '.', ',') +'€');
            }
            if(key == 'comentarios')
            {
                if(val)
                {
                    $('tr#'+id+' td.'+key).html('<a href="#" onclick="open_modal_with_content(this.getAttribute(\'title\'));return false;" class="comment" title="'+val+'"></a>');
                }
            }
        });
      });
}

function update_row(url,id)
{
    $.ajaxSetup({ cache: false });
    $.getJSON(url + id, function(data) {
        $.each(data, function(key, val) {
            if(key && val)
            {
                $('tr#'+id+' td.'+key).html(val);
            }
            if(key == 'tracking')
            {
                $('tr#'+id+' td.'+key).html(val);
            }
            if(key == 'procesado')
            {
                $('tr#'+id+' td.'+key).removeClass().addClass('procesado').addClass(val.toLowerCase());
                
                if(val.indexOf('ENVIADO_')+1 > 0)
                {
                    $('tr#'+id+' td.'+key).removeClass().addClass('procesado enviado');
                }
                if(val.indexOf('PREPARACION_')+1 > 0)
                {
                    $('tr#'+id+' td.'+key).removeClass().addClass('procesado preparacion');
                }
            }
            if(key == 'ingresos')
            {
                $('tr#'+id+' td.'+key).html(parseFloat(val).formatMoney(2, '.', ',') +'€');
            }
            if(key == 'gasto')
            {
                $('tr#'+id+' td.'+key).html(parseFloat(val).formatMoney(2, '.', ',') +'€');
            }
        });
      });
}


function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}

(function( $ ) {
    $.widget( "custom.combobox", {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function() {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          .addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function() {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function() {
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item ) {
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function() {
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
        });
 
        // Found a match, nothing to do
        if ( valid ) {
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.data( "ui-autocomplete" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );
 
  $(function() {
    $( "#combobox" ).combobox();
    $( "#combobox2" ).combobox();
    $( "#combobox3" ).combobox();
    $( "#combobox4" ).combobox();
    $( "#toggle" ).click(function() {
      $( "#combobox" ).toggle();
    });
  });
  
  // Auto send form from pagination link click
  $(function() {
    $('div.pagination').each(function(i) {
        $(this).children('a').attr('onclick', '$(\'form\').attr(\'action\', $(this).attr(\'href\'));$(\'form\').submit();return false;');
    });
  });
  
  $(function() {
      if($('#search').length)
          {
              var search = $('#search').val();
    
                if (search !== '') {
                    var table = $('table');

                    table.find('tr').each(function(index, row) {

                        var allCells = $(row).find('td');

                        if(allCells.length > 0) {
                            var found = false;

                            allCells.each(function(index, td) {

                                var regExp = new RegExp(search,'i');

                                if (regExp.test($(td).text())) {
            //                        console.log($(td).text());
                                    $(td).html(function(index, oldHTML) {
                                        return oldHTML.replace(new RegExp(search,'i'), '<b class="pulsar" style="background-color:#ffff00;">$&</b>');
                                    });
                                }
                            });
                        }
                    });    
                }
                var selectedEffect = 'pulsate';

                setTimeout(function() {
                    $('b.pulsar').hide();
                    $('b.pulsar').show( selectedEffect, 1000);
                  }, 100 );

    
          }
    
  });
  
  function showOrders (email) {
    $( "#dialog-orders" ).empty();  
    $( "#dialog-orders" ).dialog({
    minHeight: 200,    
    minWidth: 400,
    modal: true,
    title: 'Orders from ' + email,
    close: function( event, ui ) {
        
    },
    open: function(event, ui) { 
        
        $( "#dialog-orders" ).append('<div id="ajax-loader" style="display: none;"></div>');
        $("#ajax-loader").css('display', 'block');
        
        $.ajax({
            type: "POST",
            url: url_before_index + "index.php/recurrent/orders/",
            data: {'email':email}
          }).success(function( msg ) {
            $("#dialog-orders").append(msg);
            $("#ajax-loader").css('display', 'none');
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#dialog-orders" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
          });
    }
    });
  }

$(function() {
    $('#incomes_back').click(function() {
        window.location= url_before_index + 'index.php/incomes/';
    });
    
    $('#tracking_back').click(function() {
        window.location= url_before_index + 'index.php/tracking/';
    });
    
});

function AJAX_add(url) {
    
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }
    
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
    minHeight: 350,    
    minWidth: 400,
    width: 800,
    modal: true,
    title: 'Add',
    resizable: true,
    buttons: null,
    close: function( event, ui ) {
        $( "#modal_window" ).empty();
    },
    open: function(event, ui) { 
        
        $( "#modal_window" ).append('<div id="ajax-loader" style="display: block;"></div>');
        $("#ajax-loader").css('display', 'block');
        
        $.ajax({
            type: "POST",
            url: url,
            data: {'task':'add'}
          }).success(function( msg ) {
            $( "#modal_window" ).append(msg);
            $("#ajax-loader").css('display', 'none');
            $('#edit-close').click(function() {
                $( "#modal_window" ).dialog("close");
            });
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
          });
    }

    });
    
}

function AJAX_delete(url, id) {
    
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }

    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
      resizable: false,
      modal: true,
      title: 'Remove confirmation',
      buttons: {
        "Delete": function() {
          $( "#modal_window" ).empty();  
          $( this ).append('<div id="ajax-loader" style="display: none;"></div>');
          $("#ajax-loader").css('display', 'block');
          $.ajax({
            type: "POST",
            url: url,
            data: {task:'delete', id:id}
          }).success(function( msg ) {
            if (msg === '1') {
                location.reload();
            } else {
                $( "#modal_window" ).empty();
                $("#ajax-loader").css('display', 'none');
                $( "#modal_window" ).append(msg); 
            }
            
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
          });  
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      },
      open: function(event, ui) { 
          $( this ).append('<p>This will be permanently deleted and cannot be recovered. Are you sure?</p>');
      } 
    });

}

function AJAX_edit(url,id,sku){
  
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }
    
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
    minHeight: 300,    
    minWidth: 400,
    width: 800,
    modal: true,
    title: 'Edit '+sku,
    resizable: true,
    buttons: null,
    close: function( event, ui ) {
        $( "#modal_window" ).empty();
        $( "#modal_window" ).dialog( "destroy" );
    },
    open: function(event, ui) { 

        $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
        $("#ajax-loader").css('display', 'block');
        
        $.ajax({
            type: "POST",
            url: url,
            data: {task:'edit', id:id}
          }).success(function( msg ) {
            $( "#modal_window" ).append(msg);
            $("#ajax-loader").css('display', 'none');
            $("#edit-close").click(function() {
                $('#modal_window').dialog('close');
            });
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
          });
    }

    });
    
}

function open_modal_with_content(content){
    if ($( "#modal_window" ).length === 0)
        {
            $('body').append('<div id="modal_window"></div>');
        }
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
        minHeight: 300,    
        minWidth: 400,
        modal: true,
        title: 'Details',
        resizable: true,
        buttons: null,
        close: function( event, ui ) {
        },
        open: function(event, ui) { 

            $( "#modal_window" ).append(content);
            $("#ajax-loader").css('display', 'block');

        }

    });
}

function show_top_sales_details(sku)
{
    if ($( "#modal_window" ).length === 0)
    {
        $('body').append('<div id="modal_window"></div>');
    }
    
    var url = url_before_index + 'index.php/incomes/top_sales_product_details/' + sku;
    
    $( "#modal_window" ).empty();
    $( "#modal_window" ).dialog({
    minHeight: 300,    
    minWidth: 400,
    width: 800,
    modal: true,
    title: 'Product top sales details',
    resizable: true,
    buttons: null,
    close: function( event, ui ) {
        $( "#modal_window" ).empty();
    },
    open: function(event, ui) { 

        $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
        $("#ajax-loader").css('display', 'block');
        
        $.ajax({
            type: "POST",
            url: url,
            data: $('form').serializeArray()
          }).success(function( msg ) {
            $( "#modal_window" ).append(msg);
            $("#ajax-loader").css('display', 'none');
          }).error(function( jqXHR, textStatus, errorThrown ) {
            $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
            $("#ajax-loader").css('display', 'none');
          });
    }

    });
}

$(function() {
    $('body').on('submit', "#tracking_form_1", function (event) {
    
        var pedido = $('#pedido').val();
        $.pedido = $('#pedido').val();
        
        if(!pedido)
        {
            $('#pedido').addClass('wrong');
            $('#pedido').focus();
            return false;
        } 
        else 
        {
            $('#pedido').removeClass('wrong');
        }
        
        $( "#modal_window" ).empty();
        $( "#modal_window" ).dialog({
        minHeight: 300,    
        minWidth: 700,
        modal: true,
        title: 'Tracking',
        resizable: true,
        buttons: null,
        close: function( event, ui ) {
        },
        open: function(event, ui) { 

            $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
            $("#ajax-loader").css('display', 'block');

            $.ajax({
                    type: "POST",
                    url: url_before_index + "index.php/tracking/get_order/" + pedido, 
                  }).success(function( msg ) {
                    $( "#modal_window" ).append(msg);
                    $("#ajax-loader").css('display', 'none');

                    $('body').on('submit', "#tracking_order_form", function (event) {

                        // setup some local variables
                        var $form = $('#tracking_order_form');
                        // let's select and cache all the fields
                        var $inputs = $form.find("input, select, button, textarea");
                        // serialize the data in the form
                        var serializedData = $form.serialize();
                            
                        $( "#modal_window" ).empty();
                        $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
                        $( "#modal_window" ).append('<div id="success-icon" style="display: none;"></div>');
                        $("#ajax-loader").css('display', 'block');
                        
                        $.ajax({
                                    type: "POST",
                                    url: url_before_index + "index.php/tracking/save_tracking", 
                                    data: serializedData
                               }).success(function( msg ) {
                                    if(msg == '1')
                                    {
                                        $("#ajax-loader").css('display', 'none');
                                        $("#success-icon").fadeIn();
                                    }
                                    else
                                    {
                                        $("#ajax-loader").css('display', 'none');
                                        $( "#modal_window" ).append(msg);
                                    }
                               }).error(function( jqXHR, textStatus, errorThrown ) {
                                    $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
                                    $("#ajax-loader").css('display', 'none');
                               });
                               
                    event.preventDefault();
                    
                    // it will stop the event from bubbling through the DOM of your page
                    // many thanks to http://stackoverflow.com/questions/12052132/jquery-mobile-click-event-binding-twice
                    event.stopImmediatePropagation();
                });
              }).error(function( jqXHR, textStatus, errorThrown ) {
                $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
                $("#ajax-loader").css('display', 'none');
              });
        }

        });
        
        event.preventDefault();
    
    });
    
});

$(function() {
        $('body').on('submit', "#create_order_form", function (event) {

            // setup some local variables
            var $form = $('#create_order_form');
            // let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $form.serialize();

            $( "#modal_window" ).empty();
            $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
            $( "#modal_window" ).append('<div id="success-icon" style="display: none;"></div>');
            $("#ajax-loader").css('display', 'block');

            $.ajax({
                        type: "POST",
                        url: url_before_index + "index.php/dashboard/save", 
                        data: serializedData
                   }).success(function( msg ) {
                        if(msg == '1')
                        {
                            $("#ajax-loader").css('display', 'none');
                            $("#success-icon").fadeIn();
                        }
                        else
                        {
                            $("#ajax-loader").css('display', 'none');
                            $( "#modal_window" ).append(msg);
                        }
                   }).error(function( jqXHR, textStatus, errorThrown ) {
                        $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
                        $("#ajax-loader").css('display', 'none');
                   });

        event.preventDefault();

        // it will stop the event from bubbling through the DOM of your page
        // many thanks to http://stackoverflow.com/questions/12052132/jquery-mobile-click-event-binding-twice
        event.stopImmediatePropagation();
    });
});


Amazoni.confirm = function(a)
{
    if ($( "#modal_window_confirm" ).length === 0)
        {
            $('body').append('<div id="modal_window_confirm"></div>');
        }
    
    $( "#modal_window_confirm" ).empty();

    $( "#modal_window_confirm" ).append('<p>This operation can remove the temporal data from the DB.</p><br><p>Are you sure?</p>');

    $( "#modal_window_confirm" ).dialog({
        resizable: false,
        height:250,
        modal: true,
        title: 'Please confirm',
        buttons: {
          "Continue": function() {
            $( this ).dialog( "close" );
            window.location.href = a.getAttribute("href");
          },
          Cancel: function() {
            $( this ).dialog( "close" );
            $( "#modal_window_confirm" ).empty();
            return false;
          }
        }
      });

      return false;
}

$(function() {
        $('body').on('submit', "#stokoni_add_product", function (event) {

            // setup some local variables
            var $form = $('#stokoni_add_product');
            // let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $form.serialize();

            $( "#modal_window" ).empty();
            $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
            $( "#modal_window" ).append('<div id="success-icon" style="display: none;"></div>');
            $("#ajax-loader").css('display', 'block');

            $.ajax({
                        type: "POST",
                        url: $form.attr('action'), 
                        data: serializedData
                   }).success(function( msg ) {
                        if(msg == '1')
                        {
                            update_row(url_before_index + "index.php/stokoni/get_product/", $form.find('input[name="id"]').val());
                            $("#ajax-loader").css('display', 'none');
                            $("#success-icon").fadeIn();
                        }
                        else
                        {
                            $("#ajax-loader").css('display', 'none');
                            $( "#modal_window" ).append(msg);
                        }
                   }).error(function( jqXHR, textStatus, errorThrown ) {
                        $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
                        $("#ajax-loader").css('display', 'none');
                   });

        event.preventDefault();

        // it will stop the event from bubbling through the DOM of your page
        // many thanks to http://stackoverflow.com/questions/12052132/jquery-mobile-click-event-binding-twice
        event.stopImmediatePropagation();
    });
});

$(function() {
        $('body').on('submit', "#add_rule_form", function (event) {

            // setup some local variables
            var $form = $('#add_rule_form');
            // let's select and cache all the fields
            var $inputs = $form.find("input, select, button, textarea");
            // serialize the data in the form
            var serializedData = $form.serialize();

            $( "#modal_window" ).empty();
            $( "#modal_window" ).append('<div id="ajax-loader" style="display: none;"></div>');
            $( "#modal_window" ).append('<div id="success-icon" style="display: none;"></div>');
            $("#ajax-loader").css('display', 'block');

            $.ajax({
                        type: "POST",
                        url: $form.attr('action'), 
                        data: serializedData
                   }).success(function( msg ) {
                        if(msg == '1')
                        {
                            update_row(url_before_index + "index.php/amazon/get_price_rule/", $form.find('input[name="id"]').val());
                            $("#ajax-loader").css('display', 'none');
                            $("#success-icon").fadeIn();
                        }
                        else
                        {
                            $("#ajax-loader").css('display', 'none');
                            $( "#modal_window" ).append(msg);
                        }
                   }).error(function( jqXHR, textStatus, errorThrown ) {
                        $( "#modal_window" ).append(textStatus + ':  ' + errorThrown);
                        $("#ajax-loader").css('display', 'none');
                   });

        event.preventDefault();

        // it will stop the event from bubbling through the DOM of your page
        // many thanks to http://stackoverflow.com/questions/12052132/jquery-mobile-click-event-binding-twice
        event.stopImmediatePropagation();
    });
});




$(function() {
    
   //Main menu effects
   
    $('body header').on('click', 'li', function(e){
        
        var ul = $(e.currentTarget).children('ul');
        var options = {};
        ul.toggle( 'slide', options, 100 );
            
    });
    
    // The same sku rows effect
    
    $('table tr').on('mouseover', function(){
       
        var element_sku = $(this).attr('data-sku');
        
        if($("tr[data-sku='"+element_sku+"']" ).length > 1)
        {
            $("tr[data-sku='"+element_sku+"']" ).addClass('markered');

            $('table tr').on('mouseleave', function(){

                $("tr[data-sku='"+element_sku+"']" ).removeClass('markered');

            });
        }
    });
});