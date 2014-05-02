<article>
    <h1><?php echo $title;?></h1>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/shipping_companies';?>">
                <img src="<?php echo base_url().'assets/imgs/Shipping-4-icon.png';?>" alt="Shipping Companies" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/shipping_companies';?>">
                <span>Shipping Companies</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/shipping_types';?>">
                <img src="<?php echo base_url().'assets/imgs/network-clock-icon.png';?>" alt="Shipping Types" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/shipping_types';?>">
                <span>Shipping Types</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/shipping_costs';?>">
                <img src="<?php echo base_url().'assets/imgs/price-tag-icon.png';?>" alt="Shipping Costs" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/shipping_costs';?>">
                <span>Shipping Costs</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/taxes';?>">
                <img src="<?php echo base_url().'assets/imgs/pay-income-tax-online-02.png';?>" alt="Taxes" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/taxes';?>">
                <span>Taxes</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/exchange_rates';?>">
                <img src="<?php echo base_url().'assets/imgs/dollar-icon.png';?>" alt="Exchange rates" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/exchange_rates';?>">
                <span>Exchange rates</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/providers';?>">
                <img src="<?php echo base_url().'assets/imgs/coal-power-plant-icon.png';?>" alt="Proveedores" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/providers';?>">
                <span>Proveedores</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/bsc';?>">
                <img src="<?php echo base_url().'assets/imgs/presentation-icon.png';?>" alt="BSC" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/bsc';?>">
                <span>BSC</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/web_field';?>">
                <img src="<?php echo base_url().'assets/imgs/web-shop-icon-psd.png';?>" alt="Our shops" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/web_field';?>">
                <span>Our shops</span>
            </a>
        </div>
    </div>
    <form id="incomes-form" method="post" action="<?php echo base_url().'index.php/incomes/page/';?>">
        <?php $date_from = $this->input->post("date_from") ? $this->input->post("date_from") : date('Y-m-01', time());
              $date_to   = $this->input->post("date_to") ? $this->input->post("date_to") : date('Y-m-d', time());
        ?>
        <div class="filters">
            <h3>Current month: <?php echo $current_month;?></h3>
            <label for="date_picker">Date from: </label>
            <input type="text" name="date_from" value="<?php echo $date_from;?>" id="date_picker">
            <label for="date_picker_2">Date to: </label>
            <input type="text" name="date_to" value="<?php echo $date_to;?>" id="date_picker_2">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="submit" value="Process" />
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <label for="to_excel" title="Export filtered data to Excel list">To Excel: </label>
            <button id="to_excel"><span class="excel_icon"></span></button>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="text" value="<?php echo $this->input->post('search');?>" name="search" id="search">
            <input type="submit" value="Buscar">
        </div>
        <div class="incomes_wrapper">
            <div class="incomes_summary">
                <table class="thin_table">
                    <?php if (!empty($summary)) { ?>
                    <?php 
                    $total_orders       = 0;
                    $total_ingresos     = 0;
                    $total_gasto        = 0;
                    $total_profit       = 0;
                    $total_taxes        = 0;
                    $total_net_profit   = 0;
                    $total_percentage   = 0;
                    ?>
                    <tr>
                        <th>Web</th>
                        <th>Orders Shipped</th>
                        <th>Ingreso</th>
                        <th>Gasto</th>
                        <th>Profit</th>
                        <th>Taxes</th>
                        <th>Net Profit</th>
                        <th>Percentage</th>
                    </tr>
                        <?php foreach ($summary as $web) { ?>
                    <tr>
                        <td><?php echo $web->web ?></td>
                        <td><?php echo $web->total_orders ?></td>
                        <td class="ingreso"><?php echo $web->ingresos ?>&euro;</td>
                        <td class="gasto"><?php echo $web->gasto ?>&euro;</td>
                        <td><?php echo $web->profit; ?>&euro;</td>
                        <td><?php echo $web->taxes; ?>&euro;</td>
                        <td><?php echo $web->net_profit; ?>&euro;</td>
                        <td><?php echo $web->percentage; ?>&percnt;</td>
                    </tr>
                        <?php 
                        $total_orders           += $web->total_orders;
                        $total_ingresos         += $web->ingresos;
                        $total_gasto            += $web->gasto;
                        $total_profit           += $web->profit;
                        $total_taxes            += $web->taxes;
                        $total_net_profit       += $web->net_profit;
                        $total_percentage       += $web->percentage;
                        ?>
                        <?php } ?>
                    <tr>
                        <td colspan="8"></td>
                    </tr>
                    <tr class="total">
                        <td>Total:</td>
                        <td><?php echo $total_orders;?></td>
                        <td class="ingreso"><?php echo $total_ingresos;?>&euro;</td>
                        <td class="gasto"><?php echo $total_gasto;?>&euro;</td>
                        <td><?php echo $total_profit;?>&euro;</td>
                        <td><?php echo $total_taxes;?></td>
                        <td><?php echo $total_net_profit;?></td>
                        <input type="hidden" id="total_net_profit_" name="total_net_profit" value="<?php echo $total_net_profit;?>" />
                        <td><?php echo $total_percentage;?></td>
                    </tr>
                    <?php } ?>
                </table>
                <br>
                <div>
                    <table class="thin_table">
                        <?php foreach($other_costs as $cost):?>
                        <tr title="<?php echo $cost->description;?>">
                            <td><?php echo $cost->name;?></td>
                            <td>
                                <input id="<?php echo $cost->code;?>_other" <?php if((int)$cost->read_only === 1){echo 'readonly="true"';}?> type="text" name="<?php echo $cost->code;?>" value="<?php echo $cost->price;?>"/>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </table>
                </div>
            </div>
            <div class="incomes_orders">
                <div>
                    <p><?php echo $total_rows;?> orders found</p>
                </div>
                <?php if (!empty($orders)) { ?>
                <div class="pagination">
                    <?php echo $pagination;?>
                </div>
                <table class="thin_table">
                    <tr>
                        <th>Action</th>
                        <th>Id</th>
                        <th>Pedido</th>
                        <th>Procesado</th>
                        <th>Date</th>
                        <th>Web</th>
                        <th>Ingreso</th>
                        <th>Gasto</th>
                        <th>Profit</th>
                    </tr>
                        <?php foreach ($orders as $order) { ?>
                        <?php 
                        
                            $procesado_class = strtolower($order->procesado);
                        
                            if (strpos($order->procesado, 'ENVIADO_') !== false) {
                                $procesado_class = 'enviado';
                            }
                            
                            if (strpos($order->procesado, 'PREPARACION_') !== false) {
                                $procesado_class = 'preparacion';
                            }
                        
                        ?>
                    <tr id="<?php echo $order->id;?>">
                        <td>
                            <a href="#" class="edit" title="edit" onclick="edit(<?php echo $order->id;?>);return false;"></a>
                        </td>
                        <td><?php echo $order->id ?></td>
                        <td class="bold"><?php echo $order->pedido ?></td>
                        <td class="procesado <?php echo $procesado_class;?>"><?php echo $order->procesado ?></td>
                        <td><?php echo $order->fechaentrada ?></td>
                        <td class="<?php echo strtolower($order->web);?>"><?php echo $order->web ?></td>
                        <td class="ingresos ingreso"><?php echo $order->ingresos ?>&euro;</td>
                        <td class="gasto"><?php echo $order->gasto ?>&euro;</td>
                        <td><?php echo $order->profit; ?>&euro;</td>
                    </tr>  
                        <?php } ?>
                    
                </table>
                <div class="pagination">
                    <?php echo $pagination;?>
                </div>
                <div>
                    <p><?php echo $total_rows;?> orders found</p>
                </div>
                <?php } ?>
            </div>
        </div>
        <input type="hidden" id="excel_toggle" name="to_excel" value="0" />
    </form>    
