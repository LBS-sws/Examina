
<tr class='clickable-row<?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink('EM01', 'myTest/view', 'myTest/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->needHrefButton('EM01', 'myTest/view', 'view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['exa_num']; ?></td>
    <td><?php echo $this->record['correct']; ?></td>
    <td><?php echo $this->record['correct_num']; ?></td>
    <td><?php echo $this->record['wrong_num']; ?></td>
    <td>
        <?php
        if(!$this->record['bool']){
            echo TbHtml::button('<span class="fa fa-gamepad"></span> '.Yii::t('examina','start test'), array(
                'submit'=>Yii::app()->createUrl('myTest/new',array("index"=>$this->record['id']))));
        }
        ?>
    </td>
</tr>
