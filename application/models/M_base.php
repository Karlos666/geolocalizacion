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
}