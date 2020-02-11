<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio  extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('M_base');	
	}
	public function index()
	{

		$data['paises']  = $this->M_base->get_paises();
		$data['opp']  = $this->M_base->get_opp();
			
		$this->load->view('inicio', $data);
	}//end function
	
	public function get_marcadores(){
		echo json_encode($this->M_base->get_marcadores());
	}//end function

	public function get_marcadores_pais(){
		$param['id_pais'] = $this->input->post('id_pais');
		echo json_encode($this->M_base->get_marcadores_pais($param));
	}//end function

	public function get_name_pais(){
		$param['namePais'] = $this->input->post('namePais');
		echo json_encode($this->M_base->get_name_pais($param));
	}//end function

	public function get_calcular_distancia(){
		$param['namePais'] = $this->input->post('namePais');
		echo json_encode($this->M_base->get_calcular_distancia($param));
	}//end function
	public function get_organizaciones(){
		$param['id_organizacion'] = $this->input->post('id_organizacion');
		echo json_encode($this->M_base->get_organizaciones($param));

	}
	public function get_way(){

		$id_organizacion = $this->input->post("id_organizacion");
		
		//error_log($id_organizacion,45,'error_log.php');
		$way = [];
		for ($i=0; $i < count($id_organizacion) ; $i++) { 
			 $way =  $id_organizacion[$i];
		}

		echo json_encode($this->M_base->get_way($way));

	}

}

