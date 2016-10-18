<?php

namespace app\modules\core\components\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Menu;


class SideMenu extends Menu
{
    public $encodeLabels = false;
    public $activateParents = true;
    public $submenuTemplate = "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n";
    
    public function run()
    {
        parent::run();
//        $this->getView()->registerJs("$('#side-menu').metisMenu();");
    }


    protected function renderItems($items)
    {
        $n = count($items);
        $lines = [];
        foreach ($items as $i => $item) {
            $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
            if (!empty($item['items'])) {
                $item['label'] .= ' <i class="fa fa-angle-left pull-right"></i>';
                Html::addCssClass($options, 'treeview');
                foreach ($item['items'] as &$it) {
                    $it['icon'] = 'fa fa-angle-double-right';
                }
            }
            if (isset($item['icon'])) {
                $item['label'] = Html::tag('i', '', ['class' => $item['icon']]) . ' ' . $item['label'];
            }
            $tag = ArrayHelper::remove($options, 'tag', 'li');
            $class = [];
            if ($item['active']) {
                $class[] = $this->activeCssClass;
            }
            if ($i === 0 && $this->firstItemCssClass !== null) {
                $class[] = $this->firstItemCssClass;
            }
            if ($i === $n - 1 && $this->lastItemCssClass !== null) {
                $class[] = $this->lastItemCssClass;
            }
            if (!empty($class)) {
                if (empty($options['class'])) {
                    $options['class'] = implode(' ', $class);
                } else {
                    $options['class'] .= ' ' . implode(' ', $class);
                }
            }
            $menu = $this->renderItem($item);
            if (!empty($item['items'])) {
                $menu .= strtr($this->submenuTemplate, [
                    '{items}' => $this->renderItems($item['items']),
                ]);
            }
            $lines[] = Html::tag($tag, $menu, $options);
        }

        return implode("\n", $lines);
    }
    
    /**
     * 
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            $route = ltrim($route, '/');

            if ($route !== $this->route) {
                $chunk = explode('/', $this->route);
                array_pop($chunk);
                return strpos($route, implode('/', $chunk)) === 0;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                foreach (array_splice($item['url'], 1) as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

}
