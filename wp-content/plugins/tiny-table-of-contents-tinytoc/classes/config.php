<?php
/**
 * @author Marijan Šuflaj <msufflaj32@gmail.com>
 * @link http://www.php4every1.com
 */

//Disable direct view.
if (!defined('IN_PLUGIN'))
    die('You can not access this file directly.');

/**
 * Config class
 *
 * @category TinyTOC
 * @package config
 * @version 0.3 Beta
 */
class tinyConfig
{
    
	/**
	 * Object with configuration.
	 *
	 * @var stdClass
	 * @since 0.3 Beta
	 */
	private $_config;
	
	/**
     * Class instance
     * 
     * @var tinyConfig
     * @since 0.3 Beta
     */
    private static $_self;
    
    /**
     * Constructor.
     * 
     * @return 
     * @since 0.3 Beta
     */
    private function __construct() 
    {

    }
    
    /**
     * Returns class instance.
     *
     * @return tinyConfig
     * @since 0.3 beta
     */
    public static function getInstance()
    {
        if(!self::$_self)
            self::$_self = new tinyConfig();

        return self::$_self;
    }
    
	/**
	 * Returns config option.
	 *
	 * @param string|array $name String or array with params names.
	 * @return stdClass
	 * @since 0.3 Beta
	 */
	public function get($name)
	{
		if (empty($name))
		  return $this->_config;
		
		//Temp class.
		$temp = new stdClass();
		
		//Is array?
		if (is_array($name))  {
            foreach ($name as $v) {
            	//Do we have it?
            	if (isset($this->_config->$v))
            	    $temp->$v = $this->_config->$v;
            	//No? Then create it.
            	else {
            		$temp->$v = $this->_get($v);
            	}
            }
		}
		else {
			//Do we have it?
			if (isset($this->_config->$name))
			    $temp->$name = $this->_config->$name;
			//No? Then create it.
			else 
			    $temp->$name = $this->_get($name);
		}
		//Return config.
		return $temp;
	}
	
	/**
	 * Private function that returns config from object.
	 *
	 * @param string $name Config option name
	 * @return mix
	 * @since 0.3 Beta
	 */
	private function _get($name)
	{
        return $this->_config->$name = $this->_stripSlashes(get_option($name));
	}
	
	/**
	 * Function strips slashes.
	 *
	 * @param array|stdClass|string $input Input that need to be stripped
	 * @return array|stdClass|string Parsed input
	 * @since 0.3
	 */
	private function _stripSlashes($input)
	{
		if (is_object($input)) {
			foreach ($input as $k => $v) {
				if (is_array($v) || is_object($v)) {
					$input->$k = $this->_stripSlashes($v);
				}
				else {
					$input->$k = stripslashes($v);
				}
			}
			return $input;
		}
		elseif (is_array($input)) {
            foreach ($input as $k => $v) {
                if (is_array($v) || is_object($v)) {
                    $input[$k] = $this->_stripSlashes($v);
                }
                else {
                    $input[$k] = stripslashes($v);
                }
            }
            return $input;
		}
		else {
			return stripslashes($input);
		}
	}
	
	/**
	 * Function updates config.
	 *
	 * @param string|array $name Options names
	 * @param string|array $value Options values
	 * @since 0.3 Beta
	 */
	public function update($name, $value)
	{
		if (is_array($name)) {
			foreach ($name as $nameKey => $nameValue) {
				//Check if we have key in $value variable. If not use it as it is.
				$tempVal = isset($value[$nameKey]) ? $value[$nameKey] : $value;
				//Add option to database and current config.
				update_option($nameValue, $tempVal);
				$this->_updateOrAdd($nameValue, $tempVal);
			}
		}
		else {
			//Add option to database and current config.
			update_option($name, $value);
            $this->_updateOrAdd($name, $value);
		}
	}
	
    /**
     * Private function that updates/creates option in config object.
     *
     * @param string $name Option name
     * @param string $value Option value
     * @since 0.3
     */
    private function _updateOrAdd($name, $value)
    {
        $this->_config->$name = $this->_stripSlashes($value);
    }
	
    /**
     * Function deletes config entry.
     *
     * @param string|array $name Options names
     * @since 0.3
     */
	public function delete($name)
	{
		//Is array?
		if (is_array($name)) {
			//Loop.
			foreach ($name as $optionName) {
				//Delete from database and current config.
				delete_option($optionName);
				$this->_delete($optionName);
			}
		}
		else {
			//Delete from database and current config.
			delete_option($name);
			$this->_delete($name);
		}
		
	}
	
    /**
     * Private function that removes option from config object.
     *
     * @param string $name Option name
     * @since 0.3
     */
	private function _delete($name)
	{
		unset($this->_config->$name);
	}
	
    /**
     * Function creates entries in config.
     *
     * @param string|array $name Options names
     * @param string|array $value Options values
     * @param string|array $autoLoad Autoload values
     * @param string|array $deprecated Deprecated values
     * @since 0.3
     */
	public function create($name, $value, $autoLoad = 'no', $deprecated = '')
	{
		//Is array?
		if (is_array($name)) {
			//Loop.
			foreach ($name as $nameKey => $nameValue) {
				//Check if we have keys in other variables. If not use them as the are.
				$tempValue = isset($value[$nameKey]) ? $value[$nameKey] : $value;
				$tempAutoLoad = isset($autoLoad[$nameKey]) ? $autoLoad[$nameKey] : $autoLoad;
				$tempDeprecated = isset($deprecated[$nameKey]) ? $deprecated[$nameKey] : $deprecated;
				//Add option to database and current config.
				add_option($nameValue, $tempValue, $tempDeprecated, $tempAutoLoad);
				$this->_updateOrAdd($nameValue, $tempValue);
			}
		}
		else {
			//Add option to database and current config.
			add_option($name, $value, $deprecated, $autoLoad);
            $this->_updateOrAdd($name, $value);
		}
	}
}