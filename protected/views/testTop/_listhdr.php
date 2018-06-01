<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('testTop-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('type_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('testTop-list','b.name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('a.city'),'#',$this->createOrderLink('testTop-list','a.city'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('start_time').$this->drawOrderArrow('a.start_time'),'#',$this->createOrderLink('testTop-list','a.start_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('end_time').$this->drawOrderArrow('a.end_time'),'#',$this->createOrderLink('testTop-list','a.end_time'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('exa_num').$this->drawOrderArrow('a.exa_num'),'#',$this->createOrderLink('testTop-list','a.exa_num'))
			;
		?>
	</th>
</tr>
