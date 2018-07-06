<tr>
	<th width="50px"></th>
	<th width="150px">
		<?php echo TbHtml::link($this->getLabelName('title_code').$this->drawOrderArrow('a.title_code'),'#',$this->createOrderLink('question-list','a.title_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('question-list','a.name'))
			;
		?>
	</th>
</tr>
