<?php


namespace App\Controller;


use App\Utils\StringUtils;
use Cake\Event\EventInterface;

class RestController extends AppController {
    public function initialize(): void {
        parent::initialize();
        $this->RequestHandler->renderAs($this, 'json');
    }

    public function beforeFilter(EventInterface $event) {
        parent::beforeFilter($event);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
        header("Access-Control-Allow-Headers: X-Requested-With, content-type, xxx-sec, access-control-allow-methods, access-control-allow-headers, access-control-allow-origin, access-control-allow-credentials");

        if (strtolower($this->request->getMethod()) == 'get') {
            return;
        }

        $secStr = $this->request->getHeader('xxx-sec');
        if (StringUtils::isBlank($secStr)) {
            $this->denied($secStr, true);
            exit;
        }

        if ($secStr[0] != "opencms") {
            $this->denied($secStr, true);
            exit;
        }
    }

    protected function invalid($param) {
        if (!isset($param)) {
            return true;
        }

        if (!is_array($param)) {
            return true;
        }

        if (empty($param[0])) {
            return true;
        }

        return false;
    }

    protected function error($data) {
        $this->set('data', [
            "type" => "error",
            "data" => $data
        ]);
        $this->set('_serialize', 'data');
    }

    protected function correctedParam(array $param) {
        if ($param == null || !is_array($param) || count($param) == 2) {
            return $param;
        }
        $params = [$param[0]];
        $arr = [];
        for ($i = 1; $i < count($param); $i++) {
            $arr[] = $param[$i];
        }
        $params[] = implode("::", $arr);
        return $params;
    }

    protected function denied($msg = "This method are not allowed", $isRender = false) {
        $data = [
            "type" => "denied",
            "data" => $msg
        ];
        if ($isRender) {
            header('Content-Type: application/json');
            echo json_encode($data);
            return;
        }
        $this->set('data', $data);
        $this->set('_serialize', 'data');
    }

    protected function success($data) {
        $this->set('data', [
            "type" => "success",
            "data" => $data
        ]);
        $this->set('_serialize', 'data');
    }
}
