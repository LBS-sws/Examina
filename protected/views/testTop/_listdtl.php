
<tr class='clickable-row' data-href='<?php echo $this->getLink('SS02', 'testTop/edit', 'testTop/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('SS02', 'testTop/edit', 'testTop/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['exa_num']; ?></td>
    <td><?php echo TbHtml::link(Yii::t("examina","question list"),Yii::app()->createUrl('question/index', array('index'=>$this->record['id']))); ?></td>
</tr>
