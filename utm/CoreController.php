<?php
namespace Utm;

class CoreController extends \Utm\CoreComponent
{
    protected function forward($p_sController, $p_sAction, $p_sModule = null, $p_aGet = null, $p_aPost = null, $p_aCli = null)
    {
        $l_oCore = \Utm\Core::instance();
        // On re-définit la requete puis on relance l'execution avec le nouvel objet request
        $l_oCore->resetRequest($p_sController,$p_sAction,$p_sModule,$p_aGet,$p_aPost,$p_aCli);
        $l_oCore->execute($l_oCore->getRequest());
    }
    
    protected function redirect($p_sController, $p_sAction, $p_sModule = null, $p_aGet = null)
    {
        if ('cli' == PHP_SAPI) {
            throw new \Exception('Cette methode '.__METHOD__.' ne peut etre appelée en ligne de commande');
        }

        $l_sUrl = (string)'';
        $l_aReq = \Utm\Core::$config['request'];
        
        if (null != $p_sModule) {
            $l_sUrl = $l_aReq['module'].'='.$p_sModule.'&'; 
        }
        $l_sUrl .= $l_aReq['controller'].'='.$p_sController.'&';
        $l_sUrl .= $l_aReq['action'].'='.$p_sAction.'&';

        // On ajoute les parametres GET
        if (null != $p_aGet) {
            if (false == is_array($p_aGet)) {
                throw new \Exception ('Les paramêtres fournis pour la redirection doivent etre un tableau');
            }
            foreach ($p_aGet AS $key => $value) {
                $l_sUrl .= $key .'='.$value .'&';
            }
        }

        // On procède à la redirection si les entêtes n'ont pas été envoyés
        if (false == headers_sent()) {
            if (isset(\Utm\Core::$config['site']['url'])) {
                header('Location:'.\Utm\Core::$config['site']['url'].'index.php?'.rtrim($l_sUrl, '&'));
                exit;
            } else {
                header('Location:index.php?'.rtrim($l_sUrl, '&'));
                exit;
            }
        } else {
            throw new \Exception('La redirection est impossible car les entêtes ont déjà été envoyés');
        }
    }
}
