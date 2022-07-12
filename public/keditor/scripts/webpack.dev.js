const webpack = require('webpack');
const path = require('path');
const merge = require('webpack-merge')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

const common = require('./webpack.common')

module.exports = merge(common, {
	mode: 'development',
	entry: './demo/keditor_demo',
	output: {
		filename: 'keditor.[hash].js',
		path: path.resolve(__dirname, 'build')
	},

	devtool: 'cheap-module-eval-source-map',
	devServer: {
		contentBase: 'build',
		host: 'localhost',
		port: 8080
	},

	plugins: [
		new MiniCssExtractPlugin({
			filename: 'css/keditor.[hash].css'
		}),
		new webpack.NamedModulesPlugin(),
		new HtmlWebpackPlugin({
			template: './demo/keditor_demo.html',
			inject: true
		}),
	]
});
