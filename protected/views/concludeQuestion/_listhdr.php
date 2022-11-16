<tr>
    <th width="50px"></th>
    <th width="150px">
        <?php echo TbHtml::link($this->getLabelName('title_code').$this->drawOrderArrow('a.title_code'),'#',$this->createOrderLink('concludeQuestion-list','a.title_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('chapter_name').$this->drawOrderArrow('b.chapter_name'),'#',$this->createOrderLink('concludeQuestion-list','b.chapter_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('title_type').$this->drawOrderArrow('a.title_type'),'#',$this->createOrderLink('concludeQuestion-list','a.title_type'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('name').$this->drawOrderArrow('a.name'),'#',$this->createOrderLink('concludeQuestion-list','a.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('show_num').$this->drawOrderArrow('a.show_num'),'#',$this->createOrderLink('concludeQuestion-list','a.show_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('success_num').$this->drawOrderArrow('a.success_num'),'#',$this->createOrderLink('concludeQuestion-list','a.success_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('success_ratio').$this->drawOrderArrow('success_ratio'),'#',$this->createOrderLink('concludeQuestion-list','success_ratio'))
        ;
        ?>
    </th>
</tr>
