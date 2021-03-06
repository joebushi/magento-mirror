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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for Paypal API wrappers
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Paypal_Model_Api_Abstract extends Varien_Object
{
    /**
     * Config instance
     * @var Mage_Paypal_Model_Config
     */
    protected $_config = null;

    /**
     * Global private to public interface map
     * @var array
     */
    protected $_globalMap = array();

    /**
     * Filter callbacks for importing/exporting amount
     * @var array
     */
    protected $_exportToRequestFilters = array();

    /**
     * Line items export to request mapping settings
     * @var array
     */
    protected $_lineItemExportTotals = array();
    protected $_lineItemExportItemsFormat = array();

    /**
     * Return Paypal Api user name based on config data
     *
     * @return string
     */
    public function getApiUsername()
    {
        return $this->_config->apiUsername;
    }

    /**
     * Return Paypal Api password based on config data
     *
     * @return string
     */
    public function getApiPassword()
    {
        return $this->_config->apiPassword;
    }

    /**
     * Return Paypal Api signature based on config data
     *
     * @return string
     */
    public function getApiSignature()
    {
        return $this->_config->apiSignature;
    }

    /**
     * BN code getter
     *
     * @return string
     */
    public function getBuildNotationCode()
    {
        return $this->_config->getBuildNotationCode();
    }

    /**
     * Return Paypal Api proxy status based on config data
     *
     * @return bool
     */
    public function getUseProxy()
    {
        return $this->_getDataOrConfig('use_proxy', false);
    }

    /**
     * Return Paypal Api proxy host based on config data
     *
     * @return string
     */
    public function getProxyHost()
    {
        return $this->_getDataOrConfig('proxy_host', '127.0.0.1');
    }

    /**
     * Return Paypal Api proxy port based on config data
     *
     * @return string
     */
    public function getProxyPort()
    {
        return $this->_getDataOrConfig('proxy_port', '808');
    }

    /**
     * Return Paypal Api debug flag based on config data
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->_config->debugFlag;
    }

    /**
     * PayPal page CSS getter
     *
     * @return string
     */
    public function getPageStyle()
    {
        return $this->_getDataOrConfig('page_style');
    }

    /**
     * Logo URL getter
     *
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->_getDataOrConfig('logo_url');
    }

    /**
     * PayPal page header image URL getter
     *
     * @return string
     */
    public function getHdrimg()
    {
        return $this->_getDataOrConfig('paypal_hdrimg');
    }

    /**
     * PayPal page header border color getter
     *
     * @return string
     */
    public function getHdrbordercolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbordercolor');
    }

    /**
     * PayPal page header background color getter
     *
     * @return string
     */
    public function getHdrbackcolor()
    {
        return $this->_getDataOrConfig('paypal_hdrbackcolor');
    }

    /**
     * PayPal page "payflow color" (?) getter
     *
     * @return string
     */
    public function getPayflowcolor()
    {
        return $this->_getDataOrConfig('paypal_payflowcolor');
    }

    /**
     * Payment action getter
     *
     * @return string
     */
    public function getPaymentAction()
    {
        return $this->_getDataOrConfig('payment_action');
    }

    /**
     * Import $this public data to specified object or array
     *
     * @param array|Varien_Object $to
     * @param array $publicMap
     * @return array|Varien_Object
     */
    public function &import($to, array $publicMap = array())
    {
        return Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $to, $publicMap);
    }

    /**
     * Export $this public data from specified object or array
     *
     * @param array|Varien_Object $from
     * @param array $publicMap
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function export($from, array $publicMap = array())
    {
        Varien_Object_Mapper::accumulateByMap($from, array($this, 'setDataUsingMethod'), $publicMap);
        return $this;
    }

    /**
     * Config instance setter
     * @param Mage_Paypal_Model_Config $config
     * @return Mage_Paypal_Model_Api_Abstract
     */
    public function setConfigObject(Mage_Paypal_Model_Config $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Current locale code getter
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    /**
     * Always take into accoun
     */
    public function getFraudManagementFiltersEnabled()
    {
        return 1;
    }

    /**
     * Whether specified payment status indicates that money were paid
     *
     * @param string $paymentStatus
     * @return bool
     */
    public function isPaid($paymentStatus)
    {
        return $paymentStatus === 'Completed';
    }

    /**
     * Export $this public data to private request array
     *
     * @param array $internalRequestMap
     * @param array $request
     * @return array
     */
    protected function &_exportToRequest(array $privateRequestMap, array $request = array())
    {
        $map = array();
        foreach ($privateRequestMap as $key) {
            $map[$this->_globalMap[$key]] = $key;
        }
        $result = Varien_Object_Mapper::accumulateByMap(array($this, 'getDataUsingMethod'), $request, $map);
        foreach ($privateRequestMap as $key) {
            if (isset($this->_exportToRequestFilters[$key]) && isset($result[$key])) {
                $callback   = $this->_exportToRequestFilters[$key];
                $privateKey = $result[$key];
                $publicKey  = $map[$this->_globalMap[$key]];
                $result[$key] = call_user_func(array($this, $callback), $privateKey, $publicKey);
            }
        }
        return $result;
    }

    /**
     * Import $this public data from a private response array
     *
     * @param array $privateResponseMap
     * @param array $response
     */
    protected function _importFromResponse(array $privateResponseMap, array $response)
    {
        $map = array();
        foreach ($privateResponseMap as $key) {
            $map[$key] = $this->_globalMap[$key];
        }
        Varien_Object_Mapper::accumulateByMap($response, array($this, 'setDataUsingMethod'), $map);
    }

    /**
     * Prepare line items request
     *
     * @param array &$request
     * @param int $i
     */
    protected function _exportLineItems(array &$request, $i = 0)
    {
        $items = $this->getLineItems();
        if (empty($items)) {
            return;
        }
        // line items
        foreach ($items as $item) {
            foreach ($this->_lineItemExportItemsFormat as $publicKey => $privateFormat) {
                $value = $item->getDataUsingMethod($publicKey);
                if (is_float($value)) {
                    $value = $this->_filterAmount($value);
                }
                $request[sprintf($privateFormat, $i)] = $value;
            }
            $i++;
        }
        // line item totals
        $lineItemTotals = $this->getLineItemTotals();
        if ($lineItemTotals) {
            $request = Varien_Object_Mapper::accumulateByMap($lineItemTotals, $request, $this->_lineItemExportTotals);
            foreach ($this->_lineItemExportTotals as $privateKey) {
                if (isset($request[$privateKey])) {
                    $request[$privateKey] = $this->_filterAmount($request[$privateKey]);
                } else {
                    Mage::logException(new Exception(sprintf('Missing index "%s" for line item totals.', $privateKey)));
                    Mage::throwException(Mage::helper('paypal')->__('Unable to calculate cart line item totals.'));
                }
            }
        }
    }

    /**
     * Filter amounts in API calls
     * @param float|string $value
     * @return string
     */
    protected function _filterAmount($value)
    {
        return sprintf('%.2F', $value);
    }

    /**
     * Unified getter that looks in data or falls back to config
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getDataOrConfig($key, $default = null)
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        return $this->_config->$key ? $this->_config->$key : $default;
    }


    /**
     * region_id workaround: PayPal requires state code, try to find one in the address
     *
     * @param Varien_Object $address
     * @return string
     */
    protected function _lookupRegionCodeFromAddress(Varien_Object $address)
    {
        if ($regionId = $address->getData('region_id')) {
            $region = Mage::getModel('directory/region')->load($regionId);
            if ($region->getId()) {
                return $region->getCode();
            }
        }
        return '';
    }

    /**
     * Street address workaround: divides address lines into parts by specified keys
     * (keys should go as 3rd, 4th[...] parameters)
     *
     * @param Varien_Object $address
     * @param array $request
     */
    protected function _importStreetFromAddress(Varien_Object $address, array &$to)
    {
        $keys = func_get_args(); array_shift($keys); array_shift($keys);
        $street = $address->getStreet();
        if (!$keys || !$street || !is_array($street)) {
            return;
        }
        foreach ($keys as $key) {
            if ($value = array_pop($street)) {
                $to[$key] = $value;
            }
        }
    }
}
