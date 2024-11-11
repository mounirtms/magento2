<?php
/** 
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
 
class Assignpopup extends Column
{
    const RESC_ID = 'Magento_Backend::all';
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
	protected $_helperData;
    protected $_objectManager;
	protected $_aclRetriever;
 
    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \X247commerce\AssignOrderToUser\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        array $components = [],
        array $data = []
    ) {
		$this->_helperData = $helperData;
        $this->_urlBuilder = $urlBuilder;
        $this->_objectManager = $objectmanager;
		$this->_aclRetriever = $aclRetriever;
	
        $roleId = $authSession->getUser()->getRole()->getRoleId();
		$selectedResources = $this->_aclRetriever->getAllowedResourcesByRole($roleId);
			 
		if(self::RESC_ID != $selectedResources[0]){
           $data = [];
        }
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
        $result = $this->_helperData->getOrderManageAdmins("assignpopup");
        $key_form = $this->_objectManager->get('Magento\Framework\Data\Form\FormKey');
        $form_Key = $key_form->getFormKey(); // this will give you from key
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as $key => & $item) {
                $AssignedIds = explode(",",$item['assigned_user_id']);
                $item[$fieldName . '_html'] = "<button class='button'><span>Assign</span></button>";
                $item[$fieldName . '_title'] = __('Assign Order To');
                $item[$fieldName . '_submitlabel'] = __('Assign');
                $item[$fieldName . '_cancellabel'] = __('Reset');
                $item[$fieldName . '_customerid'] = $item['entity_id'];
                $item[$fieldName . '_formkey'] = $form_Key;
                $item[$fieldName . '_adminscount'] = $this->_helperData->getOrderManageAdmins("size");
                $item[$fieldName . '_admins'] = $this->_adminArray($result,$item['assigned_user_id']);
                $item[$fieldName . '_formaction'] = $this->_urlBuilder->getUrl('service/assign/index');
            }
        }
        return $dataSource;
    }

    /**
     * 
     * @param type $result
     * @param type $assigned_user_id
     * @return array
     */
    protected function _adminArray($result,$assigned_user_id) {
        foreach($result as $key => $_result) {
            if($_result['user_id'] == $assigned_user_id) {
                    $result[$key]['checked'] = 'checked';
            } else {
                    $result[$key]['checked'] = '';
            }
        }
       
        return $result;
    }
}