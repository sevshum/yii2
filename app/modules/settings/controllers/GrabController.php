<?php


namespace app\modules\settings\controllers;

use app\modules\settings\models\Item;
use Yii;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\FileHelper;

/**
 * Extracts messages to be translated from source files.
 *
 * The extracted messages can be saved the following depending on `format`
 * setting in config file:
 *
 * - PHP message source files.
 * - ".po" files.
 * - Database.
 *
 * Usage:
 * 1. Create a configuration file using the 'message/config' command:
 *    yii message/config /path/to/myapp/messages/config.php
 * 2. Edit the created config file, adjusting it for your web application needs.
 * 3. Run the 'message/extract' command, using created config:
 *    yii message /path/to/myapp/messages/config.php
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GrabController extends Controller
{
    /**
     * @var string controller default action ID.
     */
    public $defaultAction = 'run';

    /**
     * Creates a configuration file for the "extract" command.
     *
     * The generated configuration file contains detailed instructions on
     * how to customize it to fit for your needs. After customization,
     * you may use this configuration file with the "extract" command.
     *
     * @param string $filePath output file name or alias.
     * @throws Exception on failure.
     */
    public function actionConfig($filePath)
    {
        $filePath = Yii::getAlias($filePath);
        if (file_exists($filePath)) {
            if (!$this->confirm("File '{$filePath}' already exists. Do you wish to overwrite it?")) {
                return self::EXIT_CODE_NORMAL;
            }
        }
        copy(dirname(__FILE__) . DIRECTORY_SEPARATOR .  'config.php', $filePath);
        echo "Configuration file template created at '{$filePath}'.\n\n";
    }

    /**
     * Extracts messages to be translated from source code.
     *
     * This command will search through source code files and extract
     * messages that need to be translated in different languages.
     *
     * @param string $configFile the path or alias of the configuration file.
     * You may use the "yii message/config" command to generate
     * this file and then customize it for your needs.
     * @throws Exception on failure.
     */
    public function actionRun($configFile = null)
    {
        $configFile = $configFile === null ? (__DIR__ . '/config.php') : Yii::getAlias($configFile);
        if (!is_file($configFile)) {
            throw new Exception("The configuration file does not exist: $configFile");
        }

        $config = array_merge([
            'translator' => 's',
            'removeUnused' => false,
        ], require($configFile));

        if (!isset($config['sourcePath'])) {
            throw new Exception('The configuration file must specify "sourcePath".');
        }
        if (!is_dir($config['sourcePath'])) {
            throw new Exception("The source path {$config['sourcePath']} is not a valid directory.");
        }

        $files = FileHelper::findFiles(realpath($config['sourcePath']), $config);

        $messages = [];
        foreach ($files as $file) {
            $messages = array_merge($messages, $this->extractMessages($file, $config['translator']));
        }
        $this->saveMessages($messages, $config);
    }

    

    /**
     * Extracts messages from a file
     *
     * @param string $fileName name of the file to extract messages from
     * @param string $translator name of the function used to translate messages
     * @return array
     */
    protected function extractMessages($fileName, $translator)
    {
        echo "Extracting settings from $fileName...\n";
        $subject = file_get_contents($fileName);
        $messages = [];
        if (!is_array($translator)) {
            $translator = [$translator];
        }
        foreach ($translator as $currentTranslator) {
            $n = preg_match_all(
                '/\b' . $currentTranslator . '\s*\(\s*[\'"]([\w\_\.\-]+)[\'"]\s*(,\s*[\'"]?(.*)[\'"]?)?\s*\)/U',
                $subject,
                $matches,
                PREG_SET_ORDER
            );
            for ($i = 0; $i < $n; ++$i) {
                $chunk = $matches[$i];
                $key = trim($chunk[1]);
                $keys = explode('.', $key, 2);
                if (count($keys) < 2) {
                    continue;
                }
                $value = isset($chunk[3]) ? trim(trim($chunk[3]), "'\"") : null;
                $messages[$key] = [
                    'group' => $keys[0],
                    'key' => $keys[1],
                    'value' => $value,
                ];
            }
        }

        return $messages;
    }
    
    public function saveMessages($messages, $config)
    {
        if (!count($messages)) {
            echo "Nothing to save.\n";
            return;
        }
        $params = [];
        $values = [];
        $i = 0;
        foreach ($messages as $item) {
            $params[':k' . $i] = $item['key'];
            $params[':v' . $i] = $item['value'];
            $params[':g' . $i] = $item['group'];
            $values[] = "(:g{$i}, :k{$i}, :v{$i})";
            ++$i;
        }
        $res = Item::getDb()->createCommand(
            'INSERT IGNORE INTO `' . Item::tableName() . '` (`group`, `key`, `value`) VALUES ' . implode(',', $values), 
            $params
        )->execute();
        echo "Successfully saved {$res} settings.\n";
    }

    
}
