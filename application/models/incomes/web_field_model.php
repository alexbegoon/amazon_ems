<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Web field model
 *
 * @author Alexander.B <alexbassmusic@gmail.com> - https://www.odesk.com/users/~01ae8f6e1a81c189cf
 */
class Web_field_model extends CI_Model
{
    private $_buffer_data = array();
    
    const SECRET_KEY = 'BuyIn-Qd9#lsdd1&2dnsfhsghpwpguhruhgsfklghpgwqrugheiquwgh';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get all shops (WEB field in pedidos)
     * @return mixed Array of objects or boolean false on unsuccess
     */
    public function get_all_web_fields()
    {        
        $query = ' SELECT `web_fields`.* , \'\' as `password`, 
                          CONCAT(`languages`.`language`, \' (\',`languages`.`code`, \')\') as `language`, 
                          `languages`.`code` as `language_code`,  
                          (SELECT group_concat(`providers`.`name` separator \', \') 
                           FROM `'.$this->db->dbprefix('providers').'` as `providers` 
                           INNER JOIN `'.$this->db->dbprefix('web_provider').'` as `web_provider` 
                           ON `providers`.`id` = `web_provider`.`provider_id` 
                           WHERE `web_provider`.`web` = `web_fields`.`web` ) as `providers` 
                   FROM `'.$this->db->dbprefix('web_field').'` as `web_fields` 
                   LEFT JOIN `'.$this->db->dbprefix('languages').'` as `languages` 
                   ON `web_fields`.`template_language` = `languages`.`code` 
                   ORDER BY `web_fields`.`web` 
                 ';
        
        $result = $this->db->query($query);
                
        if($result->num_rows() > 0)
        {
            return $result->result();
        }
        
        return false;
    }
    
    public function get_providers_accordion($web = '')
    {
        $this->load->helper('html');
        
        $html = '';
        
        $query = ' SELECT `providers`.`name`, `providers`.`id`, 
                     CASE 
                        WHEN EXISTS(SELECT `web_provider`.`web` 
                                    FROM `'.$this->db->dbprefix('web_provider').'` AS `web_provider` 
                                    WHERE `web_provider`.`web` = \''.trim($web).'\' 
                                    AND `web_provider`.`provider_id` = `providers`.`id` 
                                    ) 
                        THEN 1
                        ELSE 0
                        END `have_this_provider`, 
                   ( SELECT `web_provider_2`.`sku_regexp` 
                     FROM `'.$this->db->dbprefix('web_provider').'` AS `web_provider_2` 
                     WHERE `web_provider_2`.`web` = \''.trim($web).'\' 
                     AND `web_provider_2`.`provider_id` = `providers`.`id` ) AS `sku_regexp`,
                   ( SELECT `web_provider_3`.`sku_regexp_2` 
                     FROM `'.$this->db->dbprefix('web_provider').'` AS `web_provider_3` 
                     WHERE `web_provider_3`.`web` = \''.trim($web).'\' 
                     AND `web_provider_3`.`provider_id` = `providers`.`id` ) AS `sku_regexp_2` 
                   FROM `'.$this->db->dbprefix('providers').'` AS `providers` 
                   ORDER BY `providers`.`name` ASC
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $rows = $result->result();
            
            foreach ($rows as $row)
            {
                $html .= heading($row->name, 3);
                $html .= '<div>'."\n";
                if($row->have_this_provider == 1)
                {
                    $html .= form_checkbox('provider['.$row->id.']', $row->id, TRUE, 'id="checkbox_provider_'.$row->id.'"')."\n";
                }
                else
                {
                    $html .= form_checkbox('provider['.$row->id.']', $row->id, FALSE, 'id="checkbox_provider_'.$row->id.'"')."\n";
                }
                
                $html .= '<label for="checkbox_provider_'.$row->id.'">set this provider</label>'."\n";
                
                $html .= br(2);
                
                $html .= '<span>Regular expression, that helps to choose this provider, using SKU,EAN and etc.</span>'."\n";
                
                $html .= br();
                
                $html .= '<textarea name="sku_regexp['.$row->id.']" maxlength="255" cols="70">'.stripslashes($row->sku_regexp).'</textarea>'."\n";
                
                $html .= br(2);
                
                $html .= '<span>Regular expression, that helps to restore the format of SKU for this Provider. <br>
                            It may be useful in case when you try to extract name from provider\'s product list. <br>
                            If you are set this RegExp, then preg_replace(RegExp, \'\') will be applied to SKU. </span>'."\n";
                
                $html .= br();
                
                $html .= '<textarea name="sku_regexp_2['.$row->id.']" maxlength="255" cols="70">'.stripslashes($row->sku_regexp_2).'</textarea>'."\n";
                
                $html .= '</div>'."\n";
            }
        }
        
        return $html;
    }
    
    /**
     * Return html of select box for all active languages in the system
     * @param string $selected Selected language
     * @return string
     */
    public function get_language_list($selected = null)
    {
        $html = '';
        
        $query = ' SELECT `language`, `code` 
                   FROM `'.$this->db->dbprefix('languages').'` 
                   WHERE `active` = 1             
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $languages = $result->result();
            
            $options = array();
            
            foreach($languages as $language)
            {
                $options[$language->code] = $language->language . ' ('.$language->code.')';
            }
            
            $html = form_dropdown('language', $options, $selected, 'id="languages_list"');
        }
        
        return $html;
    }
    
    public function add()
    {
        $post_data = $this->input->post();
        
        $this->db->trans_begin();
        
        $query = 'INSERT INTO `'.$this->db->dbprefix('web_field').'` 
                  (`web`,`title`,`url`,`email`,`template_language`,`hostname`,`username`,
                  `password`,`database`,`dbprefix`,`char_set`,`dbcollat`,
                  `sync_enabled`,`start_time`,`test_mode`, `virtuemart_version`,
                  `installed_languages`
                  )
                  VALUES
                  (\''.trim(addslashes($post_data['web'])).'\', 
                   \''.trim(addslashes($post_data['title'])).'\',
                   \''.trim(addslashes($post_data['url'])).'\',
                   \''.trim(addslashes($post_data['email'])).'\',
                   \''.$post_data['language'].'\',
                   \''.trim(addslashes($post_data['hostname'])).'\',
                   \''.trim(addslashes($post_data['username'])).'\',
                   DES_ENCRYPT(\''.trim(addslashes($post_data['password'])).'\', \''.self::SECRET_KEY.'\'), 
                   \''.trim(addslashes($post_data['database'])).'\',
                   \''.trim(addslashes($post_data['dbprefix'])).'\',
                   \''.trim(addslashes($post_data['char_set'])).'\',
                   \''.trim(addslashes($post_data['dbcollat'])).'\',
                   \''.trim(addslashes($post_data['sync_enabled'])).'\',
                   \''.trim(addslashes($post_data['start_time'])).'\',
                   \''.trim(addslashes($post_data['test_mode'])).'\',
                   \''.trim(addslashes($post_data['virtuemart_version'])).'\',
                   \''.trim(addslashes($post_data['installed_languages'])).'\'    
                   )
        ';
        
        $this->db->query($query);
        
        if($this->db->affected_rows() != 1)
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $query = ' SELECT `id` FROM `'.$this->db->dbprefix('providers').'` ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $providers = $result->result();
        }
        else 
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $query = 'INSERT INTO `'.$this->db->dbprefix('web_provider').'` 
                  (web, provider_id, sku_regexp, sku_regexp_2)
                  VALUES
                  (?,?,?,?)
        ';
        
        $total_affected_rows = 0;
        
        foreach($providers as $provider)
        {
            if(isset($post_data['provider'][$provider->id]))
            {
                $this->db->query($query, array(
                                            $post_data['web'], 
                                            $provider->id, 
                                            trim(addslashes($post_data['sku_regexp'][$provider->id])),
                                            trim(addslashes($post_data['sku_regexp_2'][$provider->id]))
                                        ));
                $total_affected_rows += $this->db->affected_rows();
            }
        }
            
        if(count($post_data['provider']) !== $total_affected_rows)
        {
            $this->db->trans_rollback();
            return false;
        }
                
        $this->db->trans_commit();
        
    }
    
    public function edit($web)
    {
        if(empty($web))
        {
            return false;
        }
        
        $post_data = $this->input->post();
        
        if(count($post_data) < 3)
        {
            return false;
        }
        
        $this->db->trans_begin();
        
        $query = ' UPDATE `'.$this->db->dbprefix('web_field').'` 
                   SET `title` = \''.trim(addslashes($post_data['title'])).'\', 
                       `url` = \''.trim(addslashes($post_data['url'])).'\', 
                       `email` = \''.trim(addslashes($post_data['email'])).'\', 
                       `template_language` = \''.trim(addslashes($post_data['language'])).'\', 
                       `hostname` = \''.trim(addslashes($post_data['hostname'])).'\',
                       `username` = \''.trim(addslashes($post_data['username'])).'\',
                       `password` = DES_ENCRYPT(\''.trim(addslashes($post_data['password'])).'\', \''.self::SECRET_KEY.'\'), 
                       `database` = \''.trim(addslashes($post_data['database'])).'\',
                       `dbprefix` = \''.trim(addslashes($post_data['dbprefix'])).'\',
                       `char_set` = \''.trim(addslashes($post_data['char_set'])).'\',
                       `dbcollat` = \''.trim(addslashes($post_data['dbcollat'])).'\',
                       `sync_enabled` = \''.trim(addslashes($post_data['sync_enabled'])).'\',
                       `start_time` = \''.trim(addslashes($post_data['start_time'])).'\',
                       `test_mode` = \''.trim(addslashes($post_data['test_mode'])).'\',
                       `virtuemart_version` = \''.trim(addslashes($post_data['virtuemart_version'])).'\',
                       `installed_languages` = \''.trim(addslashes($post_data['installed_languages'])).'\' 
                           
                   WHERE `web` = \''.$web.'\' 
        ';
                
        if(!$this->db->query($query))
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('web_provider').'` 
                   WHERE `web` = \''.$web.'\' 
        ';
        
        $this->db->query($query);
        
        $query = ' SELECT `id` FROM `'.$this->db->dbprefix('providers').'` ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() > 0)
        {
            $providers = $result->result();
        }
        else 
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $query = 'INSERT INTO `'.$this->db->dbprefix('web_provider').'` 
                  (web, provider_id, sku_regexp, sku_regexp_2)
                  VALUES
                  (?,?,?,?)
        ';
        
        $total_affected_rows = 0;
        
        foreach($providers as $provider)
        {
            if(isset($post_data['provider'][$provider->id]))
            {
                $this->db->query($query, array(
                                                $post_data['web'], 
                                                $provider->id, 
                                                trim(addslashes($post_data['sku_regexp'][$provider->id])),
                                                trim(addslashes($post_data['sku_regexp_2'][$provider->id])),
                                            ));
                $total_affected_rows += $this->db->affected_rows();
            }
        }
            
        if(count($post_data['provider']) !== $total_affected_rows)
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $this->db->trans_commit();
        
        return true;
    }
    
    public function get_template_language($web)
    {
        if(empty($web) || !is_string($web))
        {
            return false;
        }
        
        $query = ' SELECT `template_language` 
                   FROM `'.$this->db->dbprefix('web_field').'` 
                   WHERE `web` = \''.$web.'\'
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() == 1)
        {
            return $result->row()->template_language;
        }
        
        return false;
    }
    
    public function get_web_field($web)
    {
        if(empty($web) || !is_string($web))
        {
            return false;
        }
        
        $query = ' SELECT `web_field`.*, 
                   DES_DECRYPT(`web_field`.`password`, \''.self::SECRET_KEY.'\') as `password`, 
                   `languages`.`language` 
                   FROM `'.$this->db->dbprefix('web_field').'` as `web_field` 
                   LEFT JOIN `'.$this->db->dbprefix('languages').'` as `languages` 
                   ON `web_field`.`template_language` = `languages`.`code` 
                   WHERE `web_field`.`web` = \''.$web.'\' 
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows() == 1)
        {
            return $result->row();
        }
        
        return false;
    }
    
    public function remove($web)
    {
        if(empty($web) || !is_string($web))
        {
            return false;
        }
        
        $this->db->trans_begin();
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('web_provider').'` 
                   WHERE `web` = \''.$web.'\' 
        ';
        
        if(!$this->db->query($query))
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $query = ' DELETE FROM `'.$this->db->dbprefix('web_field').'` 
                   WHERE `web` = \''.$web.'\' 
        ';
        
        if(!$this->db->query($query))
        {
            $this->db->trans_rollback();
            return false;
        }
        
        $this->db->trans_commit();
        
        return true;
        
    }
    
    /**
     * Return html of select box Web fields
     * @param string $selected Selected WEB
     * @param string $name name of selectbox
     * @param string $extra Extra data
     * @return string
     */
    public function get_web_fields_list($selected = null, $name = 'web', $extra = 'id="web_fields_list"')
    {
        $html = '';
                
        $query = ' SELECT `web` 
                   FROM `'.$this->db->dbprefix('web_field').'` 
                   ORDER BY `web`    
        ';
        
        $result = $this->db->query($query);
        
        if($result->num_rows > 0)
        {
            $web_fields = $result->result();
            
            $options = array();
            
            $options[''] = '';
            
            foreach ($web_fields as $web_field)
            {
                $options[$web_field->web] = $web_field->web;
            }
            
            $html = form_dropdown($name, $options, $selected, $extra);
        }
        
        return $html;
    }
    
    /**
     * Get SKU regexps
     * @param string $web
     * @param int $provider_id
     * @return mixed Object (`sku_regexp`, `sku_regexp_2`) or boolean false on unsuccess
     */
    public function get_regexps($web, $provider_id)
    {
        if(!empty($web) && !empty($provider_id) && is_string($web) && is_integer($provider_id))
        {
            if(isset($this->_buffer_data['regexps'][$web][$provider_id]))
            {
                return $this->_buffer_data['regexps'][$web][$provider_id];
            }
            $query = ' SELECT `sku_regexp`, `sku_regexp_2` 
                       FROM `'.$this->db->dbprefix('web_provider').'` 
                       WHERE `web` = \''.$web.'\' AND `provider_id` = '.$provider_id.' 
            ';
            
            $result = $this->db->query($query);
            
            if($result->num_rows() == 1)
            {
                $this->_buffer_data['regexps'][$web][$provider_id] = $result->row();
                return $this->_buffer_data['regexps'][$web][$provider_id];
            }
            
            $this->_buffer_data['regexps'][$web][$provider_id] = false;
        }
        
        return FALSE;
    }
    
    /**
     * Return radio buttons of all Web fields
     * @return string
     */
    public function get_radio_inputs_web($name = 'web')
    {
        
        $html = '';
                
        $query = ' SELECT `web`, \''.$name.'\' as `name`, `web` as `value`, 
                           CONCAT(\'__\', `web`) as `id`, 
                           `web` as `title` 
                   FROM `'.$this->db->dbprefix('web_field').'` 
                   ORDER BY `web` 
        ';
        
        $result = $this->db->query($query);
        
        if ($result)
        {
            $web_fields = $result->result('array');
            
            foreach ($web_fields as $web)
            {                
                $html .= form_radio($web, null, null, set_radio($web['name'], $web['value'])) . form_label($web['title'], $web['id']);    
            }
        }
        
        return $html;
    }
    
    /**
     * Return radio group. On/Off sync process for this web
     * @param string $web
     * @return string HTML
     */
    public function get_sync_toggle($web = null)
    {
        $html = '';
        $enabled = false;
        
        $web_field = $this->get_web_field($web);
        
        if($web_field)
        {
            if($web_field->sync_enabled == 1)
            {
                $enabled = true;
            }                   
        }
        
        $data = array(
            array(
                'id' => 'sync_enabled',
                'type' => 'radio',
                'name' => 'sync_enabled',
                'value' => 1,
                'title' => 'Enabled',
                'checked' => $enabled
            ),
            array(
                'id' => 'sync_disabled',
                'type' => 'radio',
                'name' => 'sync_enabled',
                'value' => 0,
                'title' => 'Disabled',
                'checked' => !$enabled
            )
        );
        
        foreach ($data as $radio)
        {
            $html .= form_checkbox($radio);
            $html .= form_label($radio['title'], $radio['id']);
            $html .= "\n";
        }
        
        return $html;
    }
    
    /**
     * Return radio group. On/Off test mode for this web
     * @param type $web
     * @return string
     */
    public function get_test_mode_toggle($web = null)
    {
        $html = '';
        $enabled = false;
        
        $web_field = $this->get_web_field($web);
        
        if($web_field)
        {
            if($web_field->test_mode == 1)
            {
                $enabled = true;
            }                   
        }
        
        $data = array(
            array(
                'id' => 'test_mode_enabled',
                'type' => 'radio',
                'name' => 'test_mode',
                'value' => 1,
                'title' => 'Enabled',
                'checked' => $enabled
            ),
            array(
                'id' => 'test_mode_disabled',
                'type' => 'radio',
                'name' => 'test_mode',
                'value' => 0,
                'title' => 'Disabled',
                'checked' => !$enabled
            )
        );
        
        foreach ($data as $radio)
        {
            $html .= form_checkbox($radio);
            $html .= form_label($radio['title'], $radio['id']);
            $html .= "\n";
        }
        
        return $html;
    }
            
            
}