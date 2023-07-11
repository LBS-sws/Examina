<tr>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('videoHits-list','f.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('videoHits-list','b.name'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('f.entry_time'),'#',$this->createOrderLink('videoHits-list','f.entry_time'))
        ;
        ?>
    </th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('study_title').$this->drawOrderArrow('g.study_title'),'#',$this->createOrderLink('videoHits-list','g.study_title'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('hit_date').$this->drawOrderArrow('a.hit_date'),'#',$this->createOrderLink('videoHits-list','a.hit_date'))
			;
		?>
	</th>
</tr>
