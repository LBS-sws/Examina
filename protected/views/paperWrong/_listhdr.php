<tr>
    <th></th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('employee').$this->drawOrderArrow('a.id'),'#',$this->createOrderLink('paperWrong-list','a.id'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('paperWrong-list','b.name'))
			;
		?>
	</th>
	<th>
		<?php echo TbHtml::link($this->getLabelName('title_type').$this->drawOrderArrow('b.title_type'),'#',$this->createOrderLink('paperWrong-list','b.title_type'))
			;
		?>
	</th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('chapter_name').$this->drawOrderArrow('f.chapter_name'),'#',$this->createOrderLink('paperWrong-list','f.chapter_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('wrong_date').$this->drawOrderArrow('a.wrong_date'),'#',$this->createOrderLink('paperWrong-list','a.wrong_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('wrong_type').$this->drawOrderArrow('a.wrong_type'),'#',$this->createOrderLink('paperWrong-list','a.wrong_type'))
        ;
        ?>
    </th>
</tr>
