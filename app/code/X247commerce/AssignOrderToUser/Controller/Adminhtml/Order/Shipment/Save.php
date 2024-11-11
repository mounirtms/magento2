<?php
/**
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Controller\Adminhtml\Order\Shipment;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Shipment\Validation\QuantityValidator;

class Save extends \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save {
	
	/**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @var \Magento\Shipping\Model\Shipping\LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface
     */
    private $shipmentValidator;
	
	protected $helperData;
	
	public function __construct(
        \Magento\Backend\App\Action\Context $context,
	\X247commerce\AssignOrderToUser\Helper\Data $helperData,
        \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader,
        \Magento\Shipping\Model\Shipping\LabelGenerator $labelGenerator,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender
    ) {
	$this->helperData = $helperData;
        $this->shipmentLoader = $shipmentLoader;
        $this->labelGenerator = $labelGenerator;
        $this->shipmentSender = $shipmentSender;
        parent::__construct($context,$shipmentLoader,$labelGenerator,$shipmentSender);
    }
	
	/**
     * Save shipment and order in one transaction
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    protected function _saveShipment($shipment) {
        $shipment->getOrder()->setIsInProcess(true);
        $transaction = $this->_objectManager->create(
            'Magento\Framework\DB\Transaction'
        );
        $transaction->addObject(
            $shipment
        )->addObject(
            $shipment->getOrder()
        )->save();
        return $this;
    }
	
	/**
     * Save shipment
     * We can save only new shipment. Existing shipments are not editable
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(){
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost){
            $this->messageManager->addError(__('We can\'t save the shipment right now.'));
            return $resultRedirect->setPath('sales/order/index');
        }
        $data = $this->getRequest()->getParam('shipment');
        if (!empty($data['comment_text'])){
            $this->_objectManager->get('Magento\Backend\Model\Session')->setCommentText($data['comment_text']);
        }
        $isNeedCreateLabel = isset($data['create_shipping_label']) && $data['create_shipping_label'];
        try{
            $this->shipmentLoader->setOrderId($this->getRequest()->getParam('order_id'));
            $this->shipmentLoader->setShipmentId($this->getRequest()->getParam('shipment_id'));
            $this->shipmentLoader->setShipment($data);
            $this->shipmentLoader->setTracking($this->getRequest()->getParam('tracking'));
            $shipment = $this->shipmentLoader->load();
            if (!$shipment){
                $this->_forward('noroute');
                return;
            }
            if (!empty($data['comment_text'])){
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                $shipment->setCustomerNote($data['comment_text']);
                $shipment->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }
            $validationResult = $this->getShipmentValidator()
                ->validate($shipment, [QuantityValidator::class]);
            if ($validationResult->hasMessages()){
                $this->messageManager->addError(
                    __("Shipment Document Validation Error(s):\n" . implode("\n", $validationResult->getMessages()))
                );
                $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
                return;
            }
            $shipment->register();
            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $responseAjax = new \Magento\Framework\DataObject();
            if ($isNeedCreateLabel){
                $this->labelGenerator->create($shipment, $this->_request);
                $responseAjax->setOk(true);
            }
            $this->_saveShipment($shipment);
            // update tha assigned user details
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($this->getRequest()->getParam('order_id'));
            $this->helperData->UpdateShipment($shipment->getEntityId(), $order);
            if (!empty($data['send_email'])){
                $this->shipmentSender->send($shipment);
            }
            $shipmentCreatedMessage = __('The shipment has been created.');
            $labelCreatedMessage = __('You created the shipping label.');
            $this->messageManager->addSuccess(
                $isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage : $shipmentCreatedMessage
            );
            $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($isNeedCreateLabel){
                $responseAjax->setError(true);
                $responseAjax->setMessage($e->getMessage());
            } else{
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
        } catch (\Exception $e){
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            if ($isNeedCreateLabel){
                $responseAjax->setError(true);
                $responseAjax->setMessage(__('An error occurred while creating shipping label.'));
            } else {
                $this->messageManager->addError(__('Cannot save shipment.'));
                $this->_redirect('*/*/new', ['order_id' => $this->getRequest()->getParam('order_id')]);
            }
        }
        if ($isNeedCreateLabel) {
            $this->getResponse()->representJson($responseAjax->toJson());
        } else {
            $this->_redirect('sales/order/view', ['order_id' => $shipment->getOrderId()]);
        }
    }
	
	/**
     * @return \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface
     * @deprecated
     */
    private function getShipmentValidator(){
        if ($this->shipmentValidator === null) {
            $this->shipmentValidator = $this->_objectManager->get(
                \Magento\Sales\Model\Order\Shipment\ShipmentValidatorInterface::class
            );
        }
        return $this->shipmentValidator;
    }
}