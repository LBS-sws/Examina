
    <?php if (empty($this->record['already'])): ?>
    <tr class='clickable-row' data-href='javascript:void(0);'>

    <td></td>
    <?php else:?>
    <tr class='clickable-row' data-href='<?php echo $this->getLink('SC01', 'statisticsTest/view', 'statisticsTest/view', array('index'=>$this->record['id']));?>'>

    <td><?php echo $this->needHrefButton('SC01', 'statisticsTest/view', 'view', array('index'=>$this->record['id'])); ?></td>
    <?php endif; ?>


    <td><?php echo $this->record['name']; ?></td>
    <td><?php echo $this->record['bumen_ex']; ?></td>
    <td><?php echo $this->record['start_time']; ?></td>
    <td><?php echo $this->record['end_time']; ?></td>
    <td><?php echo $this->record['already']; ?></td>
    <td><?php echo $this->record['correct']; ?></td>
</tr>
