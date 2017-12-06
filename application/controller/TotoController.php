<?php
namespace Utm\Controller;

class TotoController extends \Utm\CoreController
{    
    public function index() {
        $App = \Utm\CoreModel::factory('App');
        var_dump($App->getProductList());
        echo 'toto::index';
    }
}
