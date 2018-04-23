<tr>
	<th width="50px"></th>
	<th width="150px">
		<?php echo TbHtml::link($this->getLabelName('title_code').$this->drawOrderArrow('title_code'),'#',$this->createOrderLink('question-list','title_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('question-list','name'))
			;
		?>
	</th>
</tr>
