<?php


namespace App\Utils;


use App\Controller\ApiController;
use Cake\Cache\Cache;

class BulkRenderUtils {

    public static function processForEvent($event, array $params, array $dataFromStorage, $data, ApiController $triggerEvent) {
        switch ($event) {
            case 'bulk':
                self::bulkInsert($params[0], $dataFromStorage, $data, $triggerEvent);
                return;
            default:
                $triggerEvent->error('there is no event with name ' . $event);
        }
    }

    private static function bulkInsert($params, array $dataFromStorage, array $data, ApiController $triggerEvent) {
        $counter = 0;
        $ids = [];
        foreach ($data as $item) {
            if (!isset($item['id']) || empty($item['id'])) {
                continue;
            }
            Cache::write($params . "_" . $item['id'], $item);
            array_push($ids, $item['id']);
            $counter++;
        }

        $dataFromStorage = array_unique(array_merge($dataFromStorage, $ids));
        Cache::write($params, $dataFromStorage);
        $triggerEvent->success(["msg" => "save success $counter items into storage", "ids" => $ids]);
    }
}
