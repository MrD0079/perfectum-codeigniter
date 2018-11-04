<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <div class="col-12">
        <div class="text-center">
            <h1>Главная</h1>
        </div>
    </div>
</div>

<?php $this->load->view('templates/CourseBlock.php', $course_data); ?>
<div class="link-block">
    <div class="container">
        <div class="col-12">
            <a href="/history"><?=$this->lang->line('archive')?></a>
        </div>
    </div>
</div>

