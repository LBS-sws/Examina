
<tr class='clickable-row <?php echo $this->record['color']; ?>' data-href='<?php echo $this->getLink($this->model->menu_code, 'mutualAudit/edit', 'mutualAudit/view', array('index'=>$this->record['id']));?>'>

    <td class="end_click">
        <?php
        if($this->record['mutual_state']==1){
            echo TbHtml::checkBox("checkId[{$this->record['id']}]","",array("class"=>"checkOne"));
        }else{
            echo "&nbsp;";
        }
        ?>
    </td>
    <td><?php echo $this->drawEditButton($this->model->menu_code, 'mutualAudit/edit', 'mutualAudit/view', array('index'=>$this->record['id'])); ?></td>
    <td><?php echo $this->record['employee_code']; ?></td>
    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['end_body']; ?></td>
    <td><?php echo $this->record['mutual_date']; ?></td>
    <td><?php echo $this->record['state']; ?></td>
</tr>
