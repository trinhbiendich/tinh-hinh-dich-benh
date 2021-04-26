<?php


namespace App\Utils;


use App\Controller\ApiController;
use Cake\Cache\Cache;

class BulkRenderUtils {

    public static function processForEvent($event, array $params, array $dataFromStorage, $data, ApiController $triggerEvent) {
        switch ($event) {
            case 'bulk':
                self::bulkInsert($params, $dataFromStorage, $data, $triggerEvent);
                return;
            default:
                $triggerEvent->error('there is no event with name ' . $event);
        }
    }

    private static function bulkInsert(array $params, array $dataFromStorage, array $data, ApiController $triggerEvent) {
        foreach ($data as $key => $value) {
            if (!is_numeric($key)) {
                $dataFromStorage[$params[1]][$key] = $value;
                continue;
            }
            if (in_array($value, $dataFromStorage[$params[1]])) {
                continue;
            }
            array_push($dataFromStorage[$params[1]], $value);
        }
        Cache::write($params[0], $dataFromStorage);
        $triggerEvent->success($data);
    }
}
