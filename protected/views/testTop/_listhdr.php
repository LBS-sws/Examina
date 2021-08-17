<tr>
	<th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('testTop-list','a.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('join_must').$this->drawOrderArrow('a.join_must'),'#',$this->createOrderLink('testTop-list','a.join_must'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bumen_ex').$this->drawOrderArrow('a.bumen_ex'),'#',$this->createOrderLink('testTop-list','a.bumen_ex'))
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
    <th></th>
</tr>
