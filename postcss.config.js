module.exports = {
    plugins: {
        'postcss-preset-env': {
            stage: 0,
            autoprefixer: {
                grid: true,
            },
        },
        cssnano: process.env.NODE_ENV === 'production' ? {} : false,
    },
};