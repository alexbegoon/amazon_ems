<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * /application/core/MY_Loader.php
 *
 */
class MY_Loader extends CI_Loader {
    public function template($template_name, $vars = array(), $return = FALSE)
    {
        $content  = $this->view('templates/header_view', $vars, $return);
        $content .= $this->view($template_name, $vars, $return);
        $content .= $this->view('templates/footer_view', $vars, $return);

        if ($return)
        {
            return $content;
        }
    }
}