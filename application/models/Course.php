<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends CI_Model{
    function get_current_curse_db(){
        $query = $this->db->get('course',1);
        return $query->result_array();
    }
    function get_all_curse_db($start=0,$limit = null){
        $query = $this->db->get('course',1);
        return $query->result_array();
    }
    function get_current_curse_api($url,array $courseTypes, $returnType = "all",array $currency, $saveInBD){
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
                    $this->save_new_curse($data,$type);
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
    private function getLastIdSession(){
        $query = $this->db->query("SELECT * FROM course");
        $lastRow = $query->last_row();
        return $lastRow->id_session;
    }
    private function save_new_curse($data){
        $query = $this->db->insert_batch('course',$data);
    }
}