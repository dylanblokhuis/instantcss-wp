const path = require('path');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: './assets/src/editor.js',
    output: {
        path: path.join(__dirname, 'assets/dist'),
        filename: '[name].bundle.js',
    },
    node: {
        fs: 'empty'
    },
    module: {
        rules: [
            {
                test: /\.css$/,
                use: [
                    {
                        loader: 'style-loader',
                    },
                    {
                        loader: 'css-loader',
                    },
                ]
            },
            {
                test: /\.ttf$/,
                use: ['file-loader']
            },
            {
                test: /\.m?js$/,
                exclude: /(node_modules|bower_components)/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'],
                        plugins: ['@babel/plugin-proposal-object-rest-spread']
                    }
                }
            }
        ]
    },
    plugins: [
        new MonacoWebpackPlugin({
            languages: ['css', 'scss'],
        }),
    ]
};
