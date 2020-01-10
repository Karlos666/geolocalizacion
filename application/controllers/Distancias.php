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
        foreach ($data["org"] as $organizacion) {
            if($organizacion->latitud != null){
                $localizacion[] = array(
                    'lat' => $organizacion->latitud,
                    'lng' => $organizacion->longitud
                );
            }
    
        }
        $localizacion_json = json_encode($localizacion);

        $data['json_localizacion'] = $localizacion_json;

        $this->load->view('distancias', $data);
    }//end function
    
 public function get_name_pais(){
        $param['namePais'] = $this->input->post('namePais');
        echo json_encode($this->M_base->get_name_pais($param));
    }//end function
}


