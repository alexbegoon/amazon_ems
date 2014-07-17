<?php header("Content-Type: text/html; charset=UTF-8"); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
  <link href="<?php echo base_url().'assets/css/main.css'; ?>" rel="stylesheet" type="text/css" media="screen" />
  <?php if ($this->ion_auth->logged_in()): ?>
  <link href="<?php echo base_url().'assets/css/print.css';?>" rel="stylesheet" type="text/css" media="print">
  <link href="<?php echo base_url().'assets/css/custom-theme/jquery-ui-1.10.3.custom.css'; ?>" rel="stylesheet" type="text/css" />
  <?php endif;?>
  <link rel="shortcut icon" href="<?php echo base_url().'assets/imgs/favicon.ico'; ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo base_url().'assets/imgs/favicon.ico'; ?>" type="image/x-icon">
  <?php if ($this->ion_auth->logged_in()): ?>
  <script>
    var url_before_index = '<?php echo base_url();?>';
  </script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-1.11.0.min.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-ui-1.10.4.min.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/global.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/Chart.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/tinymce/js/tinymce/tinymce.min.js'; ?>"></script>
  
  <script>
  <?php
        $order_by_data = get_order_by_info();
  ?>
  Amazoni.order_by        =  '<?php echo $order_by_data['order_by'];?>';
  Amazoni.order_option    =  '<?php echo $order_by_data['order_option'];?>';
  </script>
  <?php endif;?>
  
  <title><?php echo $title;?></title>
</head>
<body>
<?php
    if ($this->ion_auth->logged_in())
    {
        if ($this->ion_auth->is_admin()) 
        {
            //Admin VIEW
            ?>
            <header>
                <h1>
                    <span>
                        AMAZONI 4
                    </span>
                </h1>
                <ul>
                    <li>
                        <a href="<?php echo base_url().'index.php/dashboard/page';?>">Dashboard</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/recurrent/page';?>">Recurrent buyers</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/incomes/page';?>">Incomes</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/stokoni/page';?>">Stokoni</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/';?>">Amazon</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/auth';?>">User Manager</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/tracking/';?>">Trackings</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/auth/logout';?>">Logout</a>
                    </li>
                    <li class="generar_menu">
                        <span>Generar &#8711;</span>
                        <ul class="dropdown generar_menu">
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_gls_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar GLS</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_fedex_summary'; ?>" onclick="Amazoni.confirm(this);return false;">Generar FEDEX</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_pack_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar PACK</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_tourline_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar TOURLINE</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_stokoni_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar Stokoni Summary</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_new_products_coqueteo_summary';?>">Coqueteo New Products Report</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/products/page/';?>">Products</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/magnet/';?>">Magnet</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/reviews/';?>">Reviews</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/log/';?>">Amazon Log</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/price_rules/';?>">Price Rules</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/providers/orders/';?>">Provider Orders</a>
                    </li>
                    <li class="generar_menu">
                        <span>Exportar Preparacion &#8711;</span>
                        <ul class="dropdown generar_menu">
                            <li>
                                <a href="<?php echo base_url().'index.php/sync_process/update_stock';?>" >Update Stock</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/sync_process/verify_products';?>" >Verify Products</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/dashboard/roturastock_report/';?>" >RoturaStock Report</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/export_engelsa_summary';?>" onclick="Amazoni.confirm(this);return false;">Exportar Preparacion Engelsa</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/export_pinternacional_summary';?>" onclick="Amazoni.confirm(this);return false;">Exportar Preparacion Pinternacional</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/export_coqueteo_summary';?>" onclick="Amazoni.confirm(this);return false;">Exportar Preparacion Coqueteo</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/export_psellectiva_summary';?>" onclick="Amazoni.confirm(this);return false;">Exportar Preparacion Psellectiva</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/export_warehouse_summary';?>" onclick="Amazoni.confirm(this);return false;">Exportar Preparacion Stokoni</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/providers/orders/';?>" >Provider Order Report</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/dashboard/order_modifications/';?>" >Order Modifications</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="welcome-user">
                    <span>
                        Welcome, <?php echo $this->ion_auth->user()->row()->first_name; ?>
                    </span>
                </div>
            </header>
            <div id="header_wrapper"></div>
    
            <?php
        } else {
            //Normal user VIEW
            ?>
            <header>
                <h1>
                    <span>
                        AMAZONI 4
                    </span>
                </h1>
                <ul>
                    <li>
                        <a href="<?php echo base_url().'index.php/dashboard/page';?>">Dashboard</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/recurrent/page';?>">Recurrent buyers</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/stokoni/page';?>">Stokoni</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/';?>">Amazon</a>
                    </li>
                    <li class="generar_menu">
                        <span>Generar &#8711;</span>
                        <ul class="dropdown generar_menu">
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_gls_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar GLS</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_fedex_summary'; ?>" onclick="Amazoni.confirm(this);return false;">Generar FEDEX</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_pack_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar PACK</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_tourline_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar TOURLINE</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_stokoni_summary';?>" onclick="Amazoni.confirm(this);return false;">Generar Stokoni Summary</a>
                            </li>
                            <li>
                                <a href="<?php echo base_url().'index.php/export_csv/generar_new_products_coqueteo_summary';?>">Coqueteo New Products Report</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/tracking/';?>">Trackings</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/products/page/';?>">Products</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/magnet/';?>">Magnet</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/auth/logout';?>">Logout</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/reviews/';?>">Reviews</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/log/';?>">Amazon Log</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/price_rules/';?>">Price Rules</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/providers/orders/';?>">Provider Orders</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/dashboard/roturastock_report/';?>" >RoturaStock Report</a>
                    </li>
                </ul>
                <div class="welcome-user">
                    <span>
                        Welcome, <?php echo $this->ion_auth->user()->row()->first_name; ?>
                    </span>
                </div>
            </header>
            <div id="header_wrapper"></div>
            <?php
        }
    }
?>    