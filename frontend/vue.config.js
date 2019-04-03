module.exports = {
    devServer: {
        proxy: 'http://trnsltr.localhost'
    },
    outputDir: '../public',

    // modify the location of the generated HTML file.
    // make sure to do this only in production.
    indexPath: 'index.html'
}
