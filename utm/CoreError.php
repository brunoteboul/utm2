<?php
namespace Utm;

class CoreError extends \Utm\CoreComponent
{
    private static $m_sHtmlErrorMessage; /*!< Message d'erreur si généré en HTML*/
    private static $m_sTextErrorMessage; /*!< Message d'erreur si généré en TEXT*/

    /**
     * Renvoi le message d'erreur présenté et formaté
     * @param $p_sError string Numero de l'erreur
     * @return string variable formatée
     **/
    protected static function getMessage($p_sError)
    {
        switch($p_sError){
            default:
            case E_ERROR:
            $errname = 'E_ERROR ('.E_ERROR.')  Erreur'; break;
            case E_WARNING:
            $errname = 'E_WARNING ('.E_WARNING.')  Alerte'; break;
            case E_PARSE:
            $errname = 'E_PARSE ('.E_PARSE.')  Erreur d\'analyse'; break;
            case E_NOTICE:
            $errname = 'E_NOTICE ('.E_NOTICE.')  Note'; break;
            case E_CORE_ERROR:
            $errname = 'E_CORE_ERROR ('.E_CORE_ERROR.') PHP Core error'; break;
            case E_CORE_WARNING:
            $errname = 'E_CORE_WARNING ('.E_CORE_WARNING.') PHP Core warning'; break;
            case E_COMPILE_ERROR:
            $errname = 'E_COMPILE_ERROR ('.E_COMPILE_ERROR.')  Erreur de compilation'; break;
            case E_COMPILE_WARNING:
            $errname = 'E_COMPILE_WARNING ('.E_COMPILE_WARNING.')  Avertissement de compilation'; break;
            case E_USER_ERROR:
            $errname = 'E_USER_ERROR ('.E_USER_ERROR.')  Erreur utilisateur'; break;
            case E_USER_WARNING:
            $errname = 'E_USER_WARNING ('.E_USER_WARNING.')  Avertissement utilisateur'; break;
            case E_USER_NOTICE:
            $errname = 'E_USER_NOTICE ('.E_USER_NOTICE.')  Note utilisateur'; break;
            case E_STRICT:
            $errname = 'E_STRICT ('.E_STRICT.')  Note strict'; break;
            case E_RECOVERABLE_ERROR:
            $errname = 'E_RECOVERABLE_ERROR ('.E_RECOVERABLE_ERROR.')  Erreur fatale'; break;
            case E_ALL:
            $errname = 'E_ALL ('.E_ALL.') '; break;
        }
        return $errname;
    }

    /**
     * Gestionnaire d'erreurs
     * Si la config le permet il affiche l'erreur.
     * Un evenement onError est emis et l'erreur est placée dans la variable
     * error_message du registre
     * @param $p_sNo int Numero de l'erreur
     * @param $p_sMess string message d'erreur
     * @param $p_sFile string fichier d'ou l'erreur est partie
     * @param $p_sLine string Numero de la ligne de l'erreur
     * @param $p_sVars string tableau contenant les valeurs des variables au moment
     * de l'erreur
     **/
    public static function error_handler($p_sNo, $p_sMess, $p_sFile, $p_sLine, $p_sVars)
    {
        //Mise en forme du message
        $l_sErrMess = preg_replace('/exception \'Exception\' with message /', '', wordwrap($p_sMess,110));
        $l_sErrMess = preg_replace('/Stack trace/', "\nPile d'appels des methodes ", $l_sErrMess);

        // Creation du message d'erreur au format TXT
        self::$m_sTextErrorMessage = "Erreur :\t ".self::getMessage($p_sNo)."
Fichier : \t".$p_sFile."
Ligne : \t".$p_sLine."
Message : \t".$l_sErrMess."\n\n";

        // Création si possible du message d'erreur au format HTML
        if ('cli' != PHP_SAPI) {
        $id = uniqid();
        self::$m_sHtmlErrorMessage = "
<pre style=\"padding:15px;margin:0;border-top:1px dashed #900;border-bottom:1px dashed #900;\">
    Erreur \t: <b style=\"color:#900\">".self::getMessage($p_sNo)."</b>
    URL \t: <b style=\"color:#900\"><a href=\"".htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8')."\">".htmlspecialchars($_SERVER['REQUEST_URI'],ENT_QUOTES,'UTF-8')."</a></b>
    Fichier \t: <b style=\"color:#900\">".$p_sFile."</b>
    Ligne \t: <b style=\"color:#900\">".$p_sLine."</b>
    Message \t: <b style=\"color:#900\">".$l_sErrMess."</b>
</pre>";
        }

        // On emet l'evenement onError
        \Utm\CorePlugin::emit('onError');

        if (true == isset(\Utm\Core::$config['error']['display']) &&
            true == \Utm\Core::$config['error']['display']) {
            echo ('cli' != PHP_SAPI) ? self::$m_sHtmlErrorMessage : self::$m_sTextErrorMessage;
        }
    }

    /**
     * accesseurs
     * @param string $p_sFormat : HTML ou TEXT
     * @return string message d'erreur
     **/
    public static function getError($p_sFormat = 'html')
    {
        return (strtolower($p_sFormat) == 'html' && FALSE == empty(self::$m_sHtmlErrorMessage)) 
               ? self::$m_sHtmlErrorMessage
               : self::$m_sTextErrorMessage;
    }
}
