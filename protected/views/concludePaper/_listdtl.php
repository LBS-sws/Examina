
<tr class='clickable-row' data-href='<?php echo $this->getLink($this->model->menu_code, 'concludePaper/edit', 'concludePaper/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton($this->model->menu_code, 'concludePaper/edit', 'concludePaper/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['employee']; ?></td>
    <td><?php echo $this->record['markedly_name']; ?></td>
    <td><?php echo $this->record['join_must']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['title_sum']; ?></td>
    <td><?php echo $this->record['title_num']; ?></td>
    <td><?php echo $this->record['success_ratio']; ?></td>
</tr>
