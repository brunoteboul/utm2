<?php
namespace Utm\Plugin;
/**
 * Surcharge le système du coeur pour le chargement des configurations du
 * framework. Permet de définir les variables de conf différents environnements.
 * Le plugin repose sur une section [env] dans les fichiers de conf :
 * [env]
 * prod         = urldeprod
 * preprod      = urldepreprod
 * dev          = urldedev
 */
class Config extends \Utm\CorePlugin
{

    protected   $url;     // url autodetectée du site
    public      $env;     // environnement d'execution
    
    /**
     * Detection de l'url de l'application
     * @return string Url normalisée 
     */
    public function getUrl() 
    {
        $url = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : null) . '://' . $_SERVER['HTTP_HOST'];

        return rtrim($url,'/');
    }

    /**
     * réagit à l'evenement onstart du framework
     */
    public function onStart()
    {
        $this->url = $this->getUrl();// récupération de l'url du site
        
        // si un tableau des environnements est disponible dans la conf on
        // vérifit dans lequel on se trouve et on recharge la conf en fonction
        if (isset(\Utm\Core::$config['env']) && is_array(\Utm\Core::$config['env'])) {
            
            foreach (\Utm\Core::$config['env'] AS $key => $value) {
                $val = explode('||', $value);
                if (in_array($this->url, $val)) {
                    $this->env = $key;
                    continue;// on a trouvé l'environnement du coup on sort de la boucle
                }
            }
            
            if ($this->env) {
                $this->setEnv();// si l'environnement est définit on réécrit la conf
                \Utm\Core::$config['env']['current'] = $this->env;
            }
        }
    }
    
    /**
     * Traitement du tableau de config pour être récrit en fonction de 
     * l'environnement, au lieu d'avoir dans les .ini des fichiers de conf des
     * variables de type
     */
    protected function setEnv()
    {
        // on parcour le tableau de config
        foreach (\Utm\Core::$config AS $key => $value) {
            // on parcour chaque section du .ini
            foreach ($value AS $subKey => $subValue) {
                // si on trouve un . dans la clé 
                if (strpos($subKey, '.')) {
                    $split = explode('.', $subKey);
                    if ($split[0] == $this->env) {
                        // l'environnement de la variable de conf correspond à l'actuel
                        // on ajoute une dimension dans le tableau php en fonction de l'environnement
                        \Utm\Core::$config[$key][$split[1]] = $subValue;
                    } 
                    // on supprime l'entrée de la conf
                    unset(\Utm\Core::$config[$key][$subKey]);
                }                
            }               
        }
    }

}