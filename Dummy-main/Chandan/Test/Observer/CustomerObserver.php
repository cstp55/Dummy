<?php
namespace Chandan\Test\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Chandan\Test\Model\SyncService;
use Magento\Customer\Api\Data\CustomerInterface;

class CustomerObserver implements ObserverInterface
{
    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * CustomerObserver constructor.
     * @param SyncService $syncService
     */
    public function __construct(SyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Triggered when a customer is saved.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var CustomerInterface $customer */
        $customer = $observer->getData('customer');
        
        try {
            // Sync customer data
            $this->syncService->syncCustomerData($customer);
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }
    
    /**
     * Log the exception.
     *
     * @param \Exception $exception
     * @return void
     */
    private function logException(\Exception $exception)
    {
        $this->syncService->getLogger()->error($exception->getMessage());
    }
}
