<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]

// 定义应用目录
define('APP_PATH', __DIR__ . '/app/');
// 定义基础路径
define('CSS_PATH', '/ihowqi/public/css');
define('IMG_PATH', '/ihowqi/public/imgs');
define('JS_PATH', '/ihowqi/public/js');
// 定义session
define('LOGIN_KEY_SESSION', 'userId');
// 与甲方约定的参数名称
define('PARAM_KEY', 'ihq_u');
// 会员访问基础链接
define('LINK_BASE_KEY', 'http://localhost/ihowqi/index.php/');
// 开启调试模式
define('APP_DEBUG', false);
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
