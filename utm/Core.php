<?php
namespace Utm;

/**
 * Main framework class
 */
class Core 
{
    const           INI_PATH        = '../application/config/'; /* path to the config files */
    private static  $m_oInstance;                               /* framework object */
    public  static  $config         = [];                       /* framework configuration storage */
    private         $m_oRequest;                                /* request object */
    private         $m_oRespond;                                /* respond object */

    /**
     * Singleton pattern
     */
    private function __construct(){}

    /**
     * Framework creation
     * @return object \Utm\Core store the framework unique execution
     */
    public static function instance()
    {
        // if exists we return the current object
        if (true == isset(self::$m_oInstance)) {
            return self::$m_oInstance;
        }

        // load config
        self::loadIniFile();

        return self::$m_oInstance = new \Utm\Core();
    }

    /**
     * Get all ini files from config directory and merge the value
     */
    protected static function loadIniFile()
    {
        // getting all config ini files
        $l_aFile = glob(self::INI_PATH.'*.ini');
        if (0 === count($l_aFile)) {
            die('No configuration file when the utm.ini is at least required in the folder : '.self::INI_PATH);
        }
        
        foreach ($l_aFile AS $l_sFile) {
            self::$config = array_merge(self::$config, parse_ini_file($l_sFile, true));
        }
    }

    /**
     * Execute the framework
     */
    public function run()
    {
        // framework error handler
        set_error_handler('\Utm\CoreError::error_handler');

        try {
            echo 'framework running';
            
            $this->m_oRequest = new \Utm\CoreRequest(\Utm\Core::$config['request']);
            
            // On recupere les methodes publiques de chaque plugin
            \Utm\CorePlugin::initPlugin();
            // Emission du premier evenement
            \Utm\CorePlugin::emit('onStart');

            // Initialisation de l'objet request
            $this->m_oRequest->setRequest();
            \Utm\CorePlugin::emit('onPostRequest');
            
            // On execute la requete
            $this->execute($this->m_oRequest);
            
            // Emission de l'evenement onFinish
            \Utm\CorePlugin::emit('onFinish');
            
            echo 'framework ending';
            
        } catch (\Exception $e) {
            trigger_error($e, E_USER_ERROR);
        }
    }
    
    /**
     * Execute le controleur demandé par l'objet request
     * @param object $p_oRequest Objet contenant les élements de la requete
     **/
    public function execute(\Utm\CoreRequest $p_oRequest)
    {
        \Utm\CorePlugin::emit('onExecute');

        // Recuperation du controller aupres du finder
        $l_aCtrl = $this->findController($p_oRequest);
        $nameSpace = '\Utm\\'.self::$config['core']['controller_name']. '\\' . $l_aCtrl['class'];
        if (class_exists($nameSpace) && is_callable(array($nameSpace, $l_aCtrl['method']))) {
            $l_oInstance = new $nameSpace;
            call_user_func(array($l_oInstance, $l_aCtrl['method']));
        } else {
            throw New \Exception('La classe : '.$nameSpace.' ('.$l_aCtrl['path'].') doit contenir une methode '.$l_aCtrl['method']) ;
        }
    }
    
    public function findController(\Utm\CoreRequest $p_oRequest)
    {
        // Initialisation et raccourcis
        $l_aReturn    = false;
        $l_sModule    = $p_oRequest->getModule();// module
        $l_sClass     = ucfirst($p_oRequest->getController()).self::$config['core']['controller_name'];// controller name
        $l_sNameSpace = ($l_sModule) ? $l_sModule . self::$config['core']['module_separator'] : '';
        $l_sPath      = self::$config['path']['controller'].$l_sNameSpace.$l_sClass.'.php';
        
        if (file_exists($l_sPath)) {
            $l_aReturn['path']   = $l_sPath;
            $l_aReturn['class']  = ($l_sModule) ? $l_sModule.'\\'.$l_sClass : $l_sClass;
            $l_aReturn['method'] = $p_oRequest->getAction();
        } else {
            throw new \Exception("Le controlleur demandé n'existe pas, format accepté :" . $l_sPath);
        }
        
        return $l_aReturn;
    }

    public function registerPlugin()
    {
        // on place le plugin dans le tableau des plugins
        \Utm\CorePlugin::register(func_get_args());
    }

}
