<tr>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('f.code'),'#',$this->createOrderLink('concludeStaff-list','f.code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('concludeStaff-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('f.city'),'#',$this->createOrderLink('concludeStaff-list','f.city'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('position').$this->drawOrderArrow('f.position'),'#',$this->createOrderLink('concludeStaff-list','f.position'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('title_sum').$this->drawOrderArrow('question_sum'),'#',$this->createOrderLink('concludeStaff-list','question_sum'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('title_num').$this->drawOrderArrow('question_num'),'#',$this->createOrderLink('concludeStaff-list','question_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('success_ratio').$this->drawOrderArrow('question_ratio'),'#',$this->createOrderLink('concludeStaff-list','question_ratio'))
			;
		?>
	</th>
</tr>
