<?php
namespace Utm;

class CorePlugin extends \Utm\CoreComponent
{
    static public $m_aPlugin = []; /*!< Tableau contenant la liste des methodes et des classes associées */
    static public $m_aRegistredPlugin = []; /*<! Tableau contenant la liste des plugins chargés */

    /**
     * Enregistre les plugins dans la liste des plugins
     * @param $p_aPlugin array Tableau des plugins a enregistrer
     */
    public static function register($p_aPlugin)
    {
        foreach ($p_aPlugin AS $l_sValue) {
            if (true == is_string($l_sValue)) {
                self::$m_aRegistredPlugin[] = $l_sValue;
            }
        }
    }

    /**
     * Recupere toutes les méthodes publiques des plugins : evenements/extension
     * Affecte la liste des methodes(clés) et des classes associés(valeurs)
     **/
    public static function initPlugin()
    {
        // On recupere pour chaque plugin enregistré ses methodes publiques
        foreach (self::$m_aRegistredPlugin AS $plugin) {
            $l_aMethod = get_class_methods($plugin);
            if (false == is_array($l_aMethod)) {
                throw new \Exception ('Le plugin '.$plugin.' n\'existe pas, ou ne contient aucune methode publique');
            }
            foreach ($l_aMethod AS $l_sMethod) {
                self::$m_aPlugin[$l_sMethod][] = $plugin;
            }
        }
    }
    
    /**
     * Emet un evenement, et execute toutes les methodes associées
     * @todo Doit on permettre de passer des parametres a un evenement ?
     * @param $p_sEvent string Evenement déclenché
     **/
    public static function emit($p_sEvent)
    {
        // Si il existe une methode dans les plugins chargés correspondants à
        // l'événement déclenché, on l'execute.
        if (true == isset(self::$m_aPlugin[$p_sEvent])) {
            // On la recherche dans tous les plugins
            foreach (self::$m_aPlugin[$p_sEvent] AS $l_sClass) {
                // On verifie si l'objet n'existe pas deja dans le registre,
                // sinon on l'instancie et on le stock
                if (true == \Utm\CoreRegistry::exists($l_sClass, \Utm\Core::$config['registry']['plugin'])) {
                    $l_oPlugin = \Utm\CoreRegistry::get($l_sClass, \Utm\Core::$config['registry']['plugin']);
                } else {
                    $l_oPlugin = new $l_sClass();
                    \Utm\CoreRegistry::set($l_sClass, $l_oPlugin, \Utm\Core::$config['registry']['plugin']);
                }
                // On execute la methode du plugin
                call_user_func(array($l_oPlugin, $p_sEvent));
            }
        }
    }

    /**
     * Indique si le plugin demandé est chargé, utile pour la dépendance entre
     * plugin
     * @param string $p_sPlugin Nom du plugin a vérifier
     * @return boolean TRUE si le plugin est chargé, FALSE si il ne l'est pas
     */
    public function isLoaded($p_sPlugin)
    {
        return in_array($p_sPlugin, self::$m_aRegistredPlugin);
    }
}
