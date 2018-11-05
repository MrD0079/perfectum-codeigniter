<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<section id="course-block">
    <div class="container">
        <div class="col-4">
            <h2>Курсы валют</h2>
            <select class="form-control" name="course_type" id="course-type">
                <?= $count = 0;
                foreach ($source_type as $type => $text):?>
                    <option value="<?=$type?>" <?=($count == 0)?"selected='selected'":""?>><?=$text?></option>
                    <?= $count++;
                endforeach;?>
            </select>
            <script>
                $("#course-type").on("change",function(){
                    $.ajax({
                        url : "<?php echo site_url('pages/getAjaxCourse')?>",
                        type : "POST",
                        data : {'type':$(this).val()},
                        success : function(data) {
                            courses = jQuery.parseJSON(data);
                            $("#course-table tbody tr").remove();
                            $.each( courses, function( key, course ) {
                                row = "<tr>";
                                row += "<td>"+course.ccy+"</td>";
                                row += "<td>"+course.base_ccy+"</td>";
                                row += "<td>"+course.buy+"</td>";
                                row += "<td>"+course.sale+"</td>";
                                row += "</tr>";
                                $(row).appendTo($("#course-table tbody"));
                            });
                        },
                        error : function(data) {
                            alert("Eror. Not load data from API/")
                        }
                    });
                });
            </script>
            <div class="table-responsive">
                <table id="course-table" class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Покупка</th>
                        <th>Продажа</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr>
                            <td><?= $course['ccy'] ?></td>
                            <td><?= $course['base_ccy'] ?></td>
                            <td><?= number_format(round($course['buy'],2),2,'.','') ?></td>
                            <td><?= number_format(round($course['sale'],2),2,'.',''); ?></td>
                        </tr>
                    <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>