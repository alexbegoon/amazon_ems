<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form helper
 * 
 * @author Alexander Begoon
 */


if ( ! function_exists('getStatusOptions'))
{
	function getStatusOptions($selected = null)
	{
		// Statuses
                $statuses = array('PREPARADO','NO','SI','INCIDENCIA','PREPARACION_ENGELSA_GLS','PREPARACION_ENGELSA_FEDEX','PEDIDO_ENGELSA_GLS','PEDIDO_ENGELSA_FEDEX','ENVIADO_GLS','ENVIADO_FEDEX','PEDIDO_ENGELSA_GLS_AQUI','PEDIDO_ENGELSA_FEDEX_AQUI','ROTURASTOCK','CANCELADO','ENVIADO_GRUTINET','ENALMACEN','PROCESADO_MEGASUR','ENVIADO_MEGASUR','PEDIDO_MARABE','ENVIADO_MARABE','MULTIPRODUCTO','PAGADO','PEDIDO_MARABE_AQUI','PAYPAL','PEDIDO_ENGELSA_PACK','PREPARACION_ENGELSA_PACK','PEDIDO_ENGELSA_PACK_AQUI','ENVIADO_PACK','PREPARACION_ENGELSA_TOURLINE','PREPARACION_ENGELSA_TOURLINE_AQUI','PEDIDO_ENGELSA_TOURLINE','ENVIADO_TOURLINE','PREPARACION_FARMA_TOURLINE','PREPARACION_FARMA_TOURLINE_AQUI','PEDIDO_FARMA_TOURLINE','PTE_PAGO','PEDIDO_ENGELSA_TOURLINE_AQUI');
                asort($statuses);
                
                echo '<option value=""></option>';
                
                foreach($statuses as $option) {
                    if ((string)$selected == (string)$option) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    }    
                    echo '<option value="'.$option.'" '.$attr.'>'.$option.'</option>';
                }
                    
	}
}

if ( ! function_exists('getWebOptions'))
{
	function getWebOptions($selected = null)
	{
		// Available Web
                $web = array('COSMETIQUES', 'AMAZON', 'COSMETICAONLINE', 'KOSMETIK', 'PRIXPARFUM', 
                              'COSMETICS', 'AMAZON-USA', 'SEXSHOPIN', 'BUYIN', 'Amazon-JP' );
                asort($web);
                
                echo '<option value=""></option>';
                
                foreach($web as $option) {
                    if ((string)$selected == (string)$option) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    }    
                    echo '<option value="'.$option.'" '.$attr.'>'.$option.'</option>';
                }
                    
	}
}

if ( ! function_exists('getMonthsOptions'))
{
	function getMonthsOptions($selected)
	{
                $html = '';
		// Months Array
                $months = array(    '01' => 'January',
                                    '02' => 'February',	
                                    '03' => 'March',
                                    '04' => 'April',
                                    '05' => 'May',
                                    '06' => 'June',
                                    '07' => 'July',
                                    '08' => 'August',
                                    '09' => 'September',
                                    '10' => 'October',
                                    '11' => 'November',
                                    '12' => 'December'
                                );
                                
                foreach ($months as $num => $name) {
                    if ((int)$selected === (int)$num) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    } 
                    
                    $html .= sprintf('<option value="%s" %s>%s</option>', $num, $attr, $name);
                }   
                
                return $html;
	}
}

if ( ! function_exists('getYearsOptions'))
{
	function getYearsOptions($selected)
	{
                $html = '';
                
		// Years Array
                $years = array();
                
                // Current year
                $year_current = date('Y', time());
                
                // Start from year ????
                $start_from = 2010;
                
                for ($i = $start_from; $i <= $year_current; $i++) {
                    $years[$i] = $i;
                }
                                
                foreach ($years as $num => $name) {
                    if ((int)$selected === (int)$num) {
                        $attr = 'selected="selected"';
                    } else {
                        $attr = '';
                    } 
                    
                    $html .= sprintf('<option value="%u" %s>%u</option>', $num, $attr, $name);
                }   
                
                return $html;
	}
}

if ( ! function_exists('get_country_flag_img'))
{
	function get_country_flag_img($country_code)
	{
                $html = '';
                
                $filename = 'assets/imgs/small_flags/' . strtolower($country_code) . '.png';
                
                $image_properties = array(
                    'src' => $filename,
                    'alt' => $country_code,
                    'width' => '20',
                    'height' => '20'
                );
                
                $html = img($image_properties);
                
                return $html;
	}
}

if ( ! function_exists('get_virtuemart_versions'))
{
	function get_virtuemart_versions($selected = null)
	{
            $versions = array(
                
                ''=>'',
                '1.0.0.0'=>'1.0.0.0',
                '2.0.0.0'=>'2.0.0.0'
                
            );
            
            return form_dropdown('virtuemart_version', $versions, $selected, 'id="virtuemart_versions_list"');
        }
}

if ( ! function_exists('get_rating_stars'))
{
	function get_rating_stars($rating)
        {
            $rating = (int)floor($rating);
            
            $filename = 'assets/imgs/stars/' . $rating . '.png';
            
            $image_properties = array(
                    'src' => $filename,
                    'alt' => 'Rating '.$rating.'/5',
                    'title' => 'Rating '.$rating.'/5',
                    'width' => '68',
                    'height' => '12'
                );
            
            return img($image_properties);
        }
}
