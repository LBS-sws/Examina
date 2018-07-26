<tr class='clickable-row' data-href='<?php echo $this->getLink('SC01', 'statisticsTest/detail', 'statisticsTest/detail', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->needHrefButton('SC01', 'statisticsTest/detail', 'view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['lcd']; ?></td>
    <td><?php echo $this->record['correct']; ?></td>
    <td><?php echo $this->record['correctNum']; ?></td>
</tr>
