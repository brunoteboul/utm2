<?php
namespace Utm;

class CoreModel extends \Utm\CoreComponent
{
    
    public static function factory($p_sClass, $p_aParam = null, $p_bNew = false)
    {
        // Utilisation d'un singleton basé sur le registre pour mettre en cache
        // l'objet model instancié, sauf si le parametre $p_bNew est faux
        if (true == \Utm\CoreRegistry::exists($p_sClass, core::$config['registry']['model']) &&
            false == $p_bNew) {
            return \Utm\CoreRegistry::get($p_sClass, core::$config['registry']['model']);
        }

        if (true == file_exists(core::$config['path']['model'].$p_sClass.'.php')) {
            require_once core::$config['path']['model'].$p_sClass.'.php';
        } else {
            throw new \Exception('La classe modèle :'. core::$config['path']['model'].$p_sClass.'.php est introuvable');
        }
        
        if (null != $p_aParam) {
            $l_oClass = new $p_sClass(implode(',', $p_aParam));
        } else {
            $l_oClass = new $p_sClass();
        }
        
        \Utm\CoreRegistry::set($p_sClass, $l_oClass, \Utm\Core::$config['registry']['model']);
        return $l_oClass;
    }
}
