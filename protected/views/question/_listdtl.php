<tr class='clickable-row' data-href='<?php echo $this->getLink('SS02', 'question/edit', 'question/view', array('index'=>$this->record['id'],'quiz_id'=>$this->model->index));?>'>


    <td><?php echo $this->drawEditButton('SS02', 'question/edit', 'question/view', array('index'=>$this->record['id'],'quiz_id'=>$this->model->index)); ?></td>



    <td><?php echo $this->record['title_code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
</tr>



