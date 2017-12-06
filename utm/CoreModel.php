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

        $nameSpace = '\Utm\\Model\\' . $p_sClass;

        $l_oClass = new $nameSpace;
        
        \Utm\CoreRegistry::set($p_sClass, $l_oClass, \Utm\Core::$config['registry']['model']);
        return $l_oClass;
    }
}
