<?php
/**
 * 
 */
class M_base extends CI_Model
{
    


    public function get_marcadores()
    {
        $this->db->select("*");
        $this->db->from("opp");
        $r = $this->db->get();
        return $r->result();
    }
    public function get_marcadores_pais($param)
    {
        $this->db->select("*");
        $this->db->from("opp");
        $this->db->where("fk_id_pais", $param["id_pais"]);
        $r = $this->db->get();
        return $r->result();
    }
    public function get_paises()
    {
        $this->db->select("p.id_pais as id, p.nombre as nombre_pais");
        $this->db->from("pais p");
        $this->db->join("opp o", "id_pais = fk_id_pais");
        $this->db->group_by("p.id_pais");
        $r = $this->db->get();
        return $r->result();
    }
        public function get_opp()
    {
        $this->db->select("*");
        $this->db->from("opp");
        $r = $this->db->get();
        return $r->result();
    }
}