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
		$this->load->view('inicio');
	}
	
	public function get_marcadores(){
		echo json_encode($this->M_base->get_marcadores());
	}
}

