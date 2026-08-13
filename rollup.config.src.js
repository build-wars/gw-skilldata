import json from '@rollup/plugin-json';

/**
 * @type {import('rollup').RollupOptions}
 */
export default {
	input  : 'es6/index.js',
	output : [
		{
			file     : 'dist/gw-skilldata-es6-src.js',
			format   : 'es',
			sourcemap: true,
		},
		{
			file     : 'dist/gw-skilldata-node-src.cjs',
			format   : 'cjs',
			sourcemap: true,
		},
		{
			file     : 'dist/gw-skilldata-iife-src.js',
			format   : 'iife',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
		{
			file     : 'dist/gw-skilldata-umd-src.js',
			format   : 'umd',
			name     : 'GwSkilldata',
			sourcemap: true,
		},
	],
	plugins: [
		json(),
	],
};
