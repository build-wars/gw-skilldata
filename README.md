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
  - Skill databases for [paw·ned²](https://redeemer.biz/guild-wars/projekte/pawned2/)
- Toolset to add other translations (hopefully maybe)

Most of the release files are built on each push and hosted on GitHub pages: https://github.com/build-wars/gw-skilldata

## Requirements

- PHP 8.1+

alternatively:

- Javascript
	- node.js >= 24
	- a web browser

# Documentation

## PHP: Installation with [composer](https://getcomposer.org)

### Terminal
```
composer require buildwars/gw-skilldata
```

### composer.json
```json
{
	"require": {
		"php": "^8.1",
		"buildwars/gw-skilldata": "^2.0"
	}
}
```

Note: check the [releases](https://github.com/buildwars/gw-skilldata/releases) for valid versions.

## JS: Installation with [npm](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm)

### Terminal
```
npm install @buildwars/gw-skilldata
```

### package.json
```json
{
	"dependencies": {
		"@buildwars/gw-skilldata": "^2.0"
	}
}
```

### Direct include

You can also directly include the library in your HTML:
```html
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8"/>
	<!-- ... -->
</head>
<body>
	<!-- include the script at the bottom of the html body -->
	<script type="module">
		// import the script
		import * as skilldata from 'https://build-wars.github.io/gw-skilldata/js/gw-skilldata-es6.js';
		// alternative via unpkg
		// import * as skilldata from 'https://unpkg.com/@buildwars/gw-skilldata@2.0.0/dist/gw-skilldata-es6.js';

		// do stuff
	</script>
</body>
</html>
```

Please note that the include from GitHub pages represents the development version, which is built on each push to the main branch. Use NPM or unpkg instead for stable versions.


## Quickstart

### PHP

```php
use Buildwars\GWSkillData\SkillDataAwareInterface;
use Buildwars\GWSkillData\SkillDataAwareTrait;
use Buildwars\GWSkillData\SkillDataInterface;
use Buildwars\GWSkillData\SkillLangEnglish;

class MyClass implements SkillDataAwareInterface{
	use SkillDataAwareTrait;

	protected SkillDataInterface $skillDataEnglish;

	public function __construct(string $lang){
		// set the language and initialize $this->skillData
		$this->setSkillDataLanguage($lang);

		// alternatively, you can simply invoke one of the skill language instances
		$this->skillDataEnglish = new SkillLangEnglish;
	}

	public function getSkill(int $skillID, bool $pvp):mixed{
		// $this->skillData is now available
		$data = $this->skillData->get($skillID, $pvp);

		// do stuff with the $data array
		var_dump($data->id);
	}

}
```

### JavaScript :coffee:

JavaScript doesn't have traits, so you will need to implement that part by yourself:

```js
class MyClass{

	#languages = {
		de: SkillLangGerman,
		en: SkillLangEnglish,
	};

	#skillData;

	constructor(lang){
		this.setSkillDataLanguage(lang);
	}

	setSkillDataLanguage(lang){

		if(!this.#languages[lang]){
			throw new Error('invalid language');
		}

		this.#skillData = new this.#languages[lang]();

		return this;
	}

	getSkill(skillID, pvp){
		// this.skillData is now available
		let data = this.#skillData.get(skillID, pvp);

		// do stuff with the data array
		console.log(data.id);
	}

}
```
### Return

A call `SkillDataInterface::get(979, false)` (PHP) or `SkillDataAbstract.get(979, false)` (JS) returns a `Skill` instance that looks similar to the following:

```
Skill{
	id          = 979
	campaign    = new Campaign(Campaign::NIGHTFALL)
	profession  = new Profession(Profession::MESMER)
	attribute   = new Attribute(Attribute::DOMINATION_MAGIC)
	type        = new Type(Type::HEX_SPELL)
	is_elite    = false
	is_rp       = false
	is_pvp      = false
	pvp_split   = true
	split_id    = 3191
	upkeep      = 0
	energy      = 10
	activation  = 2
	recharge    = 12
	adrenaline  = 0
	sacrifice   = 0
	overcast    = 0
	name        = 'Mistrust'
	description = 'For 6 seconds, the next spell that target foe casts on one of your allies fails and deals 10...100 damage to that foe and all nearby foes.'
	concise     = '(6 seconds.) The next spell that target foe casts on one of your allies fails and deals 10...100 damage to target and nearby foes.'
}
```

### PvP skill redirect

When the `$pvp` parameter is set to `true`, `SkillDataInterface::get(979, true)` will redirect to the PvP version of the given skill if available,
and vice versa it will redirect a given PvP skill ID to the PvE version when `$pvp` is set to `false`:

```
Skill{
	id          = 3191
	.
	.
	is_pvp      = true
	pvp_split   = true
	split_id    = 979
	.
	.
	name        = 'Mistrust (PvP)'
	description = 'For 6 seconds, the next spell that target foe casts on one of your allies fails and deals 10...75 damage to that foe and all nearby foes.'
	concise     = '(6 seconds.) The next spell that target foe casts on one of your allies fails and deals 10...75 damage to target and nearby foes.'
}
```

### HTML tags in descriptions

The skill descriptions may contain the custom HTML tags `<gray>...</gray>` and `<sic/>` that you can either replace or use to style, for example:

```html
<gray>No effect unless hexed foe attacks.</gray>

Each attack that hits deals +13...30 Holy damage <sic/>
```

## API

(The API is similar for the JavaScript version)

### `SkillDataInterface`

The `SkillDataInterface` describes the methods available in its inheritors (`SkillLangEnglish`, `SkillLangGerman`, [...]).

| Method                                                            | Return    | Description                                                                              |
|-------------------------------------------------------------------|-----------|------------------------------------------------------------------------------------------|
| `get(int $id, bool $pvp = false)`                                 | `Skill`   | Returns the data for the given skill ID, including descriptions for the current language |
| `getAll(array $IDs, bool $pvp = false)`                           | `Skill[]` | Returns an array with the skill data for each of the given skill IDs                     |
| `getByCampaign(Campaign\|int $campaign, bool $pvp = false)`       | `Skill[]` | Returns all skills for the given campaign ID                                             |
| `getByProfession(Profession\|int $profession, bool $pvp = false)` | `Skill[]` | Returns all skills for the given profession ID                                           |
| `getByAttribute(Attribute\|int $attribute, bool $pvp = false)`    | `Skill[]` | Returns all skills for the given attribute ID                                            |
| `getByType(Type\|int $type, bool $pvp = false)`                   | `Skill[]` | Returns all skills for the given skill type ID                                           |
| `getByTypeWithSubtypes(Type\|int $type, bool $pvp = false)`       | `Skill[]` | Returns all skills for the given skill type ID and its subtypes (if any)                 |
| `getElite(bool $pvp = false)`                                     | `Skill[]` | Returns all elite skills                                                                 |
| `getRoleplay()`                                                   | `Skill[]` | Returns all roleplay skills                                                              |
| `getIDs(bool $pvp = false)`                                       | `int[]`   | Returns a list of all skill IDs, either Pve or PvP                                       |

### `Skill`

The `Skill` object holds all [skill](https://wiki.guildwars.com/wiki/Skill) data for the given skill ID and language. The names for the public (readonly) properties can be found in the constants `SkillDataInterface::KEYS_DESC` and `SkillDataInterface::KEYS_DATA` (PHP),
or in `Skill.KEYS_DESC` and `Skill.KEYS_DATA` (JS). Some of the properties hold instances of the objects described below.
This class is not meant to be invoked as standalone, but as return value for the `SkillDataInterface` methods.

| Method                                                       | Return    | Description                                                                          |
|--------------------------------------------------------------|-----------|--------------------------------------------------------------------------------------|
| `toArray()`                                                  | `mixed[]` | Returns a pure array representation (key-value object in JS) of the `Skill` instance |
| `getFieldName(string $field, Lang\|string $lang = Lang::EN)` | `string`  | Returns the display name for the given field (PHP only)                              |

### `Lang`

The `Lang` object holds the language information used for translatable strings. An instance of this object can be used as paramter in various methods.

| Method      | Return   | Description                                        |
|-------------|----------|----------------------------------------------------|
| `getName()` | `string` | Returns the readable name of the given language ID |

### `DataObjectAbstract`

The `DataObjectAbstract` class is the abstract parent of the classes listed below.

| Method           | Return   | Description                                               |
|------------------|----------|-----------------------------------------------------------|
| `getName()`      | `string` | Returns the readable name of the given ID                 |
| `is(int $id)`    | `bool`   | Checks whether the object ID is equal to the given ID     |
| `in(array $ids)` | `bool`   | Checks whether the object ID is in the given array of IDs |

#### `Attribute`

The `Attribute` class encapsulates all skill [attribute](https://wiki.guildwars.com/wiki/Attribute) related static data.

| Method                                                                                | Return       | Description                                                                                 |
|---------------------------------------------------------------------------------------|--------------|---------------------------------------------------------------------------------------------|
| `setLevel(int $level)`                                                                | `self`       | Sets the attribute level                                                                    |
| `addLevel(int $level)`                                                                | `self`       | Adds the given amount to the current attribute level                                        |
| `getLevel()`                                                                          | `int`        | Returns the current attribute level                                                         |
| `getProfession()`                                                                     | `Profession` | Returns the profession for the current attribute                                            |
| `getProfessionID()`                                                                   | `int`        | Returns the profession ID for the current attribute                                         |
| `getMaxValue()`                                                                       | `int`        | Returns the internal max value for the current attribute                                    |
| `getByProfession(Profession\|int $profession)`                                        | `int[]`      | Returns all attributes for the given profession                                             |
| `isPrimary()`                                                                         | `bool`       | Checks whether the current attribute is a primary attribute                                 |
| `clamp(int\|null $level = null)`                                                      | `int`        | Clamps the given value to the internal max value for the current attribute                  |
| `getProgressionFunction()`                                                            | `Closure`    | Returns the progression function for the given title rank or attribute                      |
| `getProgressionValue(int\|string $val0, int\|string $val15, int\|null $level = null)` | `int`        | Calculates the value for the given val0-val15 progression for the given attribute and level |
| `getProgressionTable(int $val0, int $val15, int\|null $max = null)`                   | `int[]`      | Creates a progression table for the values 0 to attribute-max of the given val0 and val15   |

#### `Campaign`

The `Campaign` class encapsulates all [campaign](https://wiki.guildwars.com/wiki/Campaign) related static data.

| Method                                               | Return   | Description                                                          |
|------------------------------------------------------|----------|----------------------------------------------------------------------|
| `getContinentName(Lang\| string\|null $lang = null)` | `string` | Returns the readable name of the continent for the given campaign ID |

#### `Profession`

The `Profession` class encapsulates all [profession](https://wiki.guildwars.com/wiki/Profession) related static data.

| Method                                     | Return      | Description                                                |
|--------------------------------------------|-------------|------------------------------------------------------------|
| `getAbbr(Lang\|string\|null $lang = null)` | `string`    | Returns the short name for the fiven profession ID         |
| `getPrimaryAttribute(int $level = 0)`      | `Attribute` | Returns the primary attribute of the current profession    |
| `getPrimaryAttributeID()`                  | `int`       | Returns the primary attribute ID of the current profession |
| `getAttributes()`                          | `int[]`     | Returns all attributes for the current profession          |

#### `Type`

The `Type` class encapsulates all [skill type](https://wiki.guildwars.com/wiki/Skill_type) related static data.

| Method           | Return  | Description                                                            |
|------------------|---------|------------------------------------------------------------------------|
| `withSubtypes()` | `int[]` | Returns the IDs for the given skill type including all of its subtypes |

## Disclaimer

Use at your own risk!

### Licensing

- Data from the [Guild Wars Wiki (GWW)](https://wiki.guildwars.com/wiki/Guild_Wars_Wiki:Copyrights) is available under the [GNU Free Documentation License](http://www.gnu.org/copyleft/fdl.html).
- Data from [GuildWiki](https://www.guildwiki.de/wiki/GuildWiki:Lizenzhinweise) is available under [Creative Commons CC BY-NC-SA 2.5](https://creativecommons.org/licenses/by-nc-sa/2.5/deed.de)
