<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Writer;

use Oro\Bundle\IntegrationBundle\Exception\TransportException;

use OroCRM\Bundle\MagentoBundle\Entity\Customer;
use OroCRM\Bundle\MagentoBundle\Service\CustomerStateHandler;

class CustomerExportWriter extends AbstractExportWriter
{
    const CUSTOMER_ID_KEY = 'customer_id';
    const FAULT_CODE_NOT_EXISTS = '102';
    const CONTEXT_CUSTOMER_POST_PROCESS = 'postProcessCustomer';

    /**
     * @var CustomerStateHandler
     */
    protected $stateHandler;

    /**
     * @param CustomerStateHandler $stateHandler
     * @return CustomerExportWriter
     */
    public function setStateHandler($stateHandler)
    {
        $this->stateHandler = $stateHandler;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        /** @var Customer $entity */
        $entity = $this->getEntity();
        if ($this->stateHandler->isCustomerRemoved($entity)) {
            return;
        }

        $item = reset($items);

        if (!$item) {
            $this->logger->error('Wrong Customer data', (array)$item);

            return;
        }

        $this->transport->init($this->getChannel()->getTransport());
        if (empty($item[self::CUSTOMER_ID_KEY])) {
            $this->writeNewItem($item);
        } else {
            $this->writeExistingItem($item);
        }

        // Clear temporary saved password
        $entity->setPassword(null);
        parent::write([$entity]);
    }

    /**
     * @param array $item
     */
    protected function writeNewItem(array $item)
    {
        /** @var Customer $entity */
        $entity = $this->getEntity();
        try {
            $customerId = $this->transport->createCustomer($item);
            $entity->setOriginId($customerId);
            $this->stateHandler->markCustomerSynced($entity);
            $this->stateHandler->markAddressesForSync($entity);

            $this->logger->info(
                sprintf('Customer with id %s successfully created with data %s', $customerId, json_encode($item))
            );
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->stepExecution->addFailureException($e);
        }
    }

    /**
     * @param array $item
     */
    protected function writeExistingItem(array $item)
    {
        /** @var Customer $entity */
        $entity = $this->getEntity();

        $customerId = $item[self::CUSTOMER_ID_KEY];

        try {
            $remoteData = $this->transport->getCustomerInfo($customerId);
            $item = $this->getStrategy()->merge(
                $this->getEntityChangeSet(),
                $item,
                $remoteData,
                $this->getTwoWaySyncStrategy()
            );

            $this->stepExecution->getJobExecution()
                ->getExecutionContext()
                ->put(self::CONTEXT_CUSTOMER_POST_PROCESS, [$item]);

            $result = $this->transport->updateCustomer($customerId, $item);

            if ($result) {
                $this->stateHandler->markCustomerSynced($entity);

                $this->logger->info(
                    sprintf('Customer with id %s successfully updated with data %s', $customerId, json_encode($item))
                );
            } else {
                $this->logger->error(sprintf('Customer with id %s was not updated', $customerId));
            }
        } catch (TransportException $e) {
            if ($e->getFaultCode() == self::FAULT_CODE_NOT_EXISTS) {
                $this->stateHandler->markCustomerRemoved($entity);
            }

            $this->logger->error($e->getMessage());
            $this->stepExecution->addFailureException($e);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->stepExecution->addFailureException($e);
        }
    }
}
