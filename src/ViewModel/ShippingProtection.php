<?php
/*
 * Copyright Extend (c) 2023. All rights reserved.
 * See Extend-COPYING.txt for license details.
 */

namespace Extend\HyvaIntegration\ViewModel;

use Extend\Integration\Api\StoreIntegrationRepositoryInterface;
use Extend\Integration\Service\Extend;
use Extend\Integration\Service\Api\ActiveEnvironmentURLBuilder;
use Extend\Integration\Service\Api\Integration;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Request\Http;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\CompositeConfigProvider;


class ShippingProtection implements
    \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public const ENABLE_SHIPPING_PROTECTION = 'extend_plans/shipping_protection/enable';


    /** @var StoreIntegrationRepositoryInterface */
    private $storeIntegrationRepository;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var ActiveEnvironmentURLBuilder */
    private $activeEnvironmentURLBuilder;

    /** @var LoggerInterface */
    private $logger;

    /** @var Http */
    private $request;

    //JM
    /** @var CartInterface */
    private $cart;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var QuoteFactory */
    private $quote;

    /**  @var CompositeConfigProvider */
    protected $configProvider;




    /**
     * ShippingProtection constructor
     *
     * @param StoreIntegrationRepositoryInterface $storeIntegrationRepository
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ActiveEnvironmentURLBuilder $activeEnvironmentURLBuilder
     * @param LoggerInterface $logger
     * @param Http $request
     * //JM
     * @param CartInterface $cart
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteFactory $quote
     * @param CompositeConfigProvider $configProvider
     *
     */
    public function __construct(
        StoreIntegrationRepositoryInterface $storeIntegrationRepository,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ActiveEnvironmentURLBuilder $activeEnvironmentURLBuilder,
        LoggerInterface $logger,
        Http $request,
        //JM
        CartInterface $cart,
        CartRepositoryInterface $cartRepository,
        QuoteFactory $quote,
        CompositeConfigProvider $configProvider

    ) {
        $this->storeIntegrationRepository = $storeIntegrationRepository;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->activeEnvironmentURLBuilder = $activeEnvironmentURLBuilder;
        $this->logger = $logger;
        $this->request = $request;
        //JM
        $this->cart = $cart;
        $this->cartRepository = $cartRepository;
        $this->quote = $quote;
        $this->configProvider = $configProvider;
    }


    /**
     * Determine if Extend Shipping Protection is currently enabled
     *
     * @return bool
     */
    public function isExtendShippingProtectionEnabled(): bool
    {
        return $this->getScopedConfigValue(Extend::ENABLE_SHIPPING_PROTECTION) === '1';
    }


    /**
     * Get Scoped Config Value
     *
     * @param string $configPath
     * @return string
     */
    private function getScopedConfigValue(string $configPath): string
    {
        $scopeCode = $this->storeManager->getStore()->getCode();
        $scopeType = ScopeInterface::SCOPE_STORES;
        return $this->scopeConfig->getValue($configPath, $scopeType, $scopeCode) ?: '';
    }


    /**
     * Get quote grand total
     *
     * @param int $quoteId
     * @return float|null
     */
    public function getTotals($quoteId)
    {
        $quote = $this->getQuoteById($quoteId);
        return $quote ? $quote->getGrandTotal() : null;
    }


    public function getCheckoutConfig(): array
    {
        return $this->configProvider->getConfig();
    }


}
