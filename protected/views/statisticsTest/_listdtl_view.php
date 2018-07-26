<tr class='clickable-row' data-href='<?php echo $this->getLink('SC01', 'statisticsTest/detailStaff', 'statisticsTest/detailStaff', array('staff'=>$this->record['id'],'index'=>$this->model->qui_id));?>'>

    <td><?php echo $this->needHrefButton('SC01', 'statisticsTest/detailStaff', 'view', array('staff'=>$this->record['id'],'index'=>$this->model->qui_id)); ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['join_num']; ?></td>
    <td><?php echo $this->record['correct']; ?></td>
    <td><?php echo $this->record['correctNum']; ?></td>
</tr>
