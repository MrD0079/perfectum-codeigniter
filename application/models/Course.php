<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Model{
    function get_current_curse_db(){
        $query = $this->db->get('course',1);
        return $query->result_array();
    }
    function get_current_curse_api($url = null){
        if(!is_null($url)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            if(isset($result)){
                //$this->save_new_curse($result);
                return $result;
            }

            return false;
        }
        return false;
    }
    private function save_new_curse($data){
        $data = (array)json_decode($data);
        $query = $this->db->insert_batch('course',$data);
    }
}