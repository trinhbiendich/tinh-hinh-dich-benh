<?php


namespace App\Utils;


use App\Controller\ApiController;
use Cake\Cache\Cache;
use Cake\Log\Log;

class RenderUtils {

    public static function processForData(array $params, array $dataFromStorage, $dataFromRequest, ApiController $triggerEvent){
        if (!isset($params[1]) || empty($params[1])) {
            foreach ($dataFromRequest as $key => $value) {
                if (!is_numeric($key)) {
                    $dataFromStorage[$key] = $value;
                    continue;
                }
                if (isset($value['id'])) {
                    $dataFromStorage[$value['id']] = $value;
                    continue;
                }
                if (in_array($value, $dataFromStorage)) {
                    continue;
                }
                array_push($dataFromStorage, $value);
            }
            Log::debug("saved " . count($dataFromStorage) . " items");
            self::renderData($params[0], $dataFromStorage, $triggerEvent, $dataFromRequest);
            return;
        }

        if (!isset($dataFromStorage[$params[1]])) {
            $dataFromStorage[$params[1]] = $dataFromRequest;
            self::renderData($params[0], $dataFromStorage, $triggerEvent, $dataFromRequest);
            return;
        }

        foreach ($dataFromRequest as $key => $value) {
            if (!is_numeric($key)) {
                $dataFromStorage[$params[1]][$key] = $value;
                continue;
            }
            if (isset($value['id'])) {
                $dataFromStorage[$params[1]][$value['id']] = $value;
                continue;
            }
            if (in_array($value, $dataFromStorage[$params[1]])) {
                continue;
            }
            array_push($dataFromStorage[$params[1]], $value);
        }
        self::renderData($params[0], $dataFromStorage, $triggerEvent, $dataFromRequest);
    }

    /**
     * @param $params
     * @param array $dataFromStorage
     * @param ApiController $triggerEvent
     * @param $dataFromRequest
     */
    public static function renderData($params, array $dataFromStorage, ApiController $triggerEvent, $dataFromRequest): void {
        Cache::write($params, $dataFromStorage);
        $triggerEvent->success($dataFromRequest);
    }
}
