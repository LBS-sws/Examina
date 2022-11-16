<tr>
    <th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('concludePaper-list','a.id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('markedly_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('concludePaper-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('join_must').$this->drawOrderArrow('b.join_must'),'#',$this->createOrderLink('concludePaper-list','b.join_must'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('concludePaper-list','a.lcd'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('title_sum').$this->drawOrderArrow('a.title_sum'),'#',$this->createOrderLink('concludePaper-list','a.title_sum'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('title_num').$this->drawOrderArrow('a.title_num'),'#',$this->createOrderLink('concludePaper-list','a.title_num'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('success_ratio').$this->drawOrderArrow('a.success_ratio'),'#',$this->createOrderLink('concludePaper-list','a.success_ratio'))
			;
		?>
	</th>
</tr>
