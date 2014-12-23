<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0).
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   RicoNeitzel
 * @package    RicoNeitzel_VertNav
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com/
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace RicoNeitzel\VertNav\Block\Plugin;

/**
 * Plugin that provides compatibility with Netzarbeiter_LoginCatalog extension
 */
class LoginCatalog
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\App\ObjectManager $objectManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\ObjectManager $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_objectManager = $objectManager;
        $this->_moduleManager = $moduleManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * Suppress rendering according to Netzarbeiter_LoginCatalog's business logic
     *
     * @param \RicoNeitzel\VertNav\Block\Navigation $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Category $category
     * @param integer $level
     * @param array|null $levelClass
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDrawOpenCategoryItem(
        \RicoNeitzel\VertNav\Block\Navigation $subject,
        \Closure $proceed,
        $category,
        $level = 0,
        array $levelClass = null
    ) {
        if ($this->_isLoginCatalogInstalledAndActive() && $this->_loginCatalogHideCategories()) {
            return '';
        }
        return $proceed($category, $level, $levelClass);
    }

    /**
     * Check if the Netzarbeiter_LoginCatalog extension is installed and active
     * @return boolean
     */
    protected function _isLoginCatalogInstalledAndActive()
    {
        return $this->_moduleManager->isEnabled('Netzarbeiter\LoginCatalog');
    }

    /**
     * Check if the Netzarbeiter_LoginCatalog extension is configured to hide categories from logged out customers
     * @return boolean
     */
    protected function _loginCatalogHideCategories()
    {
        /** @var \Netzarbeiter\LoginCatalog\Helper\Data $loginCatalog */
        $loginCatalog = $this->_objectManager->get('Netzarbeiter\LoginCatalog\Helper\Data');
        return !$this->_customerSession->isLoggedIn()
            && $loginCatalog->moduleActive()
            && $loginCatalog->getConfig('hide_categories');
    }
}
