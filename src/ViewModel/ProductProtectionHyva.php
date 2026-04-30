<?php

declare(strict_types=1);

namespace Extend\HyvaIntegration\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * ViewModel: exposes helper methods to the .phtml template.
 *
 */
class ProductProtectionHyva implements ArgumentInterface
{
    public function __construct(
	    private readonly Registry $registry,
	    private readonly CategoryRepositoryInterface $categoryRepository,
    ) {
    }


    /**
     * Returns the current product or null if unavailable.
     */
    public function getCurrentProduct(): ?ProductInterface
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Returns the product SKU for use in the Extend offer box.
     */
    public function getProductSku(): string
    {
        return (string) ($this->getCurrentProduct()?->getSku() ?? '');
    }

    /**
     * Returns the name of the product's first assigned category, or empty string.
     */
    public function getCategoryName(): string
    {

    	$product = $this->getCurrentProduct();
	if (!$product){
		return '';
	}
	$categoryIds = $product->getCategoryIds();
	if (empty($categoryIds)){
		return '';
	}
	
	try{
		return (string) $this->categoryRepository->get((int) reset($categoryIds))->getName();
	}catch (\Exception $e) {
		return '';
	}
    }
}

