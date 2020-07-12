const path = require('path');
const MonacoWebpackPlugin = require('monaco-editor-webpack-plugin');

module.exports = {
    mode: 'development',
    entry: './frontend/src/app.js',
    output: {
        path: path.join(__dirname, 'frontend/dist'),
        filename: '[name].bundle.js',
    },
    node: {
        fs: 'empty'
    },
    resolve: {
        alias: {
            "react": "preact/compat",
            "react-dom/test-utils": "preact/test-utils",
            "react-dom": "preact/compat",
            // Must be below test-utils
        },
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
                        plugins: [
                            '@babel/plugin-proposal-object-rest-spread',
                            ["@babel/plugin-transform-react-jsx",
                                {
                                    "pragma": "h",
                                    "pragmaFrag": "Fragment"
                                }
                            ]
                        ]
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
