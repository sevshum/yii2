<?php
namespace app\modules\menu\controllers\backend;

use app\modules\core\components\BackendController;
use app\modules\menu\models\CategoryItem;
use app\modules\menu\models\Menu;
use app\modules\menu\models\MenuItem;
use Yii;
use yii\data\ArrayDataProvider;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;


class ItemsController extends BackendController 
{    
    public function actionIndex() 
    {
        $request = Yii::$app->getRequest();
        $id = $request->get('id');
        $menu = $this->loadModel(Menu::className(), $id);
        $provider = $this->_getProvider($menu->getTree());
        if ($request->getIsAjax()) {
            return $this->render('index', compact('menu', 'provider'));
        }
        return $this->render('index', compact('menu', 'provider'));
    }
    
    public function actionEdit($menuId, $id = null)
    {
        $menu = $this->loadModel(Menu::className(), $menuId);
        if ($id == null) {
            $item = new MenuItem;
        } else {
            $item = $this->loadModel(MenuItem::className(), $id);
            $item->populateParent();
        }
        $item->menu_id = $menu->id;
        $ajax = false;
        if ($item->load($_POST)) {
            if ($item->validate()) {
                $response = ['success' => false, 'target' => '#menu-' . $menuId];
                $node = $this->loadModel(MenuItem::className(), $item->parent_id);
                if ($item->getIsNewRecord()) {
                    if ($item->appendTo($node)) {
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully created.'));
                        $response['success'] = true;
                    }
                } else {
                    if ($node->equals($item->parents()->one())) {
                        $item->save(false);
                    } else {
                        $item->appendTo($node);
                    }
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully updated.'));
                    $response['success'] = true;
                }
                if ($response['success']) {
                    $response['html'] = $this->renderPartial('_list', array(
                        'menu' => $menu, 'provider' => $this->_getProvider($menu->getTree())
                    ));
                    
                    return $this->renderJson($response);
                }
                
            } else {
                $ajax = true;
            }
        }
        $params = [
            'item' => $item,
            'parents' => $menu->listData() 
        ];
        if ($ajax) {
            return $this->renderJson(array(
                'success' => false,
                'target' => '#menu-item-' . $item->id,
                'html' => $this->renderPartial('_form', $params)
            ));
        } else {
            return $this->render('_form', $params);            
        }
    }
    
    public function actionMove($id, $dir)
    {
        if (Yii::$app->getRequest()->getIsAjax()) {
            $current = $this->loadModel(MenuItem::className(), $id);
            if ($dir == $current::MOVE_UP) {
                $prev = $current->prev()->one();
                if ($prev) {
                    $current->insertBefore($prev, false);
                }
            } elseif ($dir == $current::MOVE_DOWN) {
                $next = $current->next()->one();
                if ($next) {
                    $current->insertAfter($next, false);
                }
            }
            $menu = $current->menu;        
            return $this->renderJson(array(
                'html' => $this->renderPartial('_list', array(
                    'menu' => $menu, 'provider' => $this->_getProvider($menu->getTree())
                )),
                'success' => true,
                'target' => '#menu-' . $menu->id
            ));
        } else {
            throw new BadRequestHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionDelete($id)
    {
        $model = $this->loadModel(MenuItem::className(), $id);
        $model->delete();
        return $this->redirect(['/menu/menus/admin', 'menu_id' => $model->menu_id]);
    }
    
    protected function _getProvider($data)
    {
        unset($data[0]);
        return new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => array('pageSize' => 1000)
        ]);
    }
    
    public function actionSuggest()
    {
        if (isset($_GET['term']) && ($q = trim($_GET['term'])) !== '') {
            $params = ['lang' => isset($_GET['lang']) ? $_GET['lang'] : null];
            
            return Json::encode(Yii::$app->getModule('search')->suggest($q, $params));
        }
    }
}
