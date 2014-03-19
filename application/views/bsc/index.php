<article>
    <h1><?php echo $title;?></h1>
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/incomes/top_sales';?>">
                <img src="<?php echo base_url().'assets/imgs/Misc-Stats-icon.png';?>" alt="Top sales" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/incomes/top_sales';?>">
                <span>Top sales</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/bsc/overview';?>">
                <img src="<?php echo base_url().'assets/imgs/Misc-Stats-icon.png';?>" alt="BSC" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/bsc/overview';?>">
                <span>BSC</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/bsc/ssa';?>">
                <img src="<?php echo base_url().'assets/imgs/Misc-Stats-icon.png';?>" alt="SSA" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/bsc/ssa';?>">
                <span>SSA</span>
            </a>
        </div>
    </div>
</article>