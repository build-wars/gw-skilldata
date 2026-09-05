import json from '@rollup/plugin-json';

/**
 * @type {import('rollup').RollupOptions}
 */
export default {
	input  : 'es6/index.js',
	output : [
		{
			file     : '.build/js-dist/gw-skilldata-es6-src.mjs',
			format   : 'es',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-node-src.cjs',
			format   : 'cjs',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-iife-src.js',
			format   : 'iife',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
		{
			file     : '.build/js-dist/gw-skilldata-umd-src.js',
			format   : 'umd',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
	],
	plugins: [
		json(),
	],
};
