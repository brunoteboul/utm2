<?php
namespace Utm;

/**
 * Framework request object
 */
class CoreRequest extends \Utm\CoreComponent
{

    const HTTP = 1; /*!< Indique si on accede au framework par un navigateur*/
    const CLI  = 2; /*!< Indique si on accede au framework en ligne de commande*/
    const AJAX = 3; /*!< Indique si on accede au framework en ajax */

    protected $m_aReqElement = [];      /*!< Elements constituants la requete*/
    protected $m_sRequestType;          /*!< Indique si la requete est de type HTTP ou CLI*/
    protected $m_sModule;               /*!< Elements module de la requete*/
    protected $m_sController;           /*!< Elements controlleur de la requete*/
    protected $m_sAction;               /*!< Elements action de la requete*/
    protected $m_aGet;                  /*!< Elements Get de la requete*/
    protected $m_aPost;                 /*!< Elements Post de la requete*/
    protected $m_aCli;                  /*!< Elements CLI de la requete*/

    public function __construct(array $p_aReqElement)
    {
        $this->m_aReqElement = array_flip($p_aReqElement);
        
        if (PHP_SAPI=='cli') {
            $this->m_sRequestType =self::CLI;
        } else if(true == $this->isAjax()) {
            $this->m_sRequestType =self::AJAX;
        } else {
            $this->m_sRequestType =self::HTTP;
        }
        // On definit la valeur par defaut d'un controller et de l'action
        $this->m_sController = core::$config['request']['default'];
        $this->m_sAction = core::$config['request']['default'];
    }
    
    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && preg_match('#xmlhttprequest#i', $_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /**
     * Parse l'url pour créer l'objet request utilisable par le framework
     */
    public function httpParser()
    {
        parse_str($_SERVER['QUERY_STRING'] , $l_aQuery);
        return $l_aQuery;
    }

    /**
     * Acces CLI (Ligne de commande) au framework
     * Parse la requete et renvoi un tableau contenant ses éléments
     * @todo Dans les futurs version s de PHP on pourra implémenter la meme
     * syntaxe qu'une commande PHP ex: --param value --param2 value etc.
     * @return array Tableau contenant les éléments de la requete
     **/
    protected function cliParser()
    {
        $l_aQuery = array();
        // On recupere chaque valeur fournie sous la forme key=value
        for ($i=1 ; $i<$_SERVER['argc'] ; $i++) {
            parse_str($_SERVER['argv'][$i], $l_aTemp);
            $l_aQuery = array_merge($l_aQuery, $l_aTemp);
        }
        return $l_aQuery;
    }

    /**
     * On definit les membres de l'objet request(Type, elements, params, etc.)
     */
    public function setRequest()
    {
        if ($this->m_sRequestType == self::HTTP) {
            $l_aQuery = $this->httpParser();
            $this->m_aGet = $_GET;
            $this->m_aPost = $_POST;
        } else{
            $l_aQuery = $this->cliParser();
            $this->m_aCli = $_SERVER['argv'];
        }

        // On parcours le tableau afin d'y retrouver les clés definies en config
        foreach ($this->m_aReqElement AS $key => $value) {
            if (true == array_key_exists($key, $l_aQuery) 
                && true == is_string($l_aQuery[$key])) {
                if ('module' == $value) {
                    $this->m_sModule = strip_tags($l_aQuery[$key]);
                }
                if ('controller' == $value) {
                    $this->m_sController = strip_tags($l_aQuery[$key]);
                }
                if ('action' == $value) {
                    $this->m_sAction = strip_tags($l_aQuery[$key]);
                }
            }
        }
    }

    /**
     * On remplit l'objet request en fonction d'une requete supplémentaire
     * @return array Tableau request
     */
    public function setFakeRequest($p_sController, $p_sAction, $p_sModule = null, $p_aGet = null, $p_aPost = null, $p_aCli = null)
    {
        $this->m_sModule        = strip_tags($p_sModule);
        $this->m_sController    = strip_tags($p_sController);
        $this->m_sAction        = strip_tags($p_sAction);
        $this->m_aGet           = (null != $p_aGet && true == is_array($p_aGet)) ? $p_aGet : null;
        $this->m_aPost          = (null != $p_aPost && true == is_array($p_aPost)) ? $p_aPost : null;
        $this->m_aCli           = (null != $p_aCli && true == is_array($p_aCli))? $p_aCli : null;
    }

    /**
     * Accesseurs
     */
    public function getModule()
    {
        return $this->m_sModule;
    }
    public function getController()
    {
        return $this->m_sController;
    }
    public function getAction()
    {
        return $this->m_sAction;
    }
    public function getMethod()
    {
        return $this->m_sRequestType;
    }
    public function getRequest()
    {
        return array($this->m_sController, $this->m_sAction, $this->m_sModule, $this->m_aGet, $this->m_aPost, $this->m_aCli);
    }

    /**
     *
     * @param <type> $p_sElement
     * @return array
     */
    public function getInput($p_sElement = 'get')
    {
        $l_aInputs = [
            'get' => 'm_aGet',
            'post'=> 'm_aPost',
            'cli' => 'm_aCli',
        ];
        
        if (false == isset($l_aInputs[$p_sElement])) {
            throw New exception('Invalid method request'); 
        }
        
        $l_sMethod = $l_aInputs[strtolower($p_sElement)];
        if (true == isset($this->$l_sMethod)) {
            return $this->$l_sMethod;
        }
        return false;
    }
}
