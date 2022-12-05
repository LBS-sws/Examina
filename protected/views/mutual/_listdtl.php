
<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink($this->model->menu_code, 'mutual/edit', 'mutual/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->drawEditButton($this->model->menu_code, 'mutual/edit', 'mutual/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['end_body']; ?></td>
    <td><?php echo $this->record['mutual_date']; ?></td>
    <td><?php echo $this->record['state']; ?></td>
</tr>
