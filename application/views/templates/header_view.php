<? header("Content-Type: text/html; charset=UTF-8"); ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
  <link href="<?php echo base_url().'assets/css/main.css'; ?>" rel="stylesheet" type="text/css" media="screen" />
  <link href="<?php echo base_url().'assets/css/print.css';?>" rel="stylesheet" type="text/css" media="print">
  <link href="<?php echo base_url().'assets/css/custom-theme/jquery-ui-1.10.3.custom.css'; ?>" rel="stylesheet" type="text/css" />
  <link rel="shortcut icon" href="<?php echo base_url().'assets/imgs/favicon.ico'; ?>" type="image/x-icon">
  <link rel="icon" href="<?php echo base_url().'assets/imgs/favicon.ico'; ?>" type="image/x-icon">
  <script>var url_before_index = '<?php echo base_url();?>'</script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-1.9.1.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/jquery-ui-1.10.3.custom.js'; ?>"></script>
  <script type="text/javascript" src="<?php echo base_url().'assets/js/global.js'; ?>"></script>
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
                        AMAZONI VERSION 4.0
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
                        <a href="<?php echo base_url().'index.php/engelsa/page';?>">Engelsa</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/grutinet/page';?>">Grutinet</a>
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
                        <a href="<?php echo base_url().'index.php/auth/logout';?>">Logout</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/fedex_gls_summary';?>" onclick="confirm(this);return false;">Exportar Preparacion Engelsa</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_gls_summary';?>" onclick="confirm(this);return false;">Generar GLS</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_fedex_summary';?>" onclick="confirm(this);return false;">Generar FEDEX</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_pack_summary';?>" onclick="confirm(this);return false;">Generar PACK</a>
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
                        <a href="<?php echo base_url().'index.php/reviews/';?>">Reviews</a>
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
                        AMAZONI VERSION 4.0
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
                        <a href="<?php echo base_url().'index.php/engelsa/page';?>">Engelsa</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/grutinet/page';?>">Grutinet</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/stokoni/page';?>">Stokoni</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/amazon/';?>">Amazon</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/fedex_gls_summary';?>" onclick="confirm(this);return false;">Exportar Preparacion Engelsa</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/auth/logout';?>">Logout</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_gls_summary';?>" onclick="confirm(this);return false;">Generar GLS</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_fedex_summary'; ?>" onclick="confirm(this);return false;">Generar FEDEX</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'index.php/export_csv/generar_pack_summary';?>" onclick="confirm(this);return false;">Generar PACK</a>
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
                        <a href="<?php echo base_url().'index.php/reviews/';?>">Reviews</a>
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