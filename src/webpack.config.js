var path = require('path');

module.exports = {
    // 入口
    entry: {       
        index: './components/index',
    },
    // 输出
    output: {
        path: '../public/js/app/',
        filename: '[name].js'
    },
    module: {
        // 加载器
        loaders: [
            { test: /\.vue$/, loader: 'vue' },
            { test: /\.js$/, loader: 'babel', exclude: /node_modules/ },
            { test: /\.(png|jpg|gif)$/, loader: 'url-loader'}
        ]
    },
    vue: {
        loaders: {
            css: 'style!css!autoprefixer!less'
        }
    },
    babel: { 
        presets: ['es2015'],
        plugins: ['transform-runtime']
    },
    resolve: {
        // require时省略的扩展名，如：require('module') 不需要module.js
        extensions: ['', '.js', '.vue']
    }
};
