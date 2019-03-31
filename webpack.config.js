const path = require('path');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: './assets/src/editor.js',
    output: {
        path: path.join(__dirname, 'assets/dist'),
        filename: '[name].bundle.js',
    },
    optimization: {
        minimizer: [new UglifyJsPlugin()],
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
                test: /\.js$/,
                exclude: /node_modules/,
                loader: "babel-loader"
            }

        ]
    },
    plugins: [
        new MonacoWebpackPlugin({
            languages: ['css', 'scss'],
        }),
     ],
};