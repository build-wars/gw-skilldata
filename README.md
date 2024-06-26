# build-wars/gw-skilldata

[Guild Wars](https://www.guildwars.com/) skill data and skill descriptions for use with template decoders, e.g. in BBCode, Wikis etc.

[![PHP Version Support][php-badge]][php]
[![Packagist version][packagist-badge]][packagist]
[![NPM version][npm-badge]][npm]
[![License][license-badge]][license]
[![Continuous Integration][gh-action-badge]][gh-action]
[![Coverage][coverage-badge]][coverage]
[![Packagist downloads][downloads-badge]][downloads]

[php-badge]: https://img.shields.io/packagist/php-v/buildwars/gw-skilldata?logo=php&color=8892BF&logoColor=ccc
[php]: https://www.php.net/supported-versions.php
[packagist-badge]: https://img.shields.io/packagist/v/buildwars/gw-skilldata.svg?logo=packagist&logoColor=ccc
[packagist]: https://packagist.org/packages/buildwars/gw-skilldata
[npm-badge]: https://img.shields.io/npm/v/@buildwars/gw-skilldata?logo=npm&logoColor=ccc
[npm]: https://www.npmjs.com/package/@buildwars/gw-skilldata
[license-badge]: https://img.shields.io/github/license/build-wars/gw-skilldata
[license]: https://github.com/build-wars/gw-skilldata/blob/main/LICENSE
[gh-action-badge]: https://img.shields.io/github/actions/workflow/status/build-wars/gw-skilldata/ci.yml?branch=main&logo=github&logoColor=ccc
[gh-action]: https://github.com/build-wars/gw-skilldata/actions/workflows/ci.yml?query=branch%3Amain
[coverage-badge]: https://img.shields.io/codecov/c/github/build-wars/gw-skilldata.svg?logo=codecov&logoColor=ccc
[coverage]: https://codecov.io/github/build-wars/gw-skilldata
[downloads-badge]: https://img.shields.io/packagist/dt/buildwars/gw-skilldata.svg?logo=packagist&logoColor=ccc
[downloads]: https://packagist.org/packages/buildwars/gw-skilldata/stats

# Overview

## Features

- Guild Wars skill data
  - Skill descriptions for English and German
- Toolset to add other translations (hopefully maybe)


## Requirements

- PHP 8.1+


## Quickstart

### PHP

```php
use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;

class MyClass implements SkillDataAwareInterface{
	use SkillDataAwareTrait

	public function __construct(string $lang){
		// set the language and initialize $this->skillData
		$this->setSkillDataLanguage($lang);
	}

	public function getSkill(int $skillID):mixed{
		// $this->skillData is now available
		$data = $this->skillData->get($skillID);

		// do stuff with the $data array
		// the available array keys are in $this->skillData->keys
	}
}
```

The returned skill data array from `SkillDataInterface::get(979)` looks as follows:

```php
$data = [
	'id'              => 979,
	'campaign'        => 3,
	'profession'      => 5,
	'attribute'       => 2,
	'type'            => 24,
	'is_elite'        => false,
	'is_rp'           => false,
	'is_pvp'          => false,
	'pvp_split'       => true,
	'split_id'        => 3191,
	'upkeep'          => 0,
	'energy'          => 10,
	'activation'      => 2,
	'recharge'        => 12,
	'adrenaline'      => 0,
	'sacrifice'       => 0,
	'overcast'        => 0,
	'name'            => 'Mistrust',
	'description'     => 'For 6 seconds, the next spell that target foe casts on one of your allies fails and deals 10...100 damage to that foe and all nearby foes.',
	'concise'         => '(6 seconds.) The next spell that target foe casts on one of your allies fails and deals 10...100 damage to target and nearby foes.',
	'campaign_name'   => 'Nightfall',
	'profession_name' => 'Mesmer',
	'profession_abbr' => 'Me',
	'attribute_name'  => 'Domination Magic',
	'type_name'       => 'Hex Spell',
];
```

### JavaScript :coffee:

JavaScript doesn't have traits, so you will need to implement that part by yourself:

```js
class MyClass{

	_languages = {
		de: SkillLangGerman,
		en: SkillLangEnglish,
	};

	skillData;

	constructor(lang){
		this.setSkillDataLanguage(lang);
	}

	setSkillDataLanguage(lang){

		if(!this._languages[lang]){
			throw new Error('invalid language');
		}

		this.skillData = new this._languages[lang]();

		return this;
	}

	getSkill(skillID){
		// this.skillData is now available
		let data = this.skillData.get(skillID);

		// do stuff with the data array
	}

}
```

which outputs:

```js
let data = {
	id: 979,
	campaign: 3,
	profession: 5,
	attribute: 2,
	type: 24,
	is_elite: false,
	is_rp: false,
	is_pvp: false,
	pvp_split: true,
	split_id: 3191,
	upkeep: 0,
	energy: 10,
	activation: 2,
	recharge: 12,
	adrenaline: 0,
	sacrifice: 0,
	overcast: 0,
	name: 'Mistrust',
	description: 'For 6 seconds, the next spell that target foe casts on one of your allies fails and deals 10...100 damage to that foe and all nearby foes.',
	concise: '(6 seconds.) The next spell that target foe casts on one of your allies fails and deals 10...100 damage to target and nearby foes.',
	campaign_name: 'Nightfall',
	profession_name: 'Mesmer',
	profession_abbr: 'Me',
	attribute_name: 'Domination Magic',
	type_name: 'Hex Spell'
}
```

### PvP skill redirect

When the `$pvp` parameter is set to `true`, `SkillDataInterface::get(979, true)` will redirect to the PvP version of the given skill (if available, `pvp_split` and `split_id`):

```php
$data = [
	'id'              => 3191,
	'campaign'        => 3,
	'profession'      => 5,
	'attribute'       => 2,
	'type'            => 24,
	'is_elite'        => false,
	'is_rp'           => false,
	'is_pvp'          => true,
	'pvp_split'       => false,
	'split_id'        => 0,
	'upkeep'          => 0,
	'energy'          => 10,
	'activation'      => 2,
	'recharge'        => 12,
	'adrenaline'      => 0,
	'sacrifice'       => 0,
	'overcast'        => 0,
	'name'            => 'Mistrust (PvP)',
	'description'     => 'For 6 seconds, the next spell that target foe casts on one of your allies fails and deals 10...75 damage to that foe and all nearby foes.',
	'concise'         => '(6 seconds.) The next spell that target foe casts on one of your allies fails and deals 10...75 damage to target and nearby foes.',
	'campaign_name'   => 'Nightfall',
	'profession_name' => 'Mesmer',
	'profession_abbr' => 'Me',
	'attribute_name'  => 'Domination Magic',
	'type_name'       => 'Hex Spell',
];
```

### HTML tags in descriptions

The skill descriptions may contain the custom HTML tags `<gray>...</gray>` and `<sic/>` that you can either replace or use to style, for example:

```html
<gray>No effect unless hexed foe attacks.</gray>

Each attack that hits deals +13...30 Holy damage <sic/>
```

## API

### `SkillDataInterface`

(The API is similar for the JavaScript version)

| Method                                                | Description                                                                              |
|-------------------------------------------------------|------------------------------------------------------------------------------------------|
| `get(int $id, bool $pvp = false)`                     | Returns the data for the given skill ID, including descriptions for the current language |
| `getAll(array $IDs, bool $pvp = false)`               | Returns an array with the skill data for each of the given skill IDs                     |
| `getByCampaign(int $campaign, bool $pvp = false)`     | Returns all skills for the given campaign ID                                             |
| `getByProfession(int $profession, bool $pvp = false)` | Returns all skills for the given profession ID                                           |
| `getByAttribute(int $attribute, bool $pvp = false)`   | Returns all skills for the given attribute ID                                            |
| `getByType(int $type, bool $pvp = false)`             | Returns all skills for the given skill type ID                                           |
| `getElite(bool $pvp = false)`                         | Returns all elite skills                                                                 |
| `getRoleplay()`                                       | Returns all roleplay skills                                                              |


## Disclaimer

Use at your own risk!
