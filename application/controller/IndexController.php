<?php
namespace Utm\Controller;
/**
 * Controller par defaut
 *
 * Tous les controlleurs doivent étendre coreControler pour profiter des
 * methodes natives du framework
 **/
class IndexController extends \Utm\CoreController
{    

    public function index() {
//        $form = coreModel::factory('form');
//        var_dump($form);
        echo 'yop';
    }
    public function systeme() { }
    public function erreur404() { }

    public function map() {
        if (!filter_has_var(INPUT_GET, 'cid')) {
            $this->redirect('index', 'systeme');
        } else {
            $_SESSION['produit'] = (int)$_GET['cid'];
        }
        
    }
    
    public function ajaxmap() {            
        
        $results = $this->query("SELECT * FROM `consigne` 
            WHERE 1 ORDER BY nom ASC");
        $formated_result = array();
        foreach ($results AS $result) {
            $formated_result[] = array(
                'locname' => $result['consigne'],
                'lat' => $result['lat'],
                'lng' => $result['long'],
                'address' => $result['adresse'],
                'address2' => '',
                'city' => $result['ville'],
                'postal' => $result['cp'],
                'hours1' => $result['id_consigne'],
                'hours2' => '',
            );
        }
        
        if (strlen($_GET['address'])) {
            $results = $this->query("SELECT * FROM `relai` 
                WHERE 1 ORDER BY nom ASC");
            foreach ($results AS $result) {
                $formated_result[] = array(
                    'locname' => $result['nom'],
                    'lat' => $result['lat'],
                    'lng' => $result['long'],
                    'address' => $result['adresse'],
                    'address2' => $result['adresse_complement'],
                    'city' => $result['ville'],
                    'postal' => $result['cp'],
                    'hours1' => '',
                    'hours2' => $result['id_relai'],
                );
            }
            
        }
        echo json_encode($formated_result);
        die();
        
    }
    
    public function consigne() {
        if ((!filter_has_var(INPUT_GET, 'id_consigne') && !isset($_SESSION['id_consigne'])) || !isset($_SESSION['produit'])) {
            $this->redirect('index', 'systeme');
        } else if (!isset($_SESSION['id_consigne'])) {
            $_SESSION['id_consigne'] = (int)$_GET['id_consigne'];
        }
        
        $form = coreModel::factory('form');
        $formKey = 'registration';
        $form->init($formKey, array('nom', 'prenom', 'telephone', 'email_registration', 'accept'));
        $user = coreModel::factory('user');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['op'] == 'conseiller') {
            
            $form->m_aFields[$formKey]['fields']['email_registration']['value'] = $_POST['email_registration'];
            if (!$form->notEmpty('email_registration')) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                $form->m_aFields[$formKey]['fields']['email_registration']['message'] = "L'email doit être renseigné.";
            } else if (!$form->isEmail($_POST['email_registration'], TRUE)) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                $form->m_aFields[$formKey]['fields']['email_registration']['message'] = "L'email saisi ne semble pas correct.";
            } else if ($user->exists('email', $_POST['email_registration'])) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                $form->m_aFields[$formKey]['fields']['email_registration']['message'] = "L'email saisi est déjà enregistré.";
            }
            
            $form->m_aFields[$formKey]['fields']['nom']['value'] = $_POST['nom'];
            if (!$form->notEmpty('nom') || !$form->isString(filter_input(INPUT_POST, 'nom'), 2, 100)) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                if (!$form->notEmpty('nom')) {
                    $form->m_aFields[$formKey]['fields']['nom']['message'] = 'Le nom doit être renseigné.';
                } else {
                    $form->m_aFields[$formKey]['fields']['nom']['message'] = 'Le nom doit être compris entre 2 et 100 caractères.';
                }
            }
            
            $form->m_aFields[$formKey]['fields']['prenom']['value'] = $_POST['prenom'];
            if (!$form->notEmpty('prenom') || !$form->isString(filter_input(INPUT_POST, 'prenom'), 2, 100)) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                if (!$form->notEmpty('prenom')) {
                    $form->m_aFields[$formKey]['fields']['prenom']['message'] = 'Le prénom doit être renseigné.';
                } else {
                    $form->m_aFields[$formKey]['fields']['prenom']['message'] = 'Le prénom doit être compris entre 2 et 100 caractères.';
                }
            }
            $form->m_aFields[$formKey]['fields']['telephone']['value'] = $_POST['telephone'];
            if ($form->notEmpty('telephone') && ((strlen($_POST['telephone']) > 16 || strlen($_POST['telephone']) < 10))) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                $form->m_aFields[$formKey]['fields']['telephone']['message'] = 'Le téléphone doit être compris entre 10 et 16 caractères.';
            }
            
            if (!$form->notEmpty('accept')) {
                $form->m_aFields[$formKey]['error'] = TRUE;
                $form->m_aFields[$formKey]['fields']['accept']['message'] = "Vous devez accepter le règlement du jeu-concours.";
            } else {
                $form->m_aFields[$formKey]['fields']['accept']['value'] = filter_input(INPUT_POST, 'accept');
            }
            
            if (!$form->m_aFields[$formKey]['error']) {
                $form->m_aFields[$formKey]['processed'] = TRUE;
                $form->m_aFields[$formKey]['fields']['optin']['value'] = 0;
                $user->create($form->m_aFields[$formKey]['fields']);
                $this->redirect('index', 'validation', NULL, array('succes' => true));
            }
        }
    }
    

}
