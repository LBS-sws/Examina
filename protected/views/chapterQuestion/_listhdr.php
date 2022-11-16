<tr>
	<th width="50px"></th>
	<th width="150px">
		<?php echo TbHtml::link($this->getLabelName('title_code').$this->drawOrderArrow('title_code'),'#',$this->createOrderLink('chapterQuestion-list','title_code'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('title_type').$this->drawOrderArrow('title_type'),'#',$this->createOrderLink('chapterQuestion-list','title_type'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('name'),'#',$this->createOrderLink('chapterQuestion-list','name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('display').$this->drawOrderArrow('display'),'#',$this->createOrderLink('chapterQuestion-list','display'))
			;
		?>
	</th>
</tr>
