<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Description of index
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
?>
<article>
    <h1><?php echo $title; ?></h1>
    
    <div class="menu-wrapper">
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/magnet/edit_template/es';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/Spain.png';?>" alt="Edit Spanish Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/magnet/edit_template/es';?>">
                <span>Edit Spanish Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/magnet/edit_template/en';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/United Kingdom(Great Britain).png';?>" alt="Edit English Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/magnet/edit_template/en';?>">
                <span>Edit English Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/magnet/edit_template/de';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/Germany.png';?>" alt="Edit Deutsch Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/magnet/edit_template/de';?>">
                <span>Edit Deutsch Email</span>
            </a>
        </div>
        <div class="menu-item">
            <a class="menu-item-img" href="<?php echo base_url().'index.php/magnet/edit_template/fr';?>">
                <img src="<?php echo base_url().'assets/imgs/flags/France.png';?>" alt="Edit French Email" />
            </a>
            <a class="menu-item" href="<?php echo base_url().'index.php/magnet/edit_template/fr';?>">
                <span>Edit French Email</span>
            </a>
        </div>
    </div>
</article>

