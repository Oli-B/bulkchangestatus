<?php

class AdminOrdersController extends AdminOrdersControllerCore{

public function __construct()
	{
		$blub = 'NEIN';
		$this->table = 'order';
		$this->className = 'Order';
		$this->lang = false;
		$this->addRowAction('view');
		$this->explicitSelect = true;
		$this->allow_export = true;
		$this->deleted = false;
		$this->context = Context::getContext();
		$this->bulk_actions = array(
		'first' => array('text' => Configuration::get('BULKCHANGESTATUS_FIRSTTEXT'), 'confirm' => Configuration::get('BULKCHANGESTATUS_FIRSTCONFIRM')),
		'second' => array('text' => Configuration::get('BULKCHANGESTATUS_SECONDTEXT'), 'confirm' => Configuration::get('BULKCHANGESTATUS_SECONDCONFIRM')),
		'third' => array('text' => Configuration::get('BULKCHANGESTATUS_THIRDTEXT'), 'confirm' => Configuration::get('BULKCHANGESTATUS_THIRDCONFIRM')),
		'fourth' => array('text' => Configuration::get('BULKCHANGESTATUS_FOURTHTEXT'), 'confirm' => Configuration::get('BULKCHANGESTATUS_FOURTHCONFIRM')),
		'fifth' => array('text' => Configuration::get('BULKCHANGESTATUS_FIFTHTEXT'), 'confirm' => Configuration::get('BULKCHANGESTATUS_FIFTHCONFIRM'))
		);


		$this->_select = '
		a.id_currency,
		a.id_order AS id_pdf,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`,
		IF((SELECT COUNT(so.id_order) FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new';

		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
		$this->_orderBy = 'id_order';
		$this->_orderWay = 'DESC';

		$statuses_array = array();
		$statuses = OrderState::getOrderStates((int)$this->context->language->id);

		foreach ($statuses as $status)
			$statuses_array[$status['id_order_state']] = $status['name'];

		$this->fields_list = array(
		'id_order' => array(
			'title' => $this->l('ID'),
			'align' => 'center',
			'width' => 25
		),
		'reference' => array(
			'title' => $this->l('Reference'),
			'align' => 'center',
			'width' => 65
		),
		'new' => array(
			'title' => $this->l('New'),
			'width' => 25,
			'align' => 'center',
			'type' => 'bool',
			'tmpTableFilter' => true,
			'icon' => array(
				0 => 'blank.gif',
				1 => array(
					'src' => 'note.png',
					'alt' => $this->l('First customer order'),
				)
			),
			'orderby' => false
		),
		'customer' => array(
			'title' => $this->l('Customer'),
			'havingFilter' => true,
		),
		'total_paid_tax_incl' => array(
			'title' => $this->l('Total'),
			'width' => 70,
			'align' => 'right',
			'prefix' => '<b>',
			'suffix' => '</b>',
			'type' => 'price',
			'currency' => true
		),
		'payment' => array(
			'title' => $this->l('Payment: '),
			'width' => 100
		),
		'osname' => array(
			'title' => $this->l('Status'),
			'color' => 'color',
			'width' => 280,
			'type' => 'select',
			'list' => $statuses_array,
			'filter_key' => 'os!id_order_state',
			'filter_type' => 'int',
			'order_key' => 'osname'
		),
		'date_add' => array(
			'title' => $this->l('Date'),
			'width' => 130,
			'align' => 'right',
			'type' => 'datetime',
			'filter_key' => 'a!date_add'
		),
		'id_pdf' => array(
			'title' => $this->l('PDF'),
			'width' => 35,
			'align' => 'center',
			'callback' => 'printPDFIcons',
			'orderby' => false,
			'search' => false,
			'remove_onclick' => true)
		);

		$this->shopLinkType = 'shop';
		$this->shopShareDatas = Shop::SHARE_ORDER;

		if (Tools::isSubmit('id_order'))
		{
			// Save context (in order to apply cart rule)
			$order = new Order((int)Tools::getValue('id_order'));
			if (!Validate::isLoadedObject($order))
				throw new PrestaShopException('Cannot load Order object');
			$this->context->cart = new Cart($order->id_cart);
			$this->context->customer = new Customer($order->id_customer);
		}

		parent::__construct();
	}
	
public function processBulkMain($order_state){ 
	
	// If id_order is sent, we instanciate a new Order object
		if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0)
		{

			if (!Validate::isLoadedObject($order))
				throw new PrestaShopException('Can\'t load Order object');
			ShopUrl::cacheMainDomainForShop((int)$order->id_shop);
		}
	
	if (is_array($this->boxes) && !empty($this->boxes)){
				
		$employee_id = $this->context->employee->id;

		foreach ($this->boxes as $id){
		
		if ($this->tabAccess['edit'] === '1')
			{
				$order = new $this->className($id);

					$current_order_state = $order->getCurrentOrderState();
					if ($current_order_state->id != $order_state)
					{
						// Create new OrderHistory

						$history = new OrderHistory();
						$history->id_order = (int)$id;
						$history->id_employee = (int)$this->context->employee->id;

						$use_existings_payment = false;
						if (!$order->hasInvoice())
							$use_existings_payment = true;
						$history->changeIdOrderState((int)$order_state, $order, $use_existings_payment);

						$carrier = new Carrier($order->id_carrier, $order->id_lang);
						$templateVars = array();
						if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
							$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
						// Save all changes
						if ($history->addWithemail(true, $templateVars))
						{
							// synchronizes quantities if needed..
							if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
							{
								foreach ($order->getProducts() as $product)
								{
									if (StockAvailable::dependsOnStock($product['product_id']))
										StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
								}
							}
							
						}
						else
						$this->errors[] = Tools::displayError('An error occurred while changing order status, or we were unable to send an email to the customer.');
					}
					else
						$this->errors[] = Tools::displayError('The order has already been assigned this status.');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
		
		}
	}
	else
		$this->errors[] = Tools::displayError('No orders selected.');
}

	public function processBulkFirst(){
	$this->processBulkMain(Configuration::get('BULKCHANGESTATUS_FIRST'));	
	}	
	public function processBulkSecond(){
	$this->processBulkMain(Configuration::get('BULKCHANGESTATUS_SECOND'));	
	}
	public function processBulkThird(){
	$this->processBulkMain(Configuration::get('BULKCHANGESTATUS_THIRD'));	
	}
	public function processBulkFourth(){
	$this->processBulkMain(Configuration::get('BULKCHANGESTATUS_FOURTH'));	
	}
	public function processBulkFifth(){
	$this->processBulkMain(Configuration::get('BULKCHANGESTATUS_FIFTH'));	
	}
}

?>