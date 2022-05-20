/**
 * MU PLUGIN WEBPACK CONFIG
 * 
 * Sets up project configuration for a WordPress MU Plugin.
 * 
 * @author  Nate Flynn
 * @since   1.0.0
 */

// EXTERNAL DEPENDENCIES
const path = require( 'path' );
const WebpackWordPressMUPluginVersionSync = require( "@nateflynn/webpack-wordpress-mu-plugin-version-sync" );

// MAIN CONFIG
module.exports = {
    plugins: [
        new WebpackWordPressMUPluginVersionSync({
            configFile: path.resolve( __dirname, 'plugin.json' ),
            outputFile: [
                path.resolve( __dirname, 'index.php' ),
                path.resolve( __dirname, '../wp-silktide-core.php' )
            ]
        }),
    ]
}