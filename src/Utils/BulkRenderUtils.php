<?php


namespace App\Utils;


use App\Controller\ApiController;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;

class BulkRenderUtils {
    public static $BULK_PHOTOS = "bulk_photos";
    public static $BULK = "bulk";

    public static function processForEvent($event, array $params, array $dataFromStorage, $data, ApiController $triggerEvent) {
        switch ($event) {
            case self::$BULK_PHOTOS:
                self::bulkInsertPhotos($params[0], $dataFromStorage, $data, $triggerEvent);
                return;
            case self::$BULK:
                self::bulkInsert($params[0], $dataFromStorage, $data, $triggerEvent);
                return;
            default:
                $triggerEvent->error('there is no event with name ' . $event);
        }
    }

    private static function bulkInsertPhotos($params, array $dataFromStorage, array $data, ApiController $triggerEvent) {
        $counter = 0;
        $ids = [];
        foreach ($data as $item) {
            if (!isset($item['id']) || empty($item['id'])) {
                continue;
            }
            Cache::write("photos_" . $item['id'], $item);
            array_push($ids, $item['id']);
            $counter++;
        }

        $dataFromStorage = array_unique(array_merge($dataFromStorage, $ids));
        Cache::write($params, $dataFromStorage);
        $triggerEvent->success(["msg" => "save success $counter items into storage", "ids" => $ids]);
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

    public static function renderBulkPhotos(array $params, ServerRequest $request, ApiController $triggerEvent) {
        $ids = $request->getQuery("ids", "");
        if (empty($ids)) {
            $triggerEvent->error("there are no ids on API");
            return;
        }
        $idsInArr = explode(",", $ids);
        $photos = [];
        foreach ($idsInArr as $id) {
            $photo = Cache::read("photos_$id");
            if ($photo !== null) {
                $photos[] = $photo;
            }
        }
        $triggerEvent->success($photos);
    }

    public static function renderBulk(array $params, ServerRequest $request, ApiController $triggerEvent) {
        $keys = $request->getQuery("keys", "");
        if (empty($keys)) {
            $triggerEvent->error("there are no keys on API");
            return;
        }
        $keysInArr = explode(",", $keys);
        $objs = [];
        foreach ($keysInArr as $key) {
            $obj = Cache::read($key);
            if ($obj !== null) {
                $objs[$key] = $obj;
            }
        }
        $triggerEvent->success($objs);
    }
}
