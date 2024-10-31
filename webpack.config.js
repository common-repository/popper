const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

const reactJSXRuntimePolyfill = {
	entry: {
		'react-jsx-runtime': {
			import: 'react/jsx-runtime',
		},
	},
	output: {
		path: path.resolve( __dirname, 'assets/js' ),
		filename: 'react-jsx-runtime.js',
		library: {
			name: 'ReactJSXRuntime',
			type: 'window',
		},
	},
	externals: {
		react: 'React',
	},
	// Other config...
};

const myBlock = {
	...defaultConfig,
	entry: {
		...defaultConfig.entry(),
		admin: [ path.resolve( __dirname, 'src/admin/index.js' ) ],
	},
	output: {
		filename: '[name].js',
		path: path.resolve( process.cwd(), 'build' ),
	},
};

module.exports = [ reactJSXRuntimePolyfill, myBlock ];
