const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyPlugin = require('copy-webpack-plugin');
const ZipPlugin = require('zip-webpack-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const webpack = require('webpack');
const chalk = require('chalk');
const logSymbols = require('log-symbols');

let lastPercentage = 0;

const progressHandler = (percentage, message) => {
    // Round to 2 decimal places
    const roundedPercentage = Math.round(percentage * 100);

    // Only output if the percentage has changed
    if (roundedPercentage !== lastPercentage) {
        // Clear console
        process.stdout.write('\x1Bc');

        console.log(''); // Adds a blank line at the beginning

        if (percentage === 0) {
            console.log(chalk.blue(`${logSymbols.info} Build starting...`));
        } else if (percentage === 1) {
            console.log(chalk.green(`${logSymbols.success} Build completed!`));
        } else {
            const progressBar = createProgressBar(roundedPercentage);
            console.log(
                chalk.yellow(
                    `${logSymbols.warning} Progress: ${progressBar} ${roundedPercentage}%`,
                ),
            );
            console.log(chalk.cyan(`${logSymbols.info} Status: ${message}`));
        }

        console.log(''); // Adds a blank line at the end

        lastPercentage = roundedPercentage;
    }
};

// Function to create a visual progress bar
const createProgressBar = (percentage) => {
    const width = 20;
    const filledWidth = Math.round(width * (percentage / 100));
    const emptyWidth = width - filledWidth;
    return chalk.green('█'.repeat(filledWidth)) + chalk.gray('█'.repeat(emptyWidth));
};

module.exports = (env, argv) => {
    const isProduction = argv.mode === 'production';

    return {
        mode: isProduction ? 'production' : 'development',
        devtool: isProduction ? 'source-map' : 'eval-source-map',
        entry: {
            main: './src/media/com_boilerplate/js/main.js',
        },
        output: {
            filename: 'media/com_boilerplate/js/[name].bundle.js',
            path: path.resolve(__dirname, 'dist'),
            clean: true,
        },
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                    },
                },
                {
                    test: /\.css$/,
                    use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader'],
                },
            ],
        },
        plugins: [
            new webpack.ProgressPlugin({
                handler: progressHandler,
                modulesCount: 5000, // Default value
                profile: false, // Disables detailed profiling
            }),
            new MiniCssExtractPlugin({
                filename: 'media/com_boilerplate/css/styles.min.css',
            }),
            new CopyPlugin({
                patterns: [
                    {
                        from: 'src/administrator/components/com_boilerplate',
                        to: 'administrator/components/com_boilerplate',
                    },
                    // {
                    //     from: 'src/components/com_boilerplate',
                    //     to: 'components/com_boilerplate',
                    // },
                    {
                        from: 'src/media/com_boilerplate',
                        to: 'media/com_boilerplate',
                        globOptions: {
                            ignore: ['**/*.js', '**/*.css'], // Ignores JS and CSS files
                        },
                    },
                    { from: 'src/api', to: 'api', noErrorOnMissing: true },
                    { from: 'src/boilerplate.xml', to: 'boilerplate.xml' },
                ],
            }),
            ...(isProduction
                ? [
                      new ZipPlugin({
                          path: path.resolve(__dirname, 'dist'),
                          filename: 'com_boilerplate.zip',
                          extension: 'zip',
                          fileOptions: {
                              mtime: new Date(),
                              mode: 0o100664,
                              compress: true,
                              forceZip64Format: false,
                          },
                      }),
                  ]
                : []),
        ],
        optimization: {
            minimizer: [
                // For webpack@5 you can use the `...` syntax to extend existing minimizers (i.e. `terser-webpack-plugin`)
                `...`,
                new CssMinimizerPlugin(),
            ],
        },
        stats: 'errors-only', // Shows only errors in the console output
    };
};
