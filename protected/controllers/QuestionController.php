<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class QuestionController extends Controller
{
	public $function_id='SS02';

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
                'actions'=>array('new','edit','save','delete','ImportQuestion'),
                'expression'=>array('QuestionController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('QuestionController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS02');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS02');
    }
    public function actionIndex($pageNum=0,$index = 0){
        $model = new QuestionList;
        if(!is_numeric($index)||empty($index)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        if (isset($_POST['QuestionList'])) {
            $model->attributes = $_POST['QuestionList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['question_01']) && !empty($session['question_01'])) {
                $criteria = $session['question_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->index = $index;
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($quiz_id)
    {
        if(!is_numeric($quiz_id)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        $model = new QuestionForm('new');
        $model->quiz_id = $quiz_id;
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index,$quiz_id)
    {
        if(!is_numeric($quiz_id)){
            throw new CHttpException(404,'The requested page does not exist.');
        }
        $model = new QuestionForm('edit');
        $model->quiz_id = $quiz_id;
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
        $model = new QuestionForm('view');
        $model->quiz_id = $quiz_id;
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['QuestionForm'])) {
            $model = new QuestionForm($_POST['QuestionForm']['scenario']);
            $model->attributes = $_POST['QuestionForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('question/edit',array('index'=>$model->id,'quiz_id'=>$model->quiz_id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除試題
    public function actionDelete(){
        $model = new QuestionForm('delete');
        if (isset($_POST['QuestionForm'])) {
            $model->attributes = $_POST['QuestionForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                $this->redirect(Yii::app()->createUrl('question/edit',array('index'=>$model->id,'quiz_id'=>$model->quiz_id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('question/index',array('index'=>$model->quiz_id)));
    }

    //導入
    public function actionImportQuestion(){
        $model = new UploadExcelForm();
        $model->attributes = $_POST['UploadExcelForm'];
        $img = CUploadedFile::getInstance($model,'file');
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
        if(empty($img)){
            Dialog::message(Yii::t('dialog','Validation Message'), "文件不能为空");
            $this->redirect(Yii::app()->createUrl('question/index',array('index'=>$model->quiz_id)));
        }
        $url = "upload/excel/".$city."/".date("YmdHis").".".$img->getExtensionName();
        $model->file = $img->getName();
        if ($model->file && $model->validate()) {
            $img->saveAs($url);
            $loadExcel = new LoadExcel($url);
            $list = $loadExcel->getExcelList();
            $model->loadGoods($list);
            $this->redirect(Yii::app()->createUrl('question/index',array('index'=>$model->quiz_id)));
        }else{
            $message = CHtml::errorSummary($model);
            Dialog::message(Yii::t('dialog','Validation Message'), $message);
            $this->redirect(Yii::app()->createUrl('question/index',array('index'=>$model->quiz_id)));
        }
    }
}