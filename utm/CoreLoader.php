<?php
namespace Utm;

spl_autoload_register('\Utm\CoreLoader::Core');
spl_autoload_register('\Utm\CoreLoader::Plugin');
spl_autoload_register('\Utm\CoreLoader::Controller');
spl_autoload_register('\Utm\CoreLoader::Model');
spl_autoload_register('\Utm\CoreLoader::Library');
spl_autoload_register('\Utm\CoreLoader::ModuleController');

/**
 * Autoloading class for the framework core, plugin, model, library classes.
 */
class CoreLoader 
{
    /**
     * AutoLoad for framework core classes
     */
    public static function Core($class)
    {
        $nameSpace = 'Utm\\';
        $baseDir = __DIR__.DIRECTORY_SEPARATOR;

        self::LoadClass($nameSpace, $baseDir, $class);
    }
    
    /**
     * AutoLoad for framework plugin classes
     */
    public static function Plugin($class)
    {
        $nameSpace = 'Utm\\Plugin\\';
        $baseDir = \Utm\Core::$config['path']['plugin'];

        self::LoadClass($nameSpace, $baseDir, $class);
    }
    
    /**
     * AutoLoad for framework controller classes
     */
    public static function Controller($class)
    {
        $nameSpace = 'Utm\\Controller\\';
        $baseDir = \Utm\Core::$config['path']['controller'];

        self::LoadClass($nameSpace, $baseDir, $class);
    }
    
    /**
     * AutoLoad for framework model classes
     */
    public static function Model($class)
    {
        $nameSpace = 'Utm\\Library\\';
        $baseDir = \Utm\Core::$config['path']['model'];

        self::LoadClass($nameSpace, $baseDir, $class);
    }
    
    /**
     * AutoLoad for framework library classes
     */
    public static function Library($class)
    {
        $nameSpace = 'Utm\\Library\\';
        $baseDir = \Utm\Core::$config['path']['lib'];

        self::LoadClass($nameSpace, $baseDir, $class);
    }
    
    /**
     * AutoLoad for framework module controller classes
     */
    public static function ModuleController($class)
    {
        $nameSpace = 'Utm\\Controller\\';
        $len = strlen($nameSpace);
        
        if (0 === strncmp($nameSpace, $class, $len)) {
            $moduleFolder = str_replace($nameSpace, '', $class);
            $moduleFolder = explode('\\', $moduleFolder);
            if (is_array($moduleFolder) && true == strlen($moduleFolder[0])) {
                $baseDir = \Utm\Core::$config['path']['controller'].$moduleFolder[0].'/';
                var_dump($baseDir);
                self::LoadClass($nameSpace, $baseDir, $class);
            }
        } else {
            return;
        }
    }
    
    protected static function LoadClass($nameSpace, $baseDir, $class) 
    {
        $len = strlen($nameSpace);
        if (0 !== strncmp($nameSpace, $class, $len)) {
            return;
        }
        
        $fileClass = $baseDir . str_replace('\\', '/', substr($class, $len)) . '.php';

        if (file_exists($fileClass)) {
            require_once $fileClass;
        } else {
            var_dump($fileClass);
        }
    }
}
