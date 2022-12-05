<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - TeStudy';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?2.1");//
?>
<section class="content-header">
    <h1><?php echo Yii::t("app","Study mutual");?></h1>
	<ol class="breadcrumb">
		<li><?php echo $model->menu_name;?></li>
		<li class="active"><?php echo Yii::t("app","Study mutual");?></li>
	</ol>
</section>

<section class="content">
	<div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                    echo TbHtml::button('<span class="fa fa-list"></span> '.Yii::t('study','My study mutual'), array(
                        'submit'=>Yii::app()->createUrl('mutual/my',array("index"=>$model->menu_id)),
                    ));
                ?>
            </div>
            <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
            <div class="btn-group pull-right" role="group">
                <?php
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('study','publish'), array(
                        'submit'=>Yii::app()->createUrl('mutual/add',array("index"=>$model->menu_id)),
                    ));
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <div class="falls" id="falls">
                <?php
                echo $model->echoMedia();
                ?>
            </div>
        </div>
    </div>
</section>
<?php
$url = Yii::app()->createUrl('mutual/pageAjax',array("index"=>$model->menu_id));
$js = "
var ajaxBool = true;
$('#falls').waterfall({
    itemCls: 'fall-div',
    colWidth: 370,  
    gutterWidth: 10,
    gutterHeight: 0,
    maxPage: {$model->maxPage},
    checkImagesLoaded: false,
    loadingMsg: '<div style=\"text-align: center;padding: 10px 0px;\">数据加载中.....</div>',
    dataType: 'html',
    path: function(page) {
        return '{$url}&page=' + page;
    }
});
";
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/waterfall.min.js", CClientScript::POS_END);//
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
