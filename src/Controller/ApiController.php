<?php


namespace App\Controller;


use Cake\Cache\Cache;
use Cake\Http\Client\Request;
use Cake\Http\Response;

class ApiController extends RestController {

    public function show(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $data = Cache::read($param[0]);
        if ($data == null) {
            $this->error($param[0] . " not found");
            return;
        }
        if (isset($param[1]) && !empty($param[1])) {
            $data = $data[$param[1]];
        }
        if ($data == null) {
            $this->error($param[1] . " do not found on " . $param[0]);
            return;
        }
        $this->success($data);
    }

    public function add(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $data = Cache::read($param[0]);
        if ($data == null) {
            $data = [];
        }
        $saveData = $this->request->getParsedBody();
        if (count($saveData) == 0) {
            $this->error("data invalid");
        }
        if (isset($param[1]) && !empty($param[1])) {
            $data[$param[1]] = $saveData;
        } else {
            $data = $saveData;
        }
        Cache::write($param[0], $data);
        $this->success($saveData);
    }

    public function del(...$param) {
        if ($this->invalid($param)) {
            $this->error("path invalid or not found");
            return;
        }
        $data = Cache::read($param[0]);
        if ($data == null) {
            $this->error($param[0] . " not found");
            return;
        }
        if (!isset($param[1]) || empty($param[1])) {
            Cache::delete($param[0]);
            $this->success("delete " . $param[0] . " success");
            return;
        }
        if (!isset($data[$param[1]])) {
            $this->error($param[1] . " do not found on " . $param[0]);
            return;
        }
        unset($data[$param[1]]);
        Cache::write($param[0], $data);
        $this->success($data);
        return;
    }




}
