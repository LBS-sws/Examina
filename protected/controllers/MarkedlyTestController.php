<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class MarkedlyTestController extends Controller
{
    public $function_id='TE09';

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
        $this->function_id = "{$code}07";
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
                'actions'=>array('new','edit','save','delete','ajaxArticle'),
                'expression'=>array('MarkedlyTestController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('MarkedlyTestController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('ajaxStaff'),
                'expression'=>array('MarkedlyTestController','allowRead'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}07");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}07");
    }

    public static function allowRead() {
        return true;
    }

    public function actionIndex($index,$pageNum=0){
        $model = new MarkedlyTestList;
        if (isset($_POST['MarkedlyTestList'])) {
            $model->attributes = $_POST['MarkedlyTestList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['markedlyTest_'.$this->function_id]) && !empty($session['markedlyTest_'.$this->function_id])) {
                $criteria = $session['markedlyTest_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveAll($index,$model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew($menu_id)
    {
        $model = new MarkedlyTestForm('new');
        if (!$model->retrieveNewData($menu_id)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionEdit($index)
    {
        $model = new MarkedlyTestForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new MarkedlyTestForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['MarkedlyTestForm'])) {
            $model = new MarkedlyTestForm($_POST['MarkedlyTestForm']['scenario']);
            $model->attributes = $_POST['MarkedlyTestForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('markedlyTest/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除測驗單
    public function actionDelete(){
        $model = new MarkedlyTestForm('delete');
        if (isset($_POST['MarkedlyTestForm'])) {
            $model->attributes = $_POST['MarkedlyTestForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                $this->redirect(Yii::app()->createUrl('markedlyTest/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('markedlyTest/index'));
    }

    //部門的異步請求
    public function actionAjaxArticle(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $article = key_exists("article",$_POST)?$_POST['article']:0;
            $menu_id = key_exists("menu_id",$_POST)?$_POST['menu_id']:0;
            $arr = MarkedlyTestForm::searchArticle($article,$menu_id);
            echo CJSON::encode($arr);
        }else{
            echo "Error:404";
        }
        Yii::app()->end();
    }
}