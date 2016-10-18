<?php
namespace app\modules\core\traits;

trait DynamicQueryTrait
{
    /**
     * @param array $params
     * @return static
     */
    public function byDynamicParams($params)
    {
        $i = 0;
        $class = $this->modelClass;
        $table = $class::tableName();
        foreach ($params as $key => $value) {
            $i++;
            if (isset($class::$dynamicOptions[$key]) && !empty($value)) {
                $this->innerJoin(
                    "`" . $class::$dynamicParamsTable . "` AS do$i", 
                    "`$table`.id = do$i.entity_id AND do$i.entity_type = :t$i AND do$i.key = '$key' AND do$i.value = :do$i ", 
                    [":do$i" => $value, ":t$i" => $class]
                );
            }
        }
        return $this;
    }
}
