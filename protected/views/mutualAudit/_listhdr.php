<tr>
    <th>
        <?php
        echo TbHtml::checkBox("checkAll","",array("id"=>"checkAll"));
        ?>
    </th>
    <th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('b.code'),'#',$this->createOrderLink('MutualAudit-list','b.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('MutualAudit-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('b.city'),'#',$this->createOrderLink('MutualAudit-list','b.city'))
			;
		?>
	</th>
	<th width="60%">
		<?php echo TbHtml::link($this->getLabelName('end_body').$this->drawOrderArrow('a.end_body'),'#',$this->createOrderLink('MutualAudit-list','a.end_body'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('mutual_date').$this->drawOrderArrow('a.mutual_date'),'#',$this->createOrderLink('MutualAudit-list','a.mutual_date'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('mutual_state').$this->drawOrderArrow('a.mutual_state'),'#',$this->createOrderLink('MutualAudit-list','a.mutual_state'))
			;
		?>
	</th>
</tr>
