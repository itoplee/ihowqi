<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[project]'     => [
        ':s/:p/:u'   => ['admin/project/project', ['method' => 'get']]
    ],
    '[outlink]'     => [
        ':k/:u'   => ['admin/project/outlink', ['method' => 'get']]
    ],
    '[innerlink]'     => [
        ':p/:u'   => ['admin/project/innerlink', ['method' => 'get']]
    ],
];
