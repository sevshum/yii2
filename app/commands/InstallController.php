<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Yii;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InstallController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex()
    {
        $config = require(Yii::getAlias('@app/config') . '/web.php');
        $modules = isset($config['modules']) ? $config['modules'] : [];
        foreach ($modules as $key => $value) {
            $name = is_integer($key) ? $value : $key;
            $alias = '@app/modules/' . $name . '/migrations';
            if (is_dir(Yii::getAlias($alias, false))) {
                $this->run('migrate/up', ['migrationPath' => $alias, 'interactive' => 0]);
            }
        }
        $this->run('migrate/up', ['interactive' => 0]);
    }
}

