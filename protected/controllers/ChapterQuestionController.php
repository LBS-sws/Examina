<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class ChapterQuestionController extends Controller
{
	public $function_id;

    public function init(){
        $session = Yii::app()->session;
        if(key_exists("menu_code",$_GET)){
            $code = $_GET["menu_code"];
            $session["menu_code"]=$code;
        }elseif (isset($session['menu_code']) && !empty($session['menu_code'])) {
            $code = $session['menu_code'];
        }else{
            $code = "无";
        }
        $this->function_id = "{$code}02";
        parent::init();
    }

    public function filters()
    {
        return array(
            'enforceSessionExpiration',
            'enforceNoConcurrentLogin',
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('new','edit','save','delete','upload'),
                'expression'=>array('ChapterQuestionController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('ChapterQuestionController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('download'),
                'expression'=>array('ChapterQuestionController','allowAll'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowAll() {
        return true;
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}01");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}01");
    }

    public function actionIndex($pageNum=0,$chapter_id = 0){
        $model = new ChapterQuestionList;
        if(!is_numeric($chapter_id)||empty($chapter_id)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        if (isset($_POST['ChapterQuestionList'])) {
            $model->attributes = $_POST['ChapterQuestionList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['chapterQuestion_'.$this->function_id]) && !empty($session['chapterQuestion_'.$this->function_id])) {
                $criteria = $session['chapterQuestion_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($chapter_id,$model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($chapter_id)
    {
        if(!is_numeric($chapter_id)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        $model = new ChapterQuestionForm('new');
        if (!$model->retrieveChapterData($chapter_id)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new ChapterQuestionForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index,$quiz_id)
    {
        if(!is_numeric($quiz_id)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        $model = new ChapterQuestionForm('view');
        $model->quiz_id = $quiz_id;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['ChapterQuestionForm'])) {
            $model = new ChapterQuestionForm($_POST['ChapterQuestionForm']['scenario']);
            $model->attributes = $_POST['ChapterQuestionForm'];
            if ($model->validate()&&$this->function_id==$model->menu_code) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('chapterQuestion/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除試題
    public function actionDelete(){
        $model = new ChapterQuestionForm('delete');
        if (isset($_POST['ChapterQuestionForm'])) {
            $model->attributes = $_POST['ChapterQuestionForm'];
            if($model->validate()&&$this->function_id==$model->menu_code){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('chapterQuestion/index',array('chapter_id'=>$model->chapter_id)));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                $this->redirect(Yii::app()->createUrl('chapterQuestion/edit',array('index'=>$model->id)));
            }
        }
    }

    //導入
    public function actionUpload(){
        $model = new ChapterExcelForm();
        $model->attributes = $_POST['ChapterQuestionList'];
        $model->file = CUploadedFile::getInstanceByName('file');
        if ($model->validate()) {
            $city = Yii::app()->user->city();
            $uid = Yii::app()->user->id;
            $url = "upload/excel/".$city."/".$uid.".".$model->file->getExtensionName();
            $model->file->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadGoods($list);
            $this->redirect(Yii::app()->createUrl('chapterQuestion/index',array('chapter_id'=>$model->chapter_id)));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('chapterQuestion/index',array('chapter_id'=>$model->chapter_id)));
        }
    }

    public function actionDownload(){
        $file_name = "chapter_model.xlsx"; //下载文件名
        $file_dir = $path =Yii::app()->basePath."/commands/template/"; //下载文件存放目录
        if (! file_exists ( $file_dir . $file_name )) {
            header('HTTP/1.1 404 NOT FOUND');
            echo "404 NOT FOUND";
        } else {
            $file = fopen($file_dir . $file_name, "rb");
            Header("Content-type: application/octet-stream");
            Header("Accept-Ranges: bytes");
            Header("Accept-Length: " . filesize($file_dir . $file_name));
            Header("Content-Disposition: attachment; filename=" . $file_name);
            echo fread($file, filesize($file_dir . $file_name));
            fclose($file);
        }
        exit ();
    }
}