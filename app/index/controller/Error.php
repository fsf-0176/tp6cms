<?php

namespace app\index\controller;

class Error
{
    public function __call($method, $args)
    {
        echo '控制器为空哦';
        return;
    }
}
