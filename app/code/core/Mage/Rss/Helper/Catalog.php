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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Default rss helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Helper_Catalog extends Mage_Core_Helper_Abstract
{

    public function getTagFeedUrl()
    {
        $url = '';
        if(Mage::getStoreConfig('rss/catalog/tag') && $this->_getRequest()->getParam('tagId')){
            $tagModel = Mage::getModel('tag/tag')->load($this->_getRequest()->getParam('tagId'));
            if($tagModel && $tagModel->getId()){
                return Mage::getUrl('rss/catalog/tag', array('tagName' => $tagModel->getName()));
            }
        }
        return $url;
    }

}