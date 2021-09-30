<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class JuanModule extends Module
{
    protected $config_form = false;
    private  $globalProductName ="producto1";
    private  $globalProductId ="0";

    public function __construct()
    {
        $this->name = 'juanmodule';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Juande';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Modulo de Prueba');
        $this->description = $this->l('este es el primer modulo de prueba');

        $this->confirmUninstall = $this->l('estás seguro que deseas desinstalar?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayFooterBefore');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }
    public function getContent()
    {
        return $this->postProcess().$this->getForm();
    }
    
    private function postProcess()
    {
        if(Tools::isSubmit('get_data_from_laravel')){
            $productId = Tools::getValue('productId');
            $data = json_decode( file_get_contents('https://6fed-2800-cd0-4404-3800-b9fb-7a8b-326d-b485.ngrok.io/api/product?product_id='.$productId), true );
            $this->globalProductName=$data['name'];
            $this->globalProductId=$data['id'];
            return;          
        }
    }
    public function HookDisplayFooterBefore(){
     $productName=$this->globalProductName;
    $this->context->smarty->assign(['productName'=>$productName]);
    
  
    return $this->display(__FILE__,'product.tpl'); //busca el template en la ubicacion por defecto /templates/hook/product.tpl 

    }//end HookDisplayAfterProductThumbs

    private function getForm()
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->nameController= $this->name;
        $helper->identifier = $this->identifier;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->getLanguages();
        $helper->currentIndex = AdminController::$currentIndex.'&configure=' . $this->name;
        $helper->defaultFormLanguage = $this->context->controller->default_form_language;
        $helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
        $helper->title = $this->displayName;
        $helper->submit_action = 'get_data_from_laravel';
        
        $helper->fields_value = [];

        $form[] = array(
            'form'=>array(
                'legend'=>array(
                    'title'=> $this->l('obtener datos de un producto')
                ),
                'input' => array(
                    array(
                        'type'=>'text',
                        'name'=>'productId',
                        'label'=>$this->l('ingresa el ID del producto'),
                        'desc'=>'el nombre producto con id= '.$this->globalProductId.' es '. $this->globalProductName

                    ),
                ),
                'submit'=>array(
                    'title'=>$this->l('BUSCAR')
                ),

            )
        );

        return $helper->generateForm($form);
    }
    



}//end class
