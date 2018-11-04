<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {

    private $cashTime = 5; // min

    public function setCashTime($time){
        $this->cashTime = $time;
    }

    public function getCashTime(){
        return $this->cashTime;
    }

    public function view($page = 'home')
    {
        if ( ! file_exists(APPPATH.'/views/pages/'.$page.'.php'))
        {
            show_404();
        }
        $data['title'] = ucfirst($page); // Capitalize the first letter
        $this->lang->load('course');
        #show only on home page
        if($page == 'home'){
            $data['course_data'] = $this->showCurrentCurse();
        }

        #load view
        $this->load->view('templates/header', $data);
        $this->load->view('pages/'.$page, $data);
        $this->load->view('templates/footer', $data);
    }

    private function showCurrentCurse(){
        #add cash
        if($this->isLastRespCashedYet()){
            $courses = $this->getCurrentCourseFromDB();
            $data['courses'] = $courses;
        }else{
            $courses = $this->getPBankCoursAPI();
            if($courses)
                $data['courses'] = json_decode($courses);
        }
        #load language keys
        $data['source_type'] = array(
            'cash'=>$this->lang->line('cash_course'),
            'cashless'=>$this->lang->line('cashless_course'));
        return $data;
    }

    private function showAllCourses(){
        return false;
    }

    private function isLastRespCashedYet(){
        return false;
    }

    private function setCache(){
        return false;
    }

    public function getAjaxCourse() {
        $data['type'] = $this->input->post('type');
        if($this->isLastRespCashedYet()){
            $courses = $this->getCurrentCourseFromDB();
        }else{
            $courses = $this->getPBankCoursAPI($data['type']);
        }
        echo $courses;
    }

    private function getCurrentCourseFromDB(){
        $this->load->model('course');
        return $this->course->get_current_curse_db();
    }
    private function getPBankCoursAPI($type = 'cash'){
        // https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid=5
        switch ($type){
            case 'cash':
                $id = 11;
                break;
            case 'cashless':
                $id = 3;
                break;
        }
        if(isset($id)){
            $this->load->model('course');
            return $this->course->
                get_current_curse_api('https://api.privatbank.ua/p24api/pubinfo?json&exchange&coursid='.$id);
        }
        return false;
    }
}
