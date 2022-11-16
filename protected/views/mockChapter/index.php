<?php
if($this->function_id!=$model->menu_code){
    $this->redirect(Yii::app()->createUrl('site/index'));
}
$this->pageTitle=Yii::app()->name . ' - Mock test';
?>
<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/study.css?1.1");//
?>
<section class="content-header">
    <h1><?php echo Yii::t("app","Mock test");?><small>模拟测试不会保存到“测验统计”及“我的测验记录”，但错题会保存到“我的错题集”</small></h1>
	<ol class="breadcrumb">
		<li><?php echo $model->menu_name;?></li>
		<li class="active"><?php echo Yii::t("app","Mock test");?></li>
	</ol>
</section>

<section class="content">
	<div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button(Yii::t('study','all mock chapter'), array(
                    'submit'=>Yii::app()->createUrl('ChapterArticle/testAll',array("menu_id"=>$model->menu_id)),
                ));
                ?>
            </div>
            <?php if (Yii::app()->user->validRWFunction($model->menu_code)): ?>
            <div class="btn-group pull-right" role="group">
                <?php
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                        'submit'=>Yii::app()->createUrl('MockChapter/add',array("index"=>$model->menu_id)),
                    ));
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="box">
        <div class="box-body">
            <h4><?php echo Yii::t('study','mock chapter');?></h4>
            <?php
            echo $model->echoMedia();
            ?>
        </div>
    </div>

    <?php
    if (Yii::app()->user->validRWFunction($model->menu_code)){
        echo $model->echoNoneDiv();
    }
    ?>
</section>