<?php
/**
 * @package Dioscouri
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

class DSC extends JObject 
{
    protected $_name 		= 'dsc';
    static $_version 		= '1.0';
	static $_build          = 'r100';
	static $_versiontype    = 'community';
	static $_copyrightyear 	= '2011';	
	static $_min_php		= '5.2';
	
	/**
	* Get the version
	*/
	public static function getVersion()
	{
		$version = self::$_version;
		return $version;
	}
	
	/**
	 * Get the full version string
	 */
	public static function getFullVersion()
	{
		$version = self::$_version." ".JText::_( ucfirst(self::$_versiontype) )." ".self::$_build;
		return $version;
	}

	/**
	* Get the copyright year
	*/
	public static function getBuild()
	{
		return self::$_build;
	}
	
	/**
	 * Get the copyright year
	 */
	public static function getCopyrightYear()
	{
		return self::$_copyrightyear;
	}
	
	/**
	 * Get the Name
	 */
	public function getName()
	{
	    return $this->get('_name');
	}
	
	/**
	 * Get the Minimum Version of Php
	 */
	public static function getMinPhp()
	{
		//get version from PHP. Note this should be in format 'x.x.x' but on some systems will look like this: eg. 'x.x.x-unbuntu5.2'
		$phpV = self::getServerPhp();
		$minV = self::$_min_php;
		$passes = false;
	
		if ($phpV[0] >= $minV[0]) {
			if (empty($minV[2]) || $minV[2] == '*') {
				$passes = true;
			} elseif ($phpV[2] >= $minV[2]) {
				if (empty($minV[4]) || $minV[4] == '*' || $phpV[4] >= $minV[4]) {
					$passes = true;
				}
			}
		}
		//if it doesn't pass raise a Joomla Notice
		if (!$passes) :
		JError::raiseNotice('VERSION_ERROR',sprintf(JText::_('ERROR_PHP_VERSION'),$minV,$phpV));
		endif;
	
		//return minimum PHP version
		return self::$_min_php;
	}
	
	public static function getServerPhp()
	{
		return PHP_VERSION;
	}
	
	public static function getApp( $app=null, $find=true )
	{
		if (empty($app) && empty($find))
		{
			return new DSC();
		}
		
		if (empty($app) && !empty($find)) 
		{
			$app = JRequest::getCmd('option');
		}
		
		if (strpos($app, 'com_') !== false) {
			$app = str_replace( 'com_', '', $app );
		}
		
		if ( !class_exists($app) ) {
			JLoader::register( $app, JPATH_ADMINISTRATOR.DS."components".DS."com_" . $app . DS ."defines.php" );
		}
		
		return $app::getInstance();
	}

	/**
	* Get the URL to the folder containing all media assets
	*
	* @param string	$type	The type of URL to return, default 'media'
	* @return 	string	URL
	*/
	public static function getURL( $type = 'media', $com='' )
	{
	    $name = 'dioscouri';
	    if (!empty($com)) {
	        $app = self::getApp($com);
	        $name = "com_" . $app;
	    }
	    	    
	    $url = '';
	
	    switch ( $type )
	    {
	        case 'media':
	            $url = JURI::root( true ) . '/media/'.$name.'/';
	            break;
	        case 'css':
	            $url = JURI::root( true ) . '/media/'.$name.'/css/';
	            break;
	        case 'images':
	            $url = JURI::root( true ) . '/media/'.$name.'/images/';
	            break;
	        case 'js':
	            $url = JURI::root( true ) . '/media/'.$name.'/js/';
	            break;
	    }
	
	    return $url;
	}
	
	/**
	 * Get the path to the folder containing all media assets
	 *
	 * @param 	string	$type	The type of path to return, default 'media'
	 * @return 	string	Path
	 */
	public static function getPath( $type = 'media', $com='' )
	{
	    $name = 'dioscouri';
	    if (!empty($com)) {
	        $app = self::getApp($com);
	        $name = "com_" . $app;
	    }
	    
	    $path = '';
	
	    switch ( $type )
	    {
	        case 'media':
	            $path = JPATH_SITE . '/media/'.$name;
	            break;
	        case 'css':
	            $path = JPATH_SITE . '/media/'.$name.'/css';
	            break;
	        case 'images':
	            $path = JPATH_SITE . '/media/'.$name.'/images';
	            break;
	        case 'js':
	            $path = JPATH_SITE . '/media/'.$name.'/js';
	            break;
	    }
	
	    return $path;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public static function loadLibrary()
	{
		if (!class_exists('DSCLoader')) {
			jimport('joomla.filesystem.file');
			if (!JFile::exists(JPATH_SITE.'/libraries/dioscouri/loader.php')) {
				return false;
			}
			require_once JPATH_SITE.'/libraries/dioscouri/loader.php';
		}
		
		$parentPath = JPATH_SITE . '/libraries/dioscouri/library';
		DSCLoader::discover('DSC', $parentPath, true);
		
		$doc = JFactory::getDocument( );
		$uri = JURI::getInstance( );
		$js = "Dsc.jbase = '" . $uri->root( ) . "';\n";
		$doc->addScript( DSC::getURL('js') . 'common.js' );
		$doc->addScriptDeclaration( $js );
				
		return true;
	}
	
	/**
	* Adds the Highcharts library files to the autoloader
	* and adds the highcharts js file to the stack
	*
	*/
	public static function loadHighcharts()
	{
	    jimport('dioscouri.highroller.highroller.highroller');
	    jimport('dioscouri.highroller.highroller.highrollerareachart');
	    jimport('dioscouri.highroller.highroller.highrollerareasplinechart');
	    jimport('dioscouri.highroller.highroller.highrollerbarchart');
	    jimport('dioscouri.highroller.highroller.highrollercolumnchart');
	    jimport('dioscouri.highroller.highroller.highrollerlinechart');
	    jimport('dioscouri.highroller.highroller.highrollerpiechart');
	    jimport('dioscouri.highroller.highroller.highrollerscatterchart');
	    jimport('dioscouri.highroller.highroller.highrollerseriesdata');
	    jimport('dioscouri.highroller.highroller.highrollersplinechart');
	     
	    JHTML::_( 'script', 'highcharts.js', 'libraries/dioscouri/highroller/highcharts/' );
	}
	
	/**
	* Method to dump the structure of a variable for debugging purposes
	*
	* @param	mixed	A variable
	* @param	boolean	True to ensure all characters are htmlsafe
	* @return	string
	* @since	1.5
	* @static
	*/
	public static function dump( $var, $ignore_underscore = true, $htmlSafe = true )
	{
	    if (!$ignore_underscore)
	    {
	        $result = print_r( $var, true );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }
	     
	    if (!is_object($var) && !is_array($var))
	    {
	        $result = print_r( $var, true );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }
	     
	    // TODO do a recursive remove of underscored keys
	     
	    if (is_object($var))
	    {
	        $keys = get_object_vars($var);
	        foreach ($keys as $key=>$value)
	        {
	            if (substr($key, 0, 1) == '_')
	            {
	                unset($var->$key);
	            }
	            else
	            {
	                if (is_object($var->$key))
	                {
	                    $sub_keys = get_object_vars($var->$key);
	                    foreach ($sub_keys as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var->$key->$sub_key);
	                        }
	                    }
	                }
	                elseif (is_array($var->$key))
	                {
	                    foreach ($var->$key as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var->$key[$sub_key]);
	                        }
	                    }
	                }
	            }
	             
	             
	        }
	        $result = @print_r( $var, true );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }
	     
	    if (is_array($var))
	    {
	        foreach ($var as $key=>$value)
	        {
	            if (substr($key, 0, 1) == '_')
	            {
	                unset($var[$key]);
	            }
	            else
	            {
	                if (is_object($var[$key]))
	                {
	                    $sub_keys = get_object_vars($var[$key]);
	                    foreach ($sub_keys as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var[$key]->$sub_key);
	                        }
	                    }
	                }
	                elseif (is_array($var[$key]))
	                {
	                    foreach ($var[$key] as $sub_key=>$sub_key_value)
	                    {
	                        if (substr($sub_key, 0, 1) == '_')
	                        {
	                            unset($var[$key][$sub_key]);
	                        }
	                    }
	                }
	            }
	        }
	        $result = @print_r( $var, true );
	        return '<pre>'.( $htmlSafe ? htmlspecialchars( $result ) : $result).'</pre>';
	    }
	}
	
	/**
	 * Method to intelligently load class files in the framework
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return boolean
	 */
	public static function load( $classname, $filepath='library', $options=array( 'site'=>'site', 'type'=>'libraries', 'ext'=>'dioscouri' ) )
	{
	    $classname = strtolower( $classname );
	    if(version_compare(JVERSION,'1.6.0','ge')) {
	        // Joomla! 1.6+ code here
	        $classes = JLoader::getClassList();
	    } else {
	        // Joomla! 1.5 code here
	        $classes = JLoader::register();
	    }
	
	    if ( class_exists($classname) || array_key_exists( $classname, $classes ) )
	    {
	        // echo "$classname exists<br/>";
	        return true;
	    }
	
	    static $paths;
	
	    if (empty($paths))
	    {
	        $paths = array();
	    }
	
	    if (empty($paths[$classname]) || !is_file($paths[$classname]))
	    {
	        // find the file and set the path
	        if (!empty($options['base']))
	        {
	            $base = $options['base'];
	        }
	        else
	        {
	            // recreate base from $options array
	            switch ($options['site'])
	            {
	                case "site":
	                    $base = JPATH_SITE.DS;
	                    break;
	                default:
	                    $base = JPATH_ADMINISTRATOR.DS;
	                break;
	            }
	
	            $base .= (!empty($options['type'])) ? $options['type'].DS : '';
	            $base .= (!empty($options['ext'])) ? $options['ext'].DS : '';
	        }
	
	        $paths[$classname] = $base.str_replace( '.', DS, $filepath ).'.php';
	    }
	
	    // if invalid path, return false
	    if (!is_file($paths[$classname]))
	    {
	        // echo "file does not exist<br/>";
	        return false;
	    }
	
	    // if not registered, register it
	    if ( !array_key_exists( $classname, $classes ) )
	    {
	        // echo "$classname not registered, so registering it<br/>";
	        JLoader::register( $classname, $paths[$classname] );
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Intelligently loads instances of classes in framework
	 *
	 * Usage: $object = DSC::getClass( 'DSCHelperCarts', 'helpers.carts' );
	 * Usage: $suffix = DSC::getClass( 'DSCHelperCarts', 'helpers.carts' )->getSuffix();
	 * Usage: $categories = DSC::getClass( 'DSCSelect', 'select' )->category( $selected );
	 *
	 * @param string $classname   The class name
	 * @param string $filepath    The filepath ( dot notation )
	 * @param array  $options
	 * @return object of requested class (if possible), else a new JObject
	 */
	public static function getClass( $classname, $filepath='library', $options=array( 'site'=>'site', 'type'=>'libraries', 'ext'=>'dioscouri' )  )
	{
	    if (self::load( $classname, $filepath, $options ))
	    {
	        $instance = new $classname();
	        return $instance;
	    }
	
	    $instance = new JObject();
	    return $instance;
	}
	
	/**
	* Retrieves the data
	* @return array Array of objects containing the data from the database
	*/
	public function getData() 
	{
	    // load the data if it doesn't already exist
	    if (empty( $this->_data )) {
	        $database = JFactory::getDBO();
	        $query = $this->_buildQuery();
	        $database->setQuery( $query );
	        $this->_data = $database->loadObjectList();
	    }
	
	    return $this->_data;
	}
	
	/**
	 * Set Variables
	 *
	 * @acces	public
	 * @return	object
	 */
	public function setVariables() 
	{
	    $success = false;

	    if ( $data = $this->getData() )
	    {
	        for ($i=0; $i<count($data); $i++)
	        {
	            $title = $data[$i]->config_name;
	            $value = $data[$i]->value;
	            if (!empty($title)) {
	                $this->$title = $value;
	            }
	        }

	        $success = true;
	    }

	    return $success;
	}
}
?>