</article>
<div id="dialog-modal" title="Basic modal dialog">
    <div id="ajax-loader"></div>
    <div id="success-icon"></div>
    <div id="error-icon"></div>
</div>
<div id="dialog-confirm" title="">
</div>
<script>
    $(function(){
        
        $('form input, form a').click(function(){
            $('#excel_toggle').val('0');
        });

        $('#to_excel').click(function(){
            $('#excel_toggle').val('1');
        });

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
            
        // Other costs
        
        Amazoni.paypal_total_fees = <?php echo $paypal_total_fees ? $paypal_total_fees : 0;?>;
        Amazoni.sagepay_total_fees = <?php echo $sagepay_total_fees ? $sagepay_total_fees : 0;?>;
        Amazoni.tpv_total_fees = <?php echo $tpv_total_fees ? $tpv_total_fees : 0;?>;
        
        $('#sagepay_other').val(Amazoni.sagepay_total_fees);
        $('#tpv_other').val(Amazoni.tpv_total_fees);
        $('#paypal_other').val(Amazoni.paypal_total_fees);
        
        
        
        Amazoni.calculator_process = function(){
            
            Amazoni.oper_profit_other = parseFloat($('#total_net_profit_').val());
            
            Amazoni.oper_profit_other -= parseFloat($('#advertisement_other').val());
            Amazoni.oper_profit_other += parseFloat($('#rappel_other').val());
            Amazoni.oper_profit_other -= parseFloat($('#sagepay_other').val());
            Amazoni.oper_profit_other -= parseFloat($('#tpv_other').val());
            Amazoni.oper_profit_other -= parseFloat($('#paypal_other').val());
            Amazoni.oper_profit_other -= parseFloat($('#operating_cost_other').val());
            
            $('#oper_profit_other').val(Amazoni.oper_profit_other.toFixed(2));
        };
        $('.incomes_summary input').change(function(){
            Amazoni.calculator_process();
        });
        Amazoni.calculator_process();
        
    });
</script>