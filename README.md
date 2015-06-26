# Yii File Config

**This project ABANDONED! There is no wiki, issues and no support. There will be no future updates. Unfortunately, you're on your own.**

This extension allows you to store configuration entries needed by your application in a file. It provides functions for reading and writing this configuration strings.

It is based on [config](http://www.yiiframework.com/extension/config "config") extension developed by Y!!, configuration data is kept in file, not in database. There is also a number of changes and fixes to base configuration code (see below).

## Requirements

- Yii 1.1 or above.

## Usage

### Installation

1. Extract the release file under `protected/extensions`.

2. Modify your application configuration for the use of this extension (for security reasons, you have to specify path to your configuration file **relative to _protected_ folder**):

~~~php
return array
(
    ...
	'components'=>array
	(
        ...
        'config'=>array
        (
            'class'=>'ext.FileConfig',
            'configFile'=>'configuration/config.values',
        ),
        ...
	),
	...
);
~~~

Use `Yii::app()->config` for reading and writing configuration items:

~~~php
Yii::app()->config->setValue('test', '7');
Yii::app()->config->getValue('test', 'none');
~~~

Both values and configuration array are serialized, therefore you can set and get value of any kind - i.e. string, integer, boolean, array, etc.

### Configuration

These properties are available and can be set in the configuration:

- `configFile` - path to a file where configuration entries are stored (relative to _protected_ folder),
- `cacheID` - defaults to false; the ID of cache component,
- `strictMode` - defaults to false; see below, what happens, if this is set to true.

### Strict mode

If extension is in strict mode that means:

- you won't be able to read/write value from/to non-existing key,
- if configuration file is not readable, you will see exception per each attempt to read configuration (in-non strict mode an empty value will be returned instead),
- if caching component ID is invalid, exception will be raisen (in non-strict mode cache will not be used without displaying any error in this situation).

## Additional notes

Various changes in relation to original _config_ extension and other things that you should take care of:

1. For security reasons, you have to specify path to your configuration file **relative to _protected_ folder**.
2. Setter and getter functions changed from _set_ and _get_ to _setValue_ and _getValue_.
3. Strict Mode turned off by default.
4. Support for using caching component to cache configuration file is left as in base extension (with some minor changes), but most people find it unusable as configuration is kept in a file itself, therefore using caching could in this situation even degrade performance.
5. English comments were added.
6. Function _getValue_ supports optional _default_ parameter, which is returned every time when reading key value is impossible (i.e. key does not exists).

## Version history

This describes in short changes made to this extension.

### Version 1.2

On **8 March 2011** following changes were made to this extension:

- Private **_getConfig()** function became public **getConfig()**,
- Some code tweak-ups, optimizations and comments clean-ups.

### Version 1.1

On **8 December 2010** the behaviour of this extension has changed slightly for further performance optimization:

- Function **setValue()** now has optional third parameter **forceWrite**, set to FALSE by default. Actual writing of configuration file occurs now only, if this is set to TRUE (occurred always in previous version). If **forceWrite** is set to FALSE, this function now always returns TRUE. If set to TRUE, function will return result of calling **setConfig()** which can tell developer, if writing was successful.
- By analogy, function **getValue()** also now has optional third parameter, **forceRead** (also set to FALSE by default), which (when set to TRUE) forces reading of configuration file with each call to this function. In previous version, reading of configuration file occurred only when configuration array was empty and there was no way to force it in other situations.
- Private function **_setConfig()** became public function **setConfig()** and now is preferred way of writing configuration file - i.e. set as many params as you want with setValue() and only once call setConfig(). In previous version writing occurred each time configuration item was set, so there was no need for forcing write of configuration file.
- Configuration items (in **_config** array) are no longer serialized upon setting and unserialized upon getting. Now, only configuration array is serialized / unserialized, which may result in less configuration file and access time.

### Version 1.0

On **6 December 2010** this extension was initially released.

**This project ABANDONED! There is no wiki, issues and no support. There will be no future updates. Unfortunately, you're on your own.**