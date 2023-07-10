<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('f.name'),'#',$this->createOrderLink('statisticsAll-list','f.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('b.name'),'#',$this->createOrderLink('statisticsAll-list','b.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('entry_time').$this->drawOrderArrow('f.entry_time'),'#',$this->createOrderLink('statisticsAll-list','f.entry_time'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('quiz_name').$this->drawOrderArrow('g.name'),'#',$this->createOrderLink('statisticsAll-list','g.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('correct').$this->drawOrderArrow('correct'),'#',$this->createOrderLink('statisticsAll-list','correct'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('lcd').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('statisticsAll-list','a.lcd'))
        ;
        ?>
    </th>
</tr>