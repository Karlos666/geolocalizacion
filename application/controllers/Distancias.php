<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Distancias  extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('M_base');   
    }
    public function index()
    { 
        $data["org"] = $this->M_base->get_name_org("mexico");        
        $this->load->view('distancias', $data);
    }//end function
    
 public function get_name_pais(){
        $param['namePais'] = $this->input->post('namePais');
        echo json_encode($this->M_base->get_name_pais($param));
    }//end function
}


