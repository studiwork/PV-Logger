<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

        var $data;
    
        function __construct() 
        {
            parent::__construct();
            $loggedin = $this->_checkLogin();
        }
	function index()
	{
            $this->data['main_content'] = 'standard_view';
            $this->load->view('template' , $this->data);            
	}
        
        function _checkLogin()
        {
            $this->data['logged_in'] = FALSE;
            $this->data['name'] = '';
            $this->data['userlevel'] = -1; //unprivileged
            $this->data['power_graph'] = 'current';
           
            $is_logged_in = $this->session->userdata('logged_in');
            if(isset($is_logged_in) && $is_logged_in == TRUE)
            {
                $this->data['logged_in'] = TRUE;
                $this->data['name'] = $this->session->userdata('username');
                $this->data['userlevel'] = $this->session->userdata('userlevel');
            } 
        }
                
       
	
}

/* End of file index.php */
/* Location: ./application/controllers/index.php */