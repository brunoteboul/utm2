<?php
namespace Utm\Plugin;
/**
 *
 **/
class Debug extends \Utm\CorePlugin
{
    /**
     * Format l'affichage en fonction du type d'appel : HTTP ou CLI
     * @param $p_sString string Chaine de texte a afficher
     * @return string Chaine de texte formatté avec pre ou \n
     */
    protected function format($p_sString)
    {
        if ('cli' != PHP_SAPI) {
            $p_sString = '<pre>'.$p_sString.'</pre>';
        } else {
            $p_sString = "\n".$p_sString."\n";
        }
        return $p_sString;
    }

    /**
     * Affiche les infos de debug : memoire utilisée et temps d'execution avec
     * differentes methodes selon que l'on utilise xdebug ou pas
     * Cette methode se déclenche sur l'évenement onFinish
     */
    public function onFinish()
    {
        if (\Utm\Core::$config['error']['bench']) {
            if (true == extension_loaded('xdebug')) {
                // debug
                $l_sString = (xdebug_memory_usage()/1000).' Ko ('.(xdebug_peak_memory_usage()/1000).' Ko)'.round(xdebug_time_index(),3).' sec.';
            } else {
                $l_sString = (memory_get_usage(FALSE)/1000).' Ko ('.(memory_get_peak_usage(FALSE)/1000).' Ko)';
            }
            echo $this->format($l_sString);
        }
    }

    /**
     * Var_dump amélioré
     * @param $p_mElement Elements a dumper
     * @param $stop boolean Indique si on doit s'arreter apres le le dump ou pas
     */
    public function dbg($p_mElement, $stop = FALSE )
    {
        
        echo '<pre>'.var_dump($p_mElement).'</pre>';

        if (false != $stop){
            exit;
        }
    }
}