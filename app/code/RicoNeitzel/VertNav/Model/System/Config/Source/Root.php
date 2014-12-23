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

namespace RicoNeitzel\VertNav\Model\System\Config\Source;

class Root extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category
     */
    protected $_categoryResource;

    protected $_options;

    /**
     * @param \Magento\Catalog\Model\Resource\Category $categoryResource
     */
    public function __construct(\Magento\Catalog\Model\Resource\Category $categoryResource)
    {
        $this->_categoryResource = $categoryResource;
    }

    public function toOptionArray()
    {
        if (!isset($this->_options)) {
            $options = array(
                array(
                    'label' => __('Store base'),
                    'value' => 'root',
                ),
                array(
                    'label' => __('Current category children'),
                    'value' => 'current',
                ),
                array(
                    'label' => __('Same level as current category'),
                    'value' => 'siblings',
                ),
            );
            $resource = $this->_categoryResource;
            $select = $resource->getReadConnection()->select()->reset()
                ->from($resource->getTable('catalog_category_entity'), new \Zend_Db_Expr('MAX(`level`)'));
            $maxDepth = $resource->getReadConnection()->fetchOne($select);
            for ($i = 2; $i < $maxDepth; $i++) {
                $options[] = array(
                    'label' => __('Category Level %1', $i),
                    'value' => $i,
                );
            }
            $this->_options = $options;
        }
        return $this->_options;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}