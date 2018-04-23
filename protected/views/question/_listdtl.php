<tr class='clickable-row' data-href='<?php echo $this->getLink('SS01', 'question/edit', 'question/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('SS01', 'question/edit', 'question/view', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['title_code']; ?></td>
    <td><?php echo $this->record['name']; ?></td>
</tr>



