<?php


namespace App\Controller;


class RestController extends AppController {
    public function initialize(): void {
        parent::initialize();
        $this->RequestHandler->renderAs($this, 'json');
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

    protected function success($data) {
        $this->set('data', [
            "type" => "success",
            "data" => $data
        ]);
        $this->set('_serialize', 'data');
    }
}
