<?php
namespace Chandan\Test\Model;

use Chandan\Test\Helper\Data;
use Chandan\Test\Logger\TestLogger;

class SyncService
{
    /**
     * @var Data
     */
    protected $helper;

    /** @var \Magento\Customer\Model\Customer */
    protected $customer;

    /** @var \Magento\Customer\Model\ResourceModel\CustomerFactory */
    protected $customerFactory;

    /** @var Chandan\Test\Logger\TestLogger */
    protected $logger;

    /**
     * SyncService constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        TestLogger $logger,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerFactory
    ) {
        $this->logger = $logger;
        $this->customerFactory = $customerFactory;
        $this->customer = $customer;
        $this->helper = $helper;
    }

    /**
     * Sync customer data with the third-party system.
     *
     * @param \Magento\Customer\Model\Customer $customer
     */
    public function syncCustomerData($customer)
    {
        // Check if the module is enabled
        if (!$this->helper->isModuleEnabled()) {
            return;
        }
        //Code to send the customer data to the third-party system
        try {
            // Get the API endpoint
            $apiEndpoint = $this->helper->getApiEndpoint();

            // Prepare the customer data
            $customerData = [
                'customer_id' => $customer->getId(),
                'email' => $customer->getEmail(),
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                // Add more customer data as needed
            ];

            // Create a new cURL resource
            $ch = curl_init();

            // Set the cURL options
            curl_setopt($ch, CURLOPT_URL, $apiEndpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($customerData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($customerData))
            ]);

            // Execute the cURL request
            $response = curl_exec($ch);

            // Check for errors
            if ($response === false) {
                $error = curl_error($ch);
                // Handle the error as needed
            }

            // Close the cURL resource
            curl_close($ch);

            // Process the response and save sync_status true
            $this->saveCustomerSyncStatus($customer->getId());

            $this->logger->info('Customer data synced successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error syncing customer data: ' . $e->getMessage());
        }
    }

    /**
     * Save Customer Sync Status
     *
     * @param [type] $customerId
     * @return void
     */
    public function saveCustomerSyncStatus($customerId)
    {
        $customer = $this->customer->load($customerId);
        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('sync_status', 1);
        $customer->updateData($customerData);
        $customerResource = $this->customerFactory->create();
        $customerResource->saveAttribute($customer, 'sync_status');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
