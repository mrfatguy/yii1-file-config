<?php

/**
 * This is a simple extension that allows you to store (in a file) configuration
 * entries needed by your application.
 * 
 * It provides functions for reading and writing this configuration strings. It
 * is based on config extension (http://www.yiiframework.com/extension/config),
 * developed by Y!!, but place, where configuration data is kept, has been changed
 * from database to file. There is also a number of changes and fixes to base
 * extension code.
 * 
 * Changes in newest (most recent) version:
 * - Private _getConfig() became public getConfig,
 * - Some code tweak-ups and comments clean-ups.
 * 
 * Extension developed by trejder (http://www.yiiframework.com/user/7141/). For
 * more information visit http://www.yiiframework.com/extension/fileconfig.
 */

class FileConfig extends CApplicationComponent
{
    /**
     * Constants - caching component key and common error text.
     */
    const CACHE_KEY = 'Extension.FileConfig';
    const ERROR_MSG = 'Please make sure that the file exits and is accessible by PHP process.';

    /**
     * @var string Path to a configuration file
     */
    public $configFile = 'config/configuration.file';

    /**
     * @var mixed Cache component ID or FALSE if not using caching
     */
    public $cacheID = FALSE;

    /**
     * @var boolean Whether extensions is in strict mode, meaning:
     *
     * a) you won't be able to read/write value from/to non-existing key,
     *
     * b) if configuration file is not readable, you will see exception, while
     * in-non strict mode a default value will be returned instead (or NULL, if
     * default value is not provided),
     *
     * c) if caching component ID is invalid, exception will be raisen, while in
     * non-strict mode cache will not be used at all.
     */
    public $strictMode = FALSE;
    
    private $_cache;
    private $_config;

    /**
     * Reads a value from a configuration.
     * 
     * @param string $key Configuration key under which a value is stored.
     * @param mixed $default optional Default value, if key is not found.
     * @param boolean $forceRead optional Whether to force reading configuration.
     * 
     * @return mixed Value for a provided key or default value, if provided.
     */
    public function getValue($key, $default = '', $forceRead = FALSE)
    {       
        if (($this->_config === NULL) || ($forceRead)) $this->getConfig();

        if ((is_array($this->_config === FALSE)) || (array_key_exists($key, $this->_config) === FALSE))
        {
            if ($this->strictMode === TRUE)
            {
                throw new CException('Unable to get value! Key "'.$key.'" does not exists!');
            }
            else return (isset($default)) ? $default : NULL;
        }
                else return (isset($this->_config[$key])) ? $this->_config[$key] : NULL;
    }

    /**
     * Writes a value to a configuration.
     * 
     * @param string $key Configuration key under which a value is stored.
     * @param mixed $value Value of a configuration parameter (key).
     * @param boolean $forceWrite optional Whether to force writing configuration.
     * 
     * @return boolean TRUE if setting a key and writting a config was successful.
     */
    public function setValue($key, $value, $forceWrite = FALSE)
    {
        if ($this->_config != NULL)
        {
            if ((is_array($this->_config === FALSE)) || (array_key_exists($key, $this->_config) === FALSE))
            {
                if ($this->strictMode === TRUE) throw new CException('Unable to set value! Key "'.$key.'" does not exists and "strictMode" is set to TRUE!');
            }
        }
        else $this->getConfig();
        
        $this->_config[$key] = $value;
                
        $cache = $this->_getCache();
        
        if ($cache !== FALSE) $cache->set(self::CACHE_KEY, $this->_config);

        return ($forceWrite) ? $this->setConfig() : TRUE;
    }


    /**
     * Attempts to write $this->_config array to a configuration file. If
     * file isn't writable (i.e. not exits or is not accessible) it throws
     * an exception.
     */
    public function setConfig()
    {
        $file = $this->configFile;

        $handle = fopen($file, 'wb');
        if ($handle)
        {
            if (fwrite($handle, serialize($this->_config)))
            {
                if ($this->_getCache() != FALSE) $cache->set(self::CACHE_KEY, $this->_config);

                return TRUE;
            }
            else throw new CException('Error writing configuration file "'.$file.'"! '.self::ERROR_MSG);
        }
        else throw new CException('Error opening configuration file "'.$file.'" for writing! '.self::ERROR_MSG);
    }

    /**
     * Returns caching component, if cache valid and enabled, FALSE if cache
     * is disabled or throws an exception if cache is invalid.
     * @return <type>
     */
    private function _getCache()
    {
        
        if ($this->cacheID === FALSE)
        {
            return FALSE;
        }
        elseif ($this->_cache !== NULL)
        {
            return $this->_cache;
        }
        else
        {
            if ($this->strictMode === TRUE)
            {
                    throw new CException('Property cacheID "'.$this->cacheID.'" is invalid! Please make sure it refers to the ID of a valid CCache application component.');
            }
            else return FALSE;
        }
        
    }

    /**
     * Attempts to read configuration file into $this->_config array. If
     * file isn't readable (i.e. not exits or is not accessible) it throws
     * an exception (if "strictMode" set to TRUE) or returns an empty array.
     */
    public function getConfig()
    {
        $file = $this->configFile;
        $cache = $this->_getCache();
        
        if (($cache === FALSE) || ($this->_config = $cache->get(self::CACHE_KEY) === FALSE))
        {
            if (is_readable($file))
            {
                $this->_config = unserialize(file_get_contents($file));
                $this->_config = (is_array($this->_config)) ? $this->_config : array();
                
                if ($cache != FALSE) $cache->set(self::CACHE_KEY, $this->_config);
            }
            else
            {
                if ($this->strictMode === TRUE)
                {
                    throw new CException('Configuration file "'.$file.'" is not readable! '.self::ERROR_MSG);
                }
                else $this->_config = array();
            }
        }
    }
}

?>