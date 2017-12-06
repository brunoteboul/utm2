<?php
namespace Utm;

class CoreRegistry extends \Utm\CoreComponent
{
    const GENERAL = 'ns_general'; /*!< Espace de nom par defaut dans le registre */
    static private $m_aStore = []; /*!< tableau contenant le registre */
    
    /**
     * Accesseur definissant un élément
     *
     * @param $p_sLabel string clé du tableau de registre
     * @param $p_sValue array, string, object Valeur à enregistrer
     * @param $p_sNameSpace string  Namespace dans lequel placé la valeur pour
     * eviter la surcharge accidentelle.
     **/
    public static function set($p_sLabel, $p_sValue, $p_sNameSpace = self::GENERAL)
    {
        self::$m_aStore[$p_sNameSpace][$p_sLabel] = $p_sValue;
    }
    
    /**
     * Accesseur recuperant la valeur d'un élément
     *
     * @param $p_sLabel string Clé du tableau de registre
     * @param $p_sNameSpace string Namespace dans lequel placé la valeur pour
     * eviter la surcharge accidentelle
     * @return array, string, object : valeur enregistrée ou FALSE
     **/
    public static function get($p_sLabel, $p_sNameSpace = self::GENERAL)
    {
        return (true == self::exists($p_sLabel, $p_sNameSpace)) ? self::$m_aStore[$p_sNameSpace][$p_sLabel] : false;
    }
    
    /**
     * Verifie si une variable est enregistrée
     * @param $p_sVar string Variable dont on souhaite verifier l'existance
     * @param $p_sNameSpace string Namespace dans lequel placé la valeur pour
     * eviter la surcharge accidentelle
     * @return boolean TRUE / FALSE 
     **/
    public static function exists($p_sVar, $p_sNameSpace = self::GENERAL)
    {
        return isset(self::$m_aStore[$p_sNameSpace][$p_sVar]);
    }

    /**
     * Efface une entrée du registre
     * @param $p_sVar string Label de l'élément que l'on souhaite effacer
     * @param $p_sNameSpace string Namespace dans lequel placé la valeur pour
     * eviter la surcharge accidentelle
     */
    public static function erase($p_sVar, $p_sNameSpace = self::GENERAL)
    {
        if (true == self::exists($p_sVar, $p_sNameSpace)) {
            unset(self::$m_aStore[$p_sNameSpace][$p_sVar]);
        }
    }

    /**
     * Cette methode renvoi le registre complet a des fins de debug
     * @param $p_sElement string Index présent dans le registre
     * @return array $m_aStore Variable static contenant l'ensemble ou une
     * partie du registre
     */
    public static function dump($p_sElement=NULL)
    {
        if (null != $p_sElement && true == isset(self::$m_aStore[$p_sElement])) {
            return self::$m_aStore[$p_sElement]; 
        }
        return self::$m_aStore;
    }
}
