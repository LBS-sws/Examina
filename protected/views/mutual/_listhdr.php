<tr>
    <th></th>
	<th width="80%">
		<?php echo TbHtml::link($this->getLabelName('end_body').$this->drawOrderArrow('a.end_body'),'#',$this->createOrderLink('MutualMy-list','a.end_body'))
			;
		?>
	</th>
	<th width="10%">
		<?php echo TbHtml::link($this->getLabelName('mutual_date').$this->drawOrderArrow('a.mutual_date'),'#',$this->createOrderLink('MutualMy-list','a.mutual_date'))
			;
		?>
	</th>
	<th width="10%">
		<?php echo TbHtml::link($this->getLabelName('mutual_state').$this->drawOrderArrow('a.mutual_state'),'#',$this->createOrderLink('MutualMy-list','a.mutual_state'))
			;
		?>
	</th>
</tr>
