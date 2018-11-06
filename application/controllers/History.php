<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class History extends CI_Controller {

    public function index()
    {
        $this->Page();
    }
    public function Page($id = null){
        if(is_null($id)){
            $id = $this->uri->segment(3);
        }
        $data['courses'] = $this->GetCourses($id );
        $this->lang->load('site');
        $page = $this->lang->line('title-history');
        $data['title'] = ucfirst($page);
        $this->lang->load('course');
        $this->load->view('templates/header', $data);
        $this->load->view('pages/history',$data);
        $this->load->view('templates/footer', $data);
    }
    private function GetCourses($id){

        $this->load->model('course');

        $config['base_url'] = base_url().'history/page';
        $config['total_rows'] = $this->course->countRowsInTable('course');
        $config['per_page'] = "10"; // 2 * 10 = 20
        $config['num_links'] = 2;
        $config['use_page_numbers'] = TRUE;
        #bootstrap pagination
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);
        $data['courses'] = $this->course->getCurseFromDB($config['per_page'],$id);
        return $data;
    }
}