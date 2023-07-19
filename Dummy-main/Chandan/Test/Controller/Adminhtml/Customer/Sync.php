<?php
namespace Chandan\Test\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Chandan\Test\Model\SyncService;

class Sync extends Action
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SyncService
     */
    private $syncService;

    /**
     * Sync constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param SyncService $syncService
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        SyncService $syncService
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->syncService = $syncService;
    }

    /**
     * Execute the sync action.
     */
    public function execute()
    {
        $customerIds = $this->getRequest()->getParam('customer');

        if (!is_array($customerIds)) {
            $this->messageManager->addError(__('Please select customer(s).'));
        } else {
            try {
                $customers = $this->collectionFactory->create()
                    ->addFieldToFilter('entity_id', ['in' => $customerIds]);

                foreach ($customers as $customer) {
                    // Sync customer data
                    $this->syncService->syncCustomerData($customer);
                }

                $this->messageManager->addSuccess(
                    __('Synced %1 customer(s).', count($customerIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('customer/index/index');
    }
}
