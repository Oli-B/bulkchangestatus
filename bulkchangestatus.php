<?php
if (!defined('_PS_VERSION_'))
  exit;
  
  class BulkChangeStatus extends Module
{
	public function __construct()
	{
		$this->name = 'bulkchangestatus';
		$this->tab = 'administration';
		$this->version = '0.1';
		$this->author = 'Oli B';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
		
	 
		parent::__construct();
	 
		$this->displayName = $this->l('Bulk change order status');
		$this->description = $this->l('Description');
	 
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
	 
		if (!Configuration::get('BULKCHANGESTATUS_FIRST') && !Configuration::get('BULKCHANGESTATUS_FIRSTTEXT') && !Configuration::get('BULKCHANGESTATUS_FIRSTCONFIRM') && !Configuration::get('BULKCHANGESTATUS_SECOND'))      
		  $this->warning = $this->l('No name provided');
	}  
	 public function install()
		{
			  if (Shop::isFeatureActive())
				Shop::setContext(Shop::CONTEXT_ALL);
			
			Configuration::updateValue('BULKCHANGESTATUS_FIRST', 4);
			Configuration::updateValue('BULKCHANGESTATUS_SECOND', 5);
			Configuration::updateValue('BULKCHANGESTATUS_THIRD', 2);
			Configuration::updateValue('BULKCHANGESTATUS_FOURTH', 9);
			Configuration::updateValue('BULKCHANGESTATUS_FIFTH', 6);
			Configuration::updateValue('BULKCHANGESTATUS_FIRSTTEXT', 'Mark as sended');
			Configuration::updateValue('BULKCHANGESTATUS_SECONDTEXT', 'Mark as delivered');
			Configuration::updateValue('BULKCHANGESTATUS_THIRDTEXT', 'Mark as payment accepted');
			Configuration::updateValue('BULKCHANGESTATUS_FOURTHTEXT', 'Mark as on backorder');
			Configuration::updateValue('BULKCHANGESTATUS_FIFTHTEXT', 'Mark as canceled');
			Configuration::updateValue('BULKCHANGESTATUS_FIRSTCONFIRM', 'Selected Orders will be marked as sended');
			Configuration::updateValue('BULKCHANGESTATUS_SECONDCONFIRM', 'Selected Orders will be marked as delivered');
			Configuration::updateValue('BULKCHANGESTATUS_THIRDCONFIRM', 'Selected Orders will be marked as payment accepted');
			Configuration::updateValue('BULKCHANGESTATUS_FOURTHCONFIRM', 'Selected Orders will be marked as backorder');
			Configuration::updateValue('BULKCHANGESTATUS_FIFTHCONFIRM', 'Selected Orders will be marked as canceled'); 
			return parent::install();	
			
		}
	public function uninstall()
		{
			Configuration::deleteByName('BULKCHANGESTATUS_FIRST');
			Configuration::deleteByName('BULKCHANGESTATUS_SECOND');
			Configuration::deleteByName('BULKCHANGESTATUS_THIRD');
			Configuration::deleteByName('BULKCHANGESTATUS_FOURTH');
			Configuration::deleteByName('BULKCHANGESTATUS_FIFTH');
			Configuration::deleteByName('BULKCHANGESTATUS_FIRSTTEXT');
			Configuration::deleteByName('BULKCHANGESTATUS_SECONDTEXT');
			Configuration::deleteByName('BULKCHANGESTATUS_THIRDTEXT');
			Configuration::deleteByName('BULKCHANGESTATUS_FOURTHTEXT');
			Configuration::deleteByName('BULKCHANGESTATUS_FIFTHTEXT');
			Configuration::deleteByName('BULKCHANGESTATUS_FIRSTCONFIRM');
			Configuration::deleteByName('BULKCHANGESTATUS_SECONDCONFIRM');
			Configuration::deleteByName('BULKCHANGESTATUS_THIRDCONFIRM');
			Configuration::deleteByName('BULKCHANGESTATUS_FOURTHCONFIRM');
			Configuration::deleteByName('BULKCHANGESTATUS_FIFTHCONFIRM'); 
			return parent::uninstall();
			
		}
	public function getContent()
		{
			$output = null;
		 
			if (Tools::isSubmit('submit'.$this->name))
			{
				$bulkfirst = (int)(Tools::getValue('BULKCHANGESTATUS_FIRST'));
				$bulksecond = (int)(Tools::getValue('BULKCHANGESTATUS_SECOND'));
				$bulkthird = (int)(Tools::getValue('BULKCHANGESTATUS_THIRD'));
				$bulkfourth = (int)(Tools::getValue('BULKCHANGESTATUS_FOURTH'));
				$bulkfifth = (int)(Tools::getValue('BULKCHANGESTATUS_FIFTH'));
				$bulkfirsttext = strval(Tools::getValue('BULKCHANGESTATUS_FIRSTTEXT'));
				$bulksecondtext = strval(Tools::getValue('BULKCHANGESTATUS_SECONDTEXT'));
				$bulkthirdtext = strval(Tools::getValue('BULKCHANGESTATUS_THIRDTEXT'));
				$bulkfourthtext = strval(Tools::getValue('BULKCHANGESTATUS_FOURTHTEXT'));
				$bulkfifthtext = strval(Tools::getValue('BULKCHANGESTATUS_FIFTHTEXT'));
				$bulkfirstconfirm = strval(Tools::getValue('BULKCHANGESTATUS_FIRSTCONFIRM'));
				$bulksecondconfirm = strval(Tools::getValue('BULKCHANGESTATUS_SECONDCONFIRM'));
				$bulkthirdconfirm = strval(Tools::getValue('BULKCHANGESTATUS_THIRDCONFIRM'));
				$bulkfourthconfirm = strval(Tools::getValue('BULKCHANGESTATUS_FOURTHCONFIRM'));
				$bulkfifthconfirm = strval(Tools::getValue('BULKCHANGESTATUS_FIFTHCONFIRM'));
				if (!$bulkfirst  || empty($bulkfirst) || !Validate::isUnsignedInt($bulkfirst) ||
					!$bulksecond  || empty($bulksecond) || !Validate::isUnsignedInt($bulksecond) ||
					!$bulkthird  || empty($bulkthird) || !Validate::isUnsignedInt($bulkthird) ||
					!$bulkfourth  || empty($bulkfourth) || !Validate::isUnsignedInt($bulkfourth) ||
					!$bulkfifth  || empty($bulkfifth) || !Validate::isUnsignedInt($bulkfifth) ||
					!$bulkfirsttext  || empty($bulkfirsttext) || !Validate::isGenericName($bulkfirsttext) ||
					!$bulksecondtext  || empty($bulksecondtext) || !Validate::isGenericName($bulksecondtext) ||
					!$bulkthirdtext  || empty($bulkthirdtext) || !Validate::isGenericName($bulkthirdtext) ||
					!$bulkfourthtext  || empty($bulkfourthtext) || !Validate::isGenericName($bulkfourthtext) ||
					!$bulkfifthtext  || empty($bulkfifthtext) || !Validate::isGenericName($bulkfifthtext) ||
					!$bulkfirstconfirm  || empty($bulkfirstconfirm) || !Validate::isGenericName($bulkfirstconfirm) ||
					!$bulksecondconfirm  || empty($bulksecondconfirm) || !Validate::isGenericName($bulksecondconfirm) ||
					!$bulkthirdconfirm  || empty($bulkthirdconfirm) || !Validate::isGenericName($bulkthirdconfirm) ||
					!$bulkfourthconfirm  || empty($bulkfourthconfirm) || !Validate::isGenericName($bulkfourthconfirm) ||
					!$bulkfifthconfirm  || empty($bulkfifthconfirm) || !Validate::isGenericName($bulkfifthconfirm))
					$output .= $this->displayError( $this->l('Invalid Configuration values') ); 
				else
				{
					Configuration::updateValue('BULKCHANGESTATUS_FIRST', $bulkfirst);
					Configuration::updateValue('BULKCHANGESTATUS_SECOND', $bulksecond);
					Configuration::updateValue('BULKCHANGESTATUS_THIRD', $bulkthird);
					Configuration::updateValue('BULKCHANGESTATUS_FOURTH', $bulkfourth);
					Configuration::updateValue('BULKCHANGESTATUS_FIFTH', $bulkfifth);
					Configuration::updateValue('BULKCHANGESTATUS_FIRSTTEXT', $bulkfirsttext);
					Configuration::updateValue('BULKCHANGESTATUS_SECONDTEXT', $bulksecondtext);
					Configuration::updateValue('BULKCHANGESTATUS_THIRDTEXT', $bulkthirdtext);
					Configuration::updateValue('BULKCHANGESTATUS_FOURTHTEXT', $bulkfourthtext);
					Configuration::updateValue('BULKCHANGESTATUS_FIFTHTEXT', $bulkfifthtext);
					Configuration::updateValue('BULKCHANGESTATUS_FIRSTCONFIRM', $bulkfirstconfirm);
					Configuration::updateValue('BULKCHANGESTATUS_SECONDCONFIRM', $bulksecondconfirm);
					Configuration::updateValue('BULKCHANGESTATUS_THIRDCONFIRM', $bulkthirdconfirm);
					Configuration::updateValue('BULKCHANGESTATUS_FOURTHCONFIRM', $bulkfourthconfirm);
					Configuration::updateValue('BULKCHANGESTATUS_FIFTHCONFIRM', $bulkfifthconfirm);
					
					$output .= $this->displayConfirmation($this->l('Settings updated'));
				}
			}
			return $output.$this->displayForm();
		}
	public function displayForm()
			{
				// Get default Language
				$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
				 
				// Init Fields form array
				$fields_form[0]['form'] = array(
					'legend' => array(
						'title' => $this->l('Settings'),
					),
					'input' => array( 
						array(
							'type' => 'text',
							'label' => $this->l('First Status ID'),
							'name' => 'BULKCHANGESTATUS_FIRST',
							'size' => 20,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('First Text'),
							'name' => 'BULKCHANGESTATUS_FIRSTTEXT',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('First Confirmation Text'),
							'name' => 'BULKCHANGESTATUS_FIRSTCONFIRM',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Second Status ID'),
							'name' => 'BULKCHANGESTATUS_SECOND',
							'size' => 20,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Second Text'),
							'name' => 'BULKCHANGESTATUS_SECONDTEXT',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Second Confirmation Text'),
							'name' => 'BULKCHANGESTATUS_SECONDCONFIRM',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Third Status ID'),
							'name' => 'BULKCHANGESTATUS_THIRD',
							'size' => 20,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Third Text'),
							'name' => 'BULKCHANGESTATUS_THIRDTEXT',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Third Confirmation Text'),
							'name' => 'BULKCHANGESTATUS_THIRDCONFIRM',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fourth Status ID'),
							'name' => 'BULKCHANGESTATUS_FOURTH',
							'size' => 20,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fourth Text'),
							'name' => 'BULKCHANGESTATUS_FOURTHTEXT',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fourth Confirmation Text'),
							'name' => 'BULKCHANGESTATUS_FOURTHCONFIRM',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fifth Status ID'),
							'name' => 'BULKCHANGESTATUS_FIFTH',
							'size' => 20,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fifth Text'),
							'name' => 'BULKCHANGESTATUS_FIFTHTEXT',
							'size' => 50,
							'required' => true
						),
						array(
							'type' => 'text',
							'label' => $this->l('Fifth Confirmation Text'),
							'name' => 'BULKCHANGESTATUS_FIFTHCONFIRM',
							'size' => 50,
							'required' => true
						),
						
						
					),
					
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button'
					)
				);
				 
