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
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/top_sales';?>">
                <img src="<?php echo base_url().'assets/imgs/Misc-Stats-icon.png';?>" alt="Proveedores" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/top_sales';?>">
                <span>Top sales</span>
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
        <?php $filter = $this->input->post("filter");?>
        <div class="filters">
            <div class="ui-widget">
                <h3>Current month: <?php echo $current_month;?></h3>
                <label for="combobox">Select Month: </label>
                <select id="combobox" name="filter[month]">
                    <?php echo $month_options; ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="combobox2">Select Year: </label>
                <select id="combobox2" name="filter[year]">
                    <?php echo $year_options; ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <label for="combobox3">Incomes Summary: </label>
                <select id="combobox3" name="filter[incomes_summary_year]">
                    <?php echo $incomes_summary_year_options; ?>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="Process" />
            </div>
        </div>
        <div class="incomes_wrapper">
            <div class="incomes_summary">
                <table class="thin_table">
                    <?php if (!empty($summary)) { ?>
                    <?php 
                    $total_orders   = 0;
                    $total_ingresos = 0;
                    $total_gasto    = 0;
                    $total_profit   = 0;
                    ?>
                    <tr>
                        <th>Web</th>
                        <th>Orders Shipped</th>
                        <th>Ingreso</th>
                        <th>Gasto</th>
                        <th>Profit</th>
                    </tr>
                        <?php foreach ($summary as $web) { ?>
                    <tr>
                        <td><?php echo $web->web ?></td>
                        <td><?php echo $web->total_orders ?></td>
                        <td class="ingreso"><?php echo $web->ingresos ?>&euro;</td>
                        <td class="gasto"><?php echo $web->gasto ?>&euro;</td>
                        <td><?php echo $web->profit; ?>&euro;</td>
                    </tr>
                        <?php 
                        $total_orders += $web->total_orders;
                        $total_ingresos += $web->ingresos;
                        $total_gasto += $web->gasto;
                        $total_profit += $web->profit;
                        ?>
                        <?php } ?>
                    <tr>
                        <td colspan="5"></td>
                    </tr>
                    <tr class="total">
                        <td>Total:</td>
                        <td><?php echo $total_orders;?></td>
                        <td class="ingreso"><?php echo $total_ingresos;?>&euro;</td>
                        <td class="gasto"><?php echo $total_gasto;?>&euro;</td>
                        <td><?php echo $total_profit + $other_costs->sales_rappel - $other_costs->advertisement_cost;?>&euro;</td>
                    </tr>
                    <?php } ?>
                </table>
                <br>
                <div>
                    <table class="thin_table">
                        <tr>
                            <td>Advertisement cost:</td>
                            <td><input type="text" name="advertisement_cost" value="<?php echo $other_costs->advertisement_cost;?>"/></td>
                        </tr>
                        <tr>
                            <td>Sales rappel:</td>
                            <td><input type="text" name="sales_rappel" value="<?php echo $other_costs->sales_rappel;?>"/></td>
                        </tr>
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
    </form>    
</article>
<div id="dialog-modal" title="Basic modal dialog">
    <div id="ajax-loader"></div>
    <div id="success-icon"></div>
    <div id="error-icon"></div>
</div>
<div id="dialog-confirm" title="">
</div>