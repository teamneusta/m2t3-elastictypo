# Work in Progress - M2T3 - TYPO3 Elastic Integration

## Requirements

- php 7
- TYPO3 > 8.2
- T3G/elasticorn

## Explaination

- add all TYPO3 content to elastic by creating, editing
- add all TYPO3 content to elastic with a TYPO3 task

## Installation

- add following to your composer.json

```javascript
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/teamneusta/m2t3-elastictypo.git"
    }
  ],
  "require": {
    "TeamNeustaGmbH/m2t3-elastictypo": "^1.0"
  }
}
```

- after that make an `composer update`  

## Configuration

- Needed Configuration for AdditionalConfiguration
```
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['host'] = 'elasticsearch';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['port'] = 9200;

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['index'] = 'magentypo';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['m2t3_elastictypo']['content']['type'] = 'content';
```

Explain:

| option | description | example
| ------------ | ------------- | -------------
| host | elastic host | 127.0.0.1
| port | elastics port | 9200
| index | elastic index to the magentypo index | magentypo
| type | elastic index to the magentypo content type | content

#### TYPOSCRIPT

Setup:

```
<INCLUDE_TYPOSCRIPT: source="FILE:EXT:m2t3_elastictypo/Configuration/TypoScript/setup.txt">
```
