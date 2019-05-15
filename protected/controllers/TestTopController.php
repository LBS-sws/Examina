<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class TestTopController extends Controller
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
                'actions'=>array('new','edit','save','delete','ajaxDepartment'),
                'expression'=>array('TestTopController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('TestTopController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('ajaxStaff'),
                'expression'=>array('TestTopController','allowRead'),
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

    public static function allowRead() {
        return true;
    }
    public function actionIndex($pageNum=0){
        $model = new TestTopList;
        if (isset($_POST['TestTopList'])) {
            $model->attributes = $_POST['TestTopList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['testTop_01']) && !empty($session['testTop_01'])) {
                $criteria = $session['testTop_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new TestTopForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new TestTopForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new TestTopForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['TestTopForm'])) {
            $model = new TestTopForm($_POST['TestTopForm']['scenario']);
            $model->attributes = $_POST['TestTopForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('testTop/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除測驗單
    public function actionDelete(){
        $model = new TestTopForm('delete');
        if (isset($_POST['TestTopForm'])) {
            $model->attributes = $_POST['TestTopForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                $this->redirect(Yii::app()->createUrl('testTop/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('testTop/index'));
    }

    //所有城市列表
    public function getAllCityList(){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array(""=>"全部");
        $rows = Yii::app()->db->createCommand()->select()->from("security$suffix.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["code"]] = $row["name"];
            }
        }
        return $arr;
    }

    //員工列表的異步請求
    public function actionAjaxStaff(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $city = $_POST['city'];
            $list =array("status"=>0);
            $arr = TestTopForm::getAllStaffList($city);
            if(!empty($arr)){
                $list = array("status"=>1,"data"=>$arr);
            }
            echo CJSON::encode($list);
        }else{
            $this->redirect(Yii::app()->createUrl('testTop/index'));
        }
    }

    //部門的異步請求
    public function actionAjaxDepartment(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $department = $_POST['department'];
            $arr = TestTopForm::searchDepartment($department);
            echo CJSON::encode($arr);
        }else{
            echo "Error:404";
        }
        Yii::app()->end();
    }
}