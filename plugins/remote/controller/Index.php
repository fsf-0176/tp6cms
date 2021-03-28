<?php

namespace plugins\remote\controller;
use think\facade\View;
class Index
{
    public function link()
    {
        hook('remotehook');
        return View::fetch('info');
    }
}
