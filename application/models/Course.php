<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Model{
    function getCurseFromDB($perPage,$offset){
        if($this->CheckExistTable("course")) {
            $this->db->order_by('id', 'desc');
            $query = $this->db->get('course', $perPage, $offset);
            return $query->result_array();
        }else{
            return false;
        }
    }
    function countRowsInTable($table){
        return $this->db->count_all($table);
    }
    private function checkExistTable($table){
        return $this->db->table_exists($table);
    }
    private function createTable($table = "course"){
        if(!$this->checkExistTable($table)){
            $this->load->dbforge();
            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => TRUE
                ),
                'ccy' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ),
                'base_ccy' => array(
                    'type' =>'VARCHAR',
                    'constraint' => '100',
                ),
                'buy_cash' => array(
                    'type' => 'FLOAT',
                    'default' => NULL,
                ),
                'buy_cashless' => array(
                    'type' => 'FLOAT',
                    'default' => NULL,
                ),
                'sale_cash' => array(
                    'type' => 'FLOAT',
                    'default' => NULL,
                ),
                'sale_cashless' => array(
                    'type' => 'FLOAT',
                    'default' => NULL,
                ),
                'date' => array(
                    'type' => 'datetime',
                    'default' => `CURRENT_TIMESTAMP`,
                ),
            );
            $this->dbforge->add_field($fields);
            //$this->dbforge->add_field('id');
            $this->dbforge->add_key('id',TRUE);
            $attributes = array('ENGINE' => 'InnoDB','DEFAULT CHARSET'=>'utf8');
            $this->dbforge->create_table($table, FALSE, $attributes);
        }
    }
    function getCurrentCurseAPI($url,array $courseTypes, $returnType = "all",array $currency, $saveInBD){
        if(isset($url)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            foreach ($courseTypes as $key => $type) {
                curl_setopt($ch, CURLOPT_URL, $url.$type);
                $result[$key] = json_decode(curl_exec($ch));
            }
            curl_close($ch);
            if(isset($result)){
                //выбрать из списка только необходимые валюты указанные в $currency
                $data = $this->getRequiredCurrency($result,$currency);

                if($saveInBD){
                    $this->saveNewCurse($data);
                }
                if($returnType != "all")
                    return $this->getCurseByType($data,$returnType);
                else
                    return $data;
            }
        }
        return false;
    }
    private function getCurseByType($data,$type){
        $result = array();
        foreach ($data as $ccy) {
            $item = array();
            $item['base_ccy'] = $ccy['base_ccy'];
            $item['ccy'] = $ccy['ccy'];
            if($type == "cashless"){
                $item['buy'] = $ccy['buy_cashless'];
                $item['sale'] = $ccy['sale_cashless'];
            }else if($type == "cash"){
                $item['buy'] = $ccy['buy_cash'];
                $item['sale'] = $ccy['sale_cash'];
            }
            $result[] = $item;
        }
        return $result;
    }
    private function getRequiredCurrency(array $data,$currency){
        $result = array();
        //выборка из массива только необходимых валют
        //$currensy = ["base_ccy"=>"UAH","ccy"=>array("USD","EUR")]
        //$courseTypes = array('cash'=>11,'cashless'=>3);
        foreach ($currency['ccy'] as $ccy) {
            $item = array();
            $item['base_ccy'] = $currency['base_ccy'];
            $item['ccy'] = $ccy;
            foreach ($data['cash'] as $value) {
                if($value->ccy == $ccy){
                    $item['buy_cash'] = $value->buy;
                    $item['sale_cash'] = $value->sale;
                }
            }
            foreach ($data['cashless'] as $value) {
                if($value->ccy == $ccy){
                    $item['buy_cashless'] = $value->buy;
                    $item['sale_cashless'] = $value->sale;
                }
            }
            $result[] = $item;
        }
        return $result;
    }
    private function saveNewCurse($data){
        $this->createTable('course');
        return $this->db->insert_batch('course',$data);
    }
}