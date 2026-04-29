<?php
namespace Extend\HyvaIntegration\Plugin\Checkout\CustomerData;

use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class AddCategoryToCartItem
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {}

    public function afterGetItemData(AbstractItem $subject, array $result, Item $item): array
    {
        try {
            $product = $item->getProduct();
            $categoryIds = $product->getCategoryIds();
            $storeId = $this->storeManager->getStore()->getId();

            $categoryNames = [];
            foreach ($categoryIds as $categoryId) {
                $category = $this->categoryRepository->get($categoryId, $storeId);
                $categoryNames[] = $category->getName();
            }

            $result['category_ids']   = $categoryIds;
            $result['categories']     = $categoryNames;
            // Convenience: first category name, useful for single-category lookups
            $result['category']       = !empty($categoryNames) ? $categoryNames[0] : null;
        } catch (\Exception $e) {
            $this->logger->error('CartCategory plugin error: ' . $e->getMessage());
            $result['category_ids'] = [];
            $result['categories']   = [];
            $result['category']     = null;
        }

        return $result;
    }
} 