				$helper = new HelperForm();
				 
				// Module, t    oken and currentIndex
				$helper->module = $this;
				$helper->name_controller = $this->name;
				$helper->token = Tools::getAdminTokenLite('AdminModules');
				$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
				 
				// Language
				$helper->default_form_language = $default_lang;
				$helper->allow_employee_form_lang = $default_lang;
				 
				// Title and toolbar
				$helper->title = $this->displayName;
				$helper->show_toolbar = true;        // false -> remove toolbar
				$helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
				$helper->submit_action = 'submit'.$this->name;
				$helper->toolbar_btn = array(
					'save' =>
					array(
						'desc' => $this->l('Save'),
						'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
						'&token='.Tools::getAdminTokenLite('AdminModules'),
					),
					'back' => array(
						'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
						'desc' => $this->l('Back to list')
					)
				);
				 
				// Load current value
				$helper->fields_value['BULKCHANGESTATUS_FIRST'] = Configuration::get('BULKCHANGESTATUS_FIRST');
				$helper->fields_value['BULKCHANGESTATUS_FIRSTTEXT'] = Configuration::get('BULKCHANGESTATUS_FIRSTTEXT');
				$helper->fields_value['BULKCHANGESTATUS_FIRSTCONFIRM'] = Configuration::get('BULKCHANGESTATUS_FIRSTCONFIRM');
				$helper->fields_value['BULKCHANGESTATUS_SECOND'] = Configuration::get('BULKCHANGESTATUS_SECOND');
				$helper->fields_value['BULKCHANGESTATUS_SECONDTEXT'] = Configuration::get('BULKCHANGESTATUS_SECONDTEXT');
				$helper->fields_value['BULKCHANGESTATUS_SECONDCONFIRM'] = Configuration::get('BULKCHANGESTATUS_SECONDCONFIRM');
				$helper->fields_value['BULKCHANGESTATUS_THIRD'] = Configuration::get('BULKCHANGESTATUS_THIRD');
				$helper->fields_value['BULKCHANGESTATUS_THIRDTEXT'] = Configuration::get('BULKCHANGESTATUS_THIRDTEXT');
				$helper->fields_value['BULKCHANGESTATUS_THIRDCONFIRM'] = Configuration::get('BULKCHANGESTATUS_THIRDCONFIRM');
				$helper->fields_value['BULKCHANGESTATUS_FOURTH'] = Configuration::get('BULKCHANGESTATUS_FOURTH');
				$helper->fields_value['BULKCHANGESTATUS_FOURTHTEXT'] = Configuration::get('BULKCHANGESTATUS_FOURTHTEXT');
				$helper->fields_value['BULKCHANGESTATUS_FOURTHCONFIRM'] = Configuration::get('BULKCHANGESTATUS_FOURTHCONFIRM');
				$helper->fields_value['BULKCHANGESTATUS_FIFTH'] = Configuration::get('BULKCHANGESTATUS_FIFTH');
				$helper->fields_value['BULKCHANGESTATUS_FIFTHTEXT'] = Configuration::get('BULKCHANGESTATUS_FIFTHTEXT');
				$helper->fields_value['BULKCHANGESTATUS_FIFTHCONFIRM'] = Configuration::get('BULKCHANGESTATUS_FIFTHCONFIRM');
				 
				return $helper->generateForm($fields_form);
			}	
		}

?>