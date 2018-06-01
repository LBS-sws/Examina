<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class CategoryController extends Controller
{

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
                'expression'=>array('CategoryController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('CategoryController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('SS03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('SS03');
    }
    public function actionIndex($pageNum=0){
        $model = new CategoryList;
        if (isset($_POST['CategoryList'])) {
            $model->attributes = $_POST['CategoryList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['category_01']) && !empty($session['category_01'])) {
                $criteria = $session['category_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    public function actionNew()
    {
        $model = new CategoryForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new CategoryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new CategoryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    public function actionSave()
    {
        if (isset($_POST['CategoryForm'])) {
            $model = new CategoryForm($_POST['CategoryForm']['scenario']);
            $model->attributes = $_POST['CategoryForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('category/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    //刪除公司
    public function actionDelete(){
        $model = new CategoryForm('delete');
        if (isset($_POST['CategoryForm'])) {
            $model->attributes = $_POST['CategoryForm'];
            if($model->validateDelete()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
            }else{
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                $this->redirect(Yii::app()->createUrl('category/edit',array('index'=>$model->id)));
            }
        }
        $this->redirect(Yii::app()->createUrl('category/index'));
    }

    //刪除公司
    public function actionAjaxDepartment(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $city = $_POST['city'];
            $department = $_POST['department'];
            $arr = CategoryForm::searchDepartment($city,$department);
            echo CJSON::encode($arr);
        }else{
            echo "Error:404";
        }
        Yii::app()->end();
    }
}