<?php
/**
 * Class BuildPublicIndex
 *
 * @created      04.09.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace Buildwars\GWSkillDataTools\Builder;

use Buildwars\GWSkillData\Lang;
use chillerlan\Utilities\Directory;
use chillerlan\Utilities\File;
use Dom\HTMLDocument;
use function sprintf;
use function str_contains;
use function str_replace;
use const DATADIR;
use const PUBLICDIR;

final class BuildPublicIndex extends BuilderAbstract{

	private const string INDEX_HTML = PUBLICDIR.'/index.html';
	private const string INDEX_RAW  = __DIR__.'/index.htm';

	private HTMLDocument $document;

	public function build():static{
		$this->document = HTMLDocument::createFromFile(self::INDEX_RAW);

		$this
			->addJSDist()
			->addJSONSkilldesc()
			->addJSONSchemas()
			->addPawnedDBs()
		;

		$html = $this->document->saveHtml();
		// ok, the new HTMLDocument class is nice, but now we have to manually format the HTML guess?
		// better than nothing for now
		$html = str_replace(['</li><li>', "\t<li>", '</li></ul>'], ["</li>\n\t<li>", "\t\t<li>", "</li>\n\t</ul>"], $html);

		File::save(self::INDEX_HTML, $html);

		return $this;
	}

	private function addJSDist():self{
		$jsDist = $this->document->getElementById('js-dist');

		foreach(Directory::filelist(self::JS_DIST_DIR, ['cjs', 'mjs', 'js']) as $fileName => $finfo){
			$a  = $this->document->createElement('a');
			$li = $this->document->createElement('li'); // ->appendChild($a); should be here

			$a->setAttribute('href', sprintf('js/%s', $fileName)); // void, why??
			$a->textContent = $fileName; // could be a setter, returning itself

			// appendChild() returns the appended node, not the one the method is called on, hostile, why??
			// i read the mozilla docs, and it doesn't make much sense there either: breaking the
			// fluent builder pattern for a nested structure like that is a bad trade-off, it is unreadable.
			// https://developer.mozilla.org/en-US/docs/Web/API/Node/appendChild#description
			$jsDist->appendChild($li)->appendChild($a);
		}

		return $this;
	}

	private function addJSONSkilldesc():self{
		$jsonSkilldesc = $this->document->getElementById('json-skilldesc');

		foreach(Directory::filelist(DATADIR.'/json-full', null, 'skilldesc-') as $fileName => $finfo){

			if(str_contains($fileName, '-g')){
				continue;
			}

			$lang = new Lang(str_replace('skilldesc-', '', $finfo->getBasename('.json')));

			$a  = $this->document->createElement('a');
			$li = $this->document->createElement('li');
			$li->append($a, sprintf(' [%s]', $lang->getName(Lang::EN)));

			$a->setAttribute('href', sprintf('json/%s', $fileName));
			$a->textContent = $fileName;

			$jsonSkilldesc->appendChild($li);
		}

		return $this;
	}

	private function addJSONSchemas():self{
		$jsonSchemas = $this->document->getElementById('json-schemas');

		foreach(Directory::filelist(DATADIR.'/schemas', ['json'], 'skill') as $fileName => $finfo){
			$a  = $this->document->createElement('a');
			$li = $this->document->createElement('li');

			$a->setAttribute('href', sprintf('schemas/%s', $fileName));
			$a->textContent = $fileName;

			$jsonSchemas->appendChild($li)->appendChild($a);
		}

		return $this;
	}

	private function addPawnedDBs():self{

		$containers = [
			'buildwars_pve.csv'         => $this->document->getElementById('pawned-full-pve'),
			'buildwars_pvp.csv'         => $this->document->getElementById('pawned-full-pvp'),
			'buildwars_pve_concise.csv' => $this->document->getElementById('pawned-concise-pve'),
			'buildwars_pvp_concise.csv' => $this->document->getElementById('pawned-concise-pvp'),
		];

		foreach(Directory::filelist(self::PAWNED_CACHEDIR, ['csv']) as $fileName => $finfo){
			$name = $finfo->getBasename('.csv');

			$a1  = $this->document->createElement('a');
			$a1->setAttribute('href', sprintf('pawned/%s.csv', $name));
			$a1->textContent = sprintf('%s.csv', $name);

			$a2  = $this->document->createElement('a');
			$a2->setAttribute('href', sprintf('pawned/%s.ini', $name));
			$a2->textContent = '.ini';

			$li = $this->document->createElement('li');
			$li->append($a1, ' [', $a2, ']');

			foreach($containers as $needle => $ul){
				if(str_contains($fileName, $needle)){
					$ul->appendChild($li);
					break;
				}
			}

		}

		return $this;
	}

}
