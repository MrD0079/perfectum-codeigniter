<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
    <div class="col-12">
        <div class="text-center">
            <h1>История изменений курсов</h1>
        </div>
    </div>
</div>

<div class="link-block">
    <div class="container">
        <div class="col-12">
            <a href="/" class="btn btn-outline-primary" role="button"><?=$this->lang->line('home_link')?></a>
        </div>
    </div>
</div>
<p></p>
<!-- Тут блок с таблицей историей изменений -->
    <section id="history-courses">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="course-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Валюта</th>
                                <th>Покупка<br/>(в отделении)</th>
                                <th>Покупка<br/>(для карт)</th>
                                <th>Продажа<br/>(в отделении)</th>
                                <th>Продажа<br/>(для карт)</th>
                                <th>Дата</th>
                                <th>Время</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if(isset($courses)): ?>
                            <?php foreach ($courses['courses'] as $key => $course): ?>

                                <?php list($date,$time) = explode(" ",$course['date']); ?>
                                <tr>
                                    <td><?= $course['ccy']."/".$course['base_ccy'] ?></td>
                                    <td><?= number_format(round($course['buy_cash'],2),2,'.','') ?></td>
                                    <td><?= number_format(round($course['buy_cashless'],2),2,'.',''); ?></td>
                                    <td><?= number_format(round($course['sale_cash'],2),2,'.','') ?></td>
                                    <td><?= number_format(round($course['sale_cashless'],2),2,'.',''); ?></td>
                                    <td><?= $date?></td>
                                    <td><?= $time?></td>
                                </tr>
                            <?php endforeach;?>
                            <?php endif;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
<nav aria-label="Course navigation">
<?php echo $this->pagination->create_links();?>
</nav>
