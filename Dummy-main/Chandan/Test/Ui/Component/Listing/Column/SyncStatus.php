<?php

namespace Chandan\Test\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class SyncStatus extends Column
{
   /**
    * Dependencies Injection
    *
    * @param ContextInterface $context
    * @param UiComponentFactory $uiComponentFactory
    * @param array $components
    * @param array $data
    */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
            //    echo "<pre>";
            //     print_r($item); die;
                $item['sync_status'] == 1?'Yes':'No';
            }
        }
        return $dataSource;
    }
}
