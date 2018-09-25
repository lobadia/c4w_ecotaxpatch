<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace EcotaxPatch\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product ecotax form.
 */
class ProductEcotax extends CommonAbstractType
{

    private $translator;
    private $configuration;
    private $eco_tax_rate;

    /**
     * Constructor.
     *
     * @param object $translator
     * @param object $taxDataProvider
     * @param object $legacyContext
     */
    public function __construct($translator, $taxDataProvider, $legacyContext)
    {
        $this->translator = $translator;
        $this->configuration = $this->getConfiguration();
        $this->legacyContext = $legacyContext;
        $this->eco_tax_rate = $taxDataProvider->getProductEcotaxRate();
        $this->currency = $legacyContext->getContext()->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
                'ecotax_taxincl',
                FormType\MoneyType::class,
                [
                    'required' => false,
                    'label' => $this->translator->trans('Ecotax (tax incl.)', [], 'Admin.Catalog.Feature'),
                    'currency' => $this->currency->iso_code,
                    'constraints' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'float']),
                    ],
                    'attr' => ['data-eco-tax-rate' => $this->eco_tax_rate],
                ]
            );
    }
}
