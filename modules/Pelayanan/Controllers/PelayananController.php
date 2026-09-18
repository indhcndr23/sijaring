<?php

namespace Modules\Pelayanan\Controllers;

use Modules\Pelayanan\Controllers\BaseController as Controller;

class PelayananController extends Controller
{
    public function index() {
        $data = [
            'featureName' => 'Pelayanan',
            'user' => $this->auth->user()
        ];

        return $this->render("index", $data);
    }
}
