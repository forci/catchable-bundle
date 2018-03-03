let webpack = require('webpack');
let path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    // entry: {
    //     app: './app/main.js',
    //     vendor: ['vue', 'axios']
    // },

    entry: path.resolve(__dirname,'app/main.js'),

    output: {
        path: path.resolve(__dirname,'../public/build'),
        filename: 'app.js'
    },

    // output: {
    //     path: path.resolve(__dirname, '../public/build'),
    //     filename: '[name].[chunkhash].js',
    //     publicPath: '/'
    // },

    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            },
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.(eot|svg|ttf|woff|woff2)$/,
                loader: 'file-loader?name=fonts/[name].[ext]'
            },
            // {
            //     test: /\.(eot|svg|ttf|woff|woff2)$/,
            //     loader: 'file?name=public/fonts/[name].[ext]'
            // },
            // { test: /\.(png|woff|woff2|eot|ttf|svg)$/, loader: 'url-loader?limit=100000' },
            // {
            //     test: /\.css$/,
            //     loader: 'style-loader!css-loader'
            // },
            // {
            //     test: /\.scss|\.css$/,
            //     use: extractPlugin.extract({
            //         use: ['css-loader', 'sass-loader']
            //     })
            // },
            {
                test: /\.css$/,
                use: ExtractTextPlugin.extract({
                    fallback: "style-loader",
                    use: ['css-loader', 'sass-loader']
                })
            }
        ]
    },

    resolve: {
        extensions: ['*', '.js', 'vue', '.json'],
        alias: {
            'vue$': 'vue/dist/vue.common.js'
        }
    },

    plugins: [
        new CleanWebpackPlugin([
            'build'
        ], {
            root:     path.resolve(__dirname, '../public'),
            // exclude:  ['shared.js'],
            verbose:  true,
            dry:      false
        }),
        // new webpack.optimize.CommonsChunkPlugin({
        //     names: ['vendor']
        // }),
        new ExtractTextPlugin("app.css"),
        new ManifestPlugin({
            publicPath: 'bundles/forcicatchable/build/'
        })
    ]
};

if (process.env.NODE_ENV === 'production') {
    module.exports.plugins.push(
        new webpack.optimize.UglifyJsPlugin({
            sourcemap: true,
            compress: {
                warnings: false
            }
        })
    );

    module.exports.plugins.push(
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        })
    );
}