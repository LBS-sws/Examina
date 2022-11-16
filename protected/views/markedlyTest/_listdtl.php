
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->model->menu_code, 'markedlyTest/edit', 'markedlyTest/view', array('index'=>$this->record['id']));?>'>


    <?php if (Yii::app()->user->validRWFunction($this->model->menu_code)): ?>
    <td><?php echo $this->drawEditButton($this->model->menu_code, 'markedlyTest/edit', 'markedlyTest/view', array('index'=>$this->record['id'])); ?></td>
    <?php endif; ?>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['join_must']; ?></td>
    <td><?php echo $this->record['bumen_ex']; ?></td>
    <td><?php echo $this->record['exa_num']; ?></td>
    <td>
        <?php
        echo TbHtml::link(Yii::t("study","Take Test"),Yii::app()->createUrl('MarkedlyTake/test', array('markedly_id'=>$this->record['id'])),array("class"=>"btn btn-default"));
        ?>
    </td>
</tr>
