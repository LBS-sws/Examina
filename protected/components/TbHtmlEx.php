<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2022/11/11 0011
 * Time: 11:51
 */
class TbHtmlEx extends TbHtml{

    /**
     * Generates a check box list.
     * @param string $name name of the check box list.
     * @param mixed $select selection of the check boxes.
     * @param array $data $data value-label pairs used to generate the check box list.
     * @param array $htmlOptions additional HTML attributes.
     * @return string the generated list.
     */
    public static function checkBoxListEx($name, $select, $data, $htmlOptions = array())
    {
        $inline = TbArray::popValue('inline', $htmlOptions, false);
        $separator = TbArray::popValue('separator', $htmlOptions, ' ');
        $container = TbArray::popValue('container', $htmlOptions, 'div');
        $containerOptions = TbArray::popValue('containerOptions', $htmlOptions, array());

        $labelOptions = TbArray::popValue('labelOptions', $htmlOptions, array());

        if (substr($name, -2) !== '[]') {
            $name .= '[]';
        }

        $checkAll = TbArray::popValue('checkAll', $htmlOptions);
        $checkAllLast = TbArray::popValue('checkAllLast', $htmlOptions);
        if ($checkAll !== null) {
            $checkAllLabel = $checkAll;
            $checkAllLast = $checkAllLast !== null;
        }

        $items = array();
        $baseID = $containerOptions['id'] = TbArray::popValue('baseID', $htmlOptions, parent::getIdByName($name));
        $id = 0;
        $checkAll = true;

        foreach ($data as $value => $row) {
            $label = key_exists("label",$row)?$row["label"]:$row["name"];
            $checked = !is_array($select) && !strcmp($value, $select) || is_array($select) && in_array($value, $select);
            $checkAll = $checkAll && $checked;
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID . '_' . $id++;
            foreach ($row as $dataItem=>$dataName){
                if(!in_array($dataItem,array("id","label","name"))){
                    $htmlOptions["data-{$dataItem}"] = $dataName;
                }
            }
            if ($inline) {
                $htmlOptions['label'] = $label;
                self::addCssClass('checkbox-inline', $labelOptions);
                $htmlOptions['labelOptions'] = $labelOptions;
                $items[] = self::checkBox($name, $checked, $htmlOptions);
            } else {
                $option = self::checkBox($name, $checked, $htmlOptions);
                $items[] = self::tag(
                    'div',
                    array('class' => 'checkbox'),
                    self::label($option . ' ' . $label, false, $labelOptions)
                );
            }
        }

        if (isset($checkAllLabel)) {
            $htmlOptions['value'] = 1;
            $htmlOptions['id'] = $id = $baseID . '_all';
            $htmlOptions['label'] = $checkAllLabel;
            $htmlOptions['labelOptions'] = $labelOptions;
            $item = self::checkBox($id, $checkAll, $htmlOptions);
            if ($inline) {
                self::addCssClass('checkbox-inline', $labelOptions);
            } else {
                $item = self::checkBox($id, $checkAll, $htmlOptions);
                $item = self::tag(
                    'div',
                    array('class' => 'checkbox'),
                    $item
                );
            }
            if ($checkAllLast) {
                $items[] = $item;
            } else {
                array_unshift($items, $item);
            }
            $name = strtr($name, array('[' => '\\[', ']' => '\\]'));
            $js = <<<EOD
jQuery('#$id').on('click', function() {
    jQuery("input[name='$name']").prop('checked', this.checked);
});
jQuery("input[name='$name']").on('click', function() {
	jQuery('#$id').prop('checked', !jQuery("input[name='$name']:not(:checked)").length);
});
jQuery('#$id').prop('checked', !jQuery("input[name='$name']:not(:checked)").length);
EOD;
            $cs = Yii::app()->getClientScript();
            $cs->registerCoreScript('jquery');
            $cs->registerScript($id, $js);
        }

        $inputs = implode($separator, $items);
        return !empty($container) ? self::tag($container, $containerOptions, $inputs) : $inputs;
    }


    /**
     * Generates a radio button list.
     * @param string $name name of the radio button list.
     * @param mixed $select selection of the radio buttons.
     * @param array $data $data value-label pairs used to generate the radio button list.
     * @param array $htmlOptions additional HTML attributes.
     * @return string the generated list.
     */
    public static function radioButtonListEx($name, $select, $data, $htmlOptions = array())
    {
        $inline = TbArray::popValue('inline', $htmlOptions, false);
        $separator = TbArray::popValue('separator', $htmlOptions, ' ');
        $container = TbArray::popValue('container', $htmlOptions, 'div');
        $containerOptions = TbArray::popValue('containerOptions', $htmlOptions, array());
        $labelOptions = TbArray::popValue('labelOptions', $htmlOptions, array());
        $empty = TbArray::popValue('empty', $htmlOptions);
        if (isset($empty)) {
            $empty = !is_array($empty) ? array('' => $empty) : $empty;
            $data = TbArray::merge($empty, $data);
        }

        $items = array();
        $baseID = $containerOptions['id'] = TbArray::popValue('baseID', $htmlOptions, parent::getIdByName($name));

        $id = 0;
        foreach ($data as $value => $row) {
            $label = key_exists("label",$row)?$row["label"]:$row["name"];
            $checked = !strcmp($value, $select);
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID . '_' . $id++;
            foreach ($row as $dataItem=>$dataName){
                if(!in_array($dataItem,array("id","label","name"))){
                    $htmlOptions["data-{$dataItem}"] = $dataName;
                }
            }
            if ($inline) {
                $htmlOptions['label'] = $label;
                self::addCssClass('radio-inline', $labelOptions);
                $htmlOptions['labelOptions'] = $labelOptions;
                $items[] = self::radioButton($name, $checked, $htmlOptions);
            } else {
                $option = self::radioButton($name, $checked, $htmlOptions);
                $items[] = self::tag(
                    'div',
                    array('class' => 'radio'),
                    self::label($option . ' ' . $label, false, $labelOptions)
                );
            }
        }

        $inputs = implode($separator, $items);
        return !empty($container) ? self::tag($container, $containerOptions, $inputs) : $inputs;
    }

}