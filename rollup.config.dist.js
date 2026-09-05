import babel from '@rollup/plugin-babel';
import json from '@rollup/plugin-json';
import terser from '@rollup/plugin-terser';

/**
 * @type {import('rollup').RollupOptions}
 */
export default {
	input  : 'es6/index.js',
	output : [
		{
			file     : '.build/js-dist/gw-skilldata-es6.mjs',
			format   : 'es',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-node.cjs',
			format   : 'cjs',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-iife.js',
			format   : 'iife',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-umd.js',
			format   : 'umd',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
	],
	plugins: [
		babel({
			babelHelpers: 'bundled',
			configFile  : './babel.config.json',
		}),
		json(),
		terser({
			format: {
				comments         : false,
				keep_quoted_props: true,
//				max_line_len: 130,
				quote_style: 1,
				preamble   :
					  '/*\n'
					+ ' * buildwars/gw-skilldata\n'
					+ ' * @copyright  2026 smiley\n'
					+ ' * @license    MIT\n'
					+ ' * @link       https://github.com/build-wars/gw-skilldata\n'
					+ ' */',
			},
		}),
	],
};
