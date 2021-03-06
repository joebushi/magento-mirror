<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml coupons report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Coupons_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'updated_at_order')
            ? 'salesrule/report_updatedat_collection'
            : 'salesrule/report_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => Mage::helper('reports')->__('Period'),
            'index'             => 'period',
            'type'              => 'string',
            'width'             => 100,
            'sortable'          => false,
            'totals_label'      => Mage::helper('adminhtml')->__('Total'),
            'subtotals_label'   => Mage::helper('adminhtml')->__('SubTotal')
        ));

        $this->addColumn('coupon_code', array(
            'header'    => Mage::helper('reports')->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code'
        ));

        $this->addColumn('coupon_uses', array(
            'header'    => Mage::helper('reports')->__('Number of Use'),
            'sortable'  => false,
            'index'     => 'coupon_uses',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currency_code = $this->getCurrentCurrencyCode();

        $this->addColumn('subtotal_amount', array(
            'header'        => Mage::helper('reports')->__('Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'total'         => 'sum',
            'index'         => 'subtotal_amount'
        ));

        $this->addColumn('discount_amount', array(
            'header'        => Mage::helper('reports')->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'total'         => 'sum',
            'index'         => 'discount_amount'
        ));

        $this->addColumn('total_amount', array(
            'header'        => Mage::helper('reports')->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currency_code,
            'total'         => 'sum',
            'index'         => 'total_amount'
        ));

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', Mage::helper('reports')->__('Excel'));

        return parent::_prepareColumns();
    }
}
