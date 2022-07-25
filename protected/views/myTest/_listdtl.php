
<tr class='clickable-row' data-href='<?php echo $this->getLink('EM01', 'myTest/view', 'myTest/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->needHrefButton('EM01', 'myTest/view', 'view', array('index'=>$this->record['id'])); ?></td>

    <td><?php echo $this->record['name']; ?></td>
    <!--
    <td><?php echo $this->record['bumen_ex']; ?></td>
    -->
    <td><?php echo $this->record['exa_num']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['correct']; ?></td>
    <td><?php echo $this->record['correct_num']; ?></td>
    <td><?php echo $this->record['wrong_num']; ?></td>
</tr>
