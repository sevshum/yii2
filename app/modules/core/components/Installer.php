<?php

namespace app\modules\core\components;


class Installer
{

    public static function run($event)
    {
        $root = str_replace('\\', '/', __DIR__ . '/../../');
        $envNames = ['dev', 'prod'];

        echo "Yii Application Initialization Tool v1.0\n\n";


        echo "Which environment do you want the application to be initialized in?\n\n";
        foreach ($envNames as $i => $name) {
            echo "  [$i] $name\n";
        }
        echo "\n  Your choice [0-" . (count($envNames) - 1) . ', or "q" to quit] ';
        $answer = self::getStdIn();

        if (!ctype_digit($answer) || !isset($envNames[$answer])) {
            echo "\n  Quit initialization.\n";
            exit(0);
        }

        $envName = $envNames[$answer];

        if (!in_array($envName, $envNames)) {
            $envsList = implode(', ', $envNames);
            echo "\n  $envName is not a valid environment. Try one of the following: $envsList. \n";
            exit(2);
        }

        echo "\n  Start initialization ...\n\n";
        $all = false;

        self::copyFile($root, "app/config/env/{$envName}.php", "app/config/env.php", $all, []);

        echo "\n  ... initialization completed.\n\n";
    }

    protected static function getStdIn()
    {
        $handle = fopen('php://stdin', 'r'); //trim(fgets(STDIN))
        return trim(fgets($handle));
    }

    protected static function copyFile($root, $source, $target, &$all, $params)
    {
        if (!is_file($root . '/' . $source)) {
            echo "       skip $target ($source not exist)\n";
            return true;
        }
        if (is_file($root . '/' . $target)) {
            if (file_get_contents($root . '/' . $source) === file_get_contents($root . '/' . $target)) {
                echo "  unchanged $target\n";
                return true;
            }
            if ($all) {
                echo "  overwrite $target\n";
            } else {
                echo "      exist $target\n";
                echo "            ...overwrite? [Yes|No|All|Quit] ";


                $answer = !empty($params['overwrite']) ? $params['overwrite'] : self::getStdIn();
                if (!strncasecmp($answer, 'q', 1)) {
                    return false;
                } else {
                    if (!strncasecmp($answer, 'y', 1)) {
                        echo "  overwrite $target\n";
                    } else {
                        if (!strncasecmp($answer, 'a', 1)) {
                            echo "  overwrite $target\n";
                            $all = true;
                        } else {
                            echo "       skip $target\n";
                            return true;
                        }
                    }
                }
            }
            file_put_contents($root . '/' . $target, file_get_contents($root . '/' . $source));
            return true;
        }
        echo "   generate $target\n";
        return copy($root . '/' . $source, $root . '/' . $target);
    }

}
