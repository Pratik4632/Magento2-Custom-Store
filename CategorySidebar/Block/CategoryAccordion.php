<?php

namespace Conceptive\CategorySidebar\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category;

class CategoryAccordion extends \Magento\Framework\View\Element\Template
{
    protected $categoryCollectionFactory;

    public function __construct(
        Context $context,
        CategoryCollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }


    public function getCategoryCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addIsActiveFilter();
        $collection->addOrderField('path');
        $collection->addLevelFilter(2); 
        $collection->addFieldToFilter('entity_id', ['neq' => 2]);
        return $collection;
    }

    public function getCategoryTreeHtml($categories, $level = 0)
    {
        $html = '<ul class="level-' . $level . '">';
        /** @var Category $category */
        foreach ($categories as $category) {

            $liClass = $category->hasChildren() ? 'parent-category' : 'child-category';

            $html .= '<li class="' . $liClass . '">';
            $html .= '<a href="' . $category->getUrl() . '">' . $category->getName() . '</a>';

            if ($category->hasChildren()) {
                $html .= '<span class="touch-button"></span>';
                $subcategories = $category->getChildrenCategories();
                if ($subcategories->getSize()) {
                    $html .= $this->getCategoryTreeHtml($subcategories, $level + 1);
                }
            }
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
