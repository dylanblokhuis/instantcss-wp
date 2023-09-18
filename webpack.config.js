const path = require("path");
const MonacoWebpackPlugin = require("monaco-editor-webpack-plugin");
const webpack = require("webpack");

module.exports = {
  mode: "development",
  entry: "./assets/src/editor.js",
  output: {
    path: path.join(__dirname, "assets/dist"),
    filename: "[name].bundle.js",
  },
  resolve: {
    fallback: {
      fs: false,
      crypto: require.resolve("crypto-browserify"),
      path: require.resolve("path-browserify"),
      buffer: require.resolve("buffer/"),
      stream: require.resolve("stream-browserify"),
      process: require.resolve("process/browser"),
    },
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          {
            loader: "style-loader",
          },
          {
            loader: "css-loader",
          },
        ],
      },
      {
        test: /\.ttf$/,
        use: ["file-loader"],
      },
      {
        test: /\.m?js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env"],
            plugins: ["@babel/plugin-proposal-object-rest-spread"],
          },
        },
      },
    ],
  },
  plugins: [
    new webpack.ProvidePlugin({
      process: "process/browser",
    }),
    new MonacoWebpackPlugin({
      languages: ["css", "scss"],
    }),
  ],
};
