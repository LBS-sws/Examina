<tr class='clickable-row' data-href='<?php echo $this->getLink($this->model->menu_code, 'chapterQuestion/edit', 'chapterQuestion/edit', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton($this->model->menu_code, 'chapterQuestion/edit', 'chapterQuestion/edit', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['title_code']; ?></td>
    <td><?php echo $this->record['title_type']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['display']; ?></td>
</tr>



