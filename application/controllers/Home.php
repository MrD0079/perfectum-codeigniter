<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    private $cashTime = 600; // 10 min
    private $cacheCourseId = 'courseCache';
    private $currency = array("base_ccy"=>"UAH","ccy"=>array("USD","EUR"));

    public function setCashTime($time){
        $this->cashTime = $time;
    }

    public function getCashTime(){
        return $this->cashTime;
    }

    public function setCurrency($name,$value){
        $this->currency[$name] = $value;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function index()
    {
        $page = "Home";
        $this->lang->load('site');
        $data['title'] = ucfirst($this->lang->line('title-home'));

        $this->lang->load('course');
        $data['course_data'] = $this->showCurrentCurse();

        #load view
        $this->load->view('templates/header', $data);
        $this->load->view('pages/'.$page, $data);
        $this->load->view('templates/footer', $data);
    }

    private function showCurrentCurse($cashType = "cash"){
        if($dataCash = $this->getCache($this->cacheCourseId."_".$cashType)){
            $data['courses'] = json_decode($dataCash,true);
        }else{
            $courses = $this->getPBankCourseAPI("cash");
            if($courses)
                $data['courses'] = $courses;
            $this->setCache($this->cacheCourseId."_".$cashType,$courses);
        }
        //load language keys
        $data['source_type'] = array(
            'cash'=>$this->lang->line('cash_course'),
            'cashless'=>$this->lang->line('cashless_course'));
        return $data;
    }

    private function getCache($cashId){
        $this->load->driver('cache',
            array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'my_')
        );
        return $this->cache->get($cashId);
    }

    private function setCache($cashId,$data){
        $this->load->driver('cache',
            array('adapter' => 'apc', 'backup' => 'file', 'key_prefix' => 'my_')
        );
        if(!$this->cache->get($cashId)){
            if(is_array($data))
                $data = json_encode($data);
            return $this->cache->save($cashId,$data,$this->cashTime);
        }
        return false;
    }

    public function getAjaxCourse() {
        $data['type'] = $this->input->post('type');

        if($dataCache = $this->getCache($this->cacheCourseId."_".$data['type'])){
            $courses = $dataCache;
        }else{
            $courses = $this->getPBankCourseAPI($data['type'],false);
           $this->setCache($this->cacheCourseId."_".$data['type'],$courses);
            $courses = json_encode($courses);
        }
        echo $courses;
    }

    private function getPBankCourseAPI($type = 'all',$saveInBD = true){
        //PrivatBank API ( https://api.privatbank.ua/#p24/exchange )
        $courseTypes = array(
            'cash'=>11,
            'cashless'=>3
        );
        if($type != ""){
            $this->load->model('course');
            $url = 'https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=';
            return $this->course->
                get_current_curse_api($url,$courseTypes,$type,$this->currency,$saveInBD);
        }
        return false;
    }
}
