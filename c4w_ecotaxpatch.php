<?php
/**
 * 2007-2018 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use EcotaxPatch\Form\Admin\Product\ProductEcotax;
//use Symfony\Component\Form\Extension\Core\Type as FormType;

class C4w_EcotaxPatch extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'c4w_ecotaxpatch';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Rémi d\'Almeida';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Ecotax Patch');
        $this->description = $this->l('Fixes some Prestashop bug and adds new features to rule the ecotax');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        //Configuration::updateValue('C4W_ECOTAXPATCH_LIVE_MODE', false);

        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install() &&
            $this->registerHook(
                array(
                    'displayAdminProductsPriceStepBottom',
                    'actionProductSave',
                )
            );
    }

    public function uninstall()
    {
        //Configuration::deleteByName('C4W_ECOTAXPATCH_LIVE_MODE');

        include(dirname(__FILE__) . '/sql/uninstall.php');

        return parent::uninstall();
    }

    public function hookDisplayAdminProductsPriceStepBottom($hookParams)
    {
        if ($this->isSymfonyContext() && Configuration::get('PS_USE_ECOTAX')) {
            $productEcotax = $this->get('ecotax_product_repository')->findEcotaxByProductId($hookParams['id_product']);
            $data = array('ecotax_taxincl' => empty($productEcotax) ? '0.000000' : $productEcotax[0]['ecotax_taxincl']);
            $formBuilder = $this->get('form.factory')->createBuilder(ProductEcotax::class, $data, array('empty_data' => '0'));
            $form = $formBuilder->getForm();
            return $this->get('twig')->render(dirname(__FILE__) . '/src/Resources/views/admin/ecotax_taxincl.twig',[
                'form' => $form->createView(),
            ]);
        }
        return false;
    }

    public function hookActionProductSave($hookParams) {
        if(Configuration::get('PS_USE_ECOTAX')) {
            $productEcotaxRepo = $this->get('ecotax_product_repository');
            $productEcotaxRepo->setEcotaxByProductId((float)$_POST['product_ecotax']['ecotax_taxincl'],$hookParams['id_product']);
        }
    }

}
