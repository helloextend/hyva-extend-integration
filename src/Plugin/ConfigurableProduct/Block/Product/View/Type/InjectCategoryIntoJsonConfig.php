<?php
namespace Extend\HyvaIntegration\Plugin\ConfigurableProduct\Block\Product\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class InjectCategoryIntoJsonConfig
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly LoggerInterface $logger
    ) {}

    public function afterGetJsonConfig(Configurable $subject, string $result): string
    {
        try {
            $config = json_decode($result, true);
            $storeId = $this->storeManager->getStore()->getId();

            $categoryMap = []; // keyed by child product ID

            foreach ($subject->getAllowProducts() as $product) {
                $categoryIds = $product->getCategoryIds();
                $categoryNames = [];

                foreach ($categoryIds as $categoryId) {
                    $category = $this->categoryRepository->get($categoryId, $storeId);
                    $categoryNames[] = $category->getName();
                }

                $categoryMap[$product->getId()] = [
                    'category_ids' => $categoryIds,
                    'categories'   => $categoryNames,
                    'category'     => !empty($categoryNames) ? $categoryNames[0] : null,
                ];
            }

            $config['categoryMap'] = $categoryMap;

            return json_encode($config);
        } catch (\Exception $e) {
            $this->logger->error('InjectCategoryIntoJsonConfig error: ' . $e->getMessage());
            return $result;
        }
    }
}
