
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->model->menu_code, 'concludeQuestion/edit', 'concludeQuestion/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton($this->model->menu_code, 'concludeQuestion/edit', 'concludeQuestion/view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['title_code']; ?></td>
    <td><?php echo $this->record['chapter_name']; ?></td>
    <td><?php echo $this->record['title_type']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['show_num']; ?></td>
    <td><?php echo $this->record['success_num']; ?></td>
    <td><?php echo $this->record['success_ratio']; ?></td>
</tr>
