<?php


namespace App\Controller;


use App\Utils\BulkRenderUtils;
use App\Utils\RenderUtils;
use Cake\Cache\Cache;
use Cake\Log\Log;

class ApiController extends RestController {

    public function show(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $params = $this->correctedParam($param);

        $eventName = $this->request->getQuery("event", "");
        switch ($eventName) {
            case BulkRenderUtils::$BULK_PHOTOS:
                BulkRenderUtils::renderBulkPhotos($params, $this->request, $this);
                return;
            case BulkRenderUtils::$BULK:
                BulkRenderUtils::renderBulk($params, $this->request, $this);
                return;
        }

        $data = Cache::read($params[0]);
        if ($data == null) {
            $this->error($params[0] . " not found");
            return;
        }
        if (!isset($params[1]) || empty($params[1])) {
            $this->success($data);
            return;
        }

        if (isset($data[$params[1]])) {
            $data = $data[$params[1]];
            $this->success($data);
            return;
        }
        $this->error($params[1] . " do not found on " . $params[0]);
    }

    public function add(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $params = $this->correctedParam($param);

        $dataFromStorage = Cache::read($params[0]);
        if ($dataFromStorage == null) {
            $dataFromStorage = [];
        }
        $dataFromRequest = $this->request->getParsedBody();
        if (count($dataFromRequest) == 0) {
            $this->error("data invalid");
        }

        Log::debug("paths: " . implode(" => ", $params));

        if (isset($dataFromRequest['event'])) {
            $event = $dataFromRequest['event'];

            if (!isset($dataFromRequest['data'])) {
                $this->error("data invalid");
                return;
            }

            BulkRenderUtils::processForEvent($event, $params, $dataFromStorage, $dataFromRequest['data'], $this);
        } else {
            RenderUtils::processForData($params, $dataFromStorage, $dataFromRequest, $this);
        }
    }

    public function del(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $params = $this->correctedParam($param);

        $data = Cache::read($params[0]);
        if ($data == null) {
            $this->error($params[0] . " not found");
            return;
        }
        if (!isset($param[1]) || empty($params[1])) {
            Cache::delete($params[0]);
            $this->success("delete " . $params[0] . " success");
            return;
        }
        if (!isset($data[$params[1]])) {
            $this->error($params[1] . " do not found on " . $params[0]);
            return;
        }
        unset($data[$params[1]]);
        Cache::write($params[0], $data);
        $this->success($data);
    }



    public function options(...$param) {
        $this->success($param);
    }
}
