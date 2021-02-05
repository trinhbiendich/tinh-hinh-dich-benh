<?php


namespace App\Controller;


use Cake\Cache\Cache;

class ApiController extends RestController {

    public function show(...$param) {
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
        if (isset($params[1]) && !empty($params[1]) && isset($data[$params[1]])) {
            $data = $data[$param[1]];
        }
        if ($data == null) {
            $this->error($params[1] . " do not found on " . $params[0]);
            return;
        }
        $this->success($data);
    }

    public function add(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $params = $this->correctedParam($param);

        $data = Cache::read($params[0]);
        if ($data == null) {
            $data = [];
        }
        $saveData = $this->request->getParsedBody();
        if (count($saveData) == 0) {
            $this->error("data invalid");
        }
        if (isset($params[1]) && !empty($params[1])) {
            $data[$params[1]] = $saveData;
        } else {
            $data = $saveData;
        }
        Cache::write($params[0], $data);
        $this->success($saveData);
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




}
