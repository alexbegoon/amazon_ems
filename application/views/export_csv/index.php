<article>
    <h1><?php echo $title;?></h1>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo isset($button_link) ? $button_link : base_url().'index.php/export_csv/fedex_gls';?>">
                <img src="<?php echo base_url().'assets/imgs/Button-Download-icon.png';?>" alt="Download Fedex/GLS" />
            </a>
            <a class="menu-item" href="<?php echo isset($button_link) ? $button_link : base_url().'index.php/export_csv/fedex_gls';?>">
                <span><?php echo isset($button_name) ? $button_name : 'Download Fedex/GLS';?></span>
            </a>
        </div>
    </div>
</article>