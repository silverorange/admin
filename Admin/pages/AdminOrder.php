<?php

require_once 'Admin/Admin.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminPage.php';

/**
 * Generic admin ordering page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * ordering page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
abstract class AdminOrder extends AdminPage
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->getRoot()->addJavaScript(
			'packages/admin/javascript/admin-order.js', Admin::PACKAGE_ID);

		$this->ui->loadFromXML(dirname(__FILE__).'/order.xml');
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();
		$form = $this->ui->getWidget('order_form');

		if ($form->isProcessed()) {
			$this->saveData();
			$this->app->messages->add($this->getUpdatedMessage());
			$this->relocate();
		}
	}

	// }}}
	// {{{ protected function saveData()

	/**
	 * Saves ordering information
	 */
	protected function saveData()
	{
		$this->saveIndexes();
	}

	// }}}
	// {{{ protected function saveIndexes()

	/**
	 * Saves the updated ordering indexes of each option
	 *
	 * @see AdminOrder::saveIndex()
	 */
	protected function saveIndexes()
	{
		$count = 0;
		$order_widget = $this->ui->getWidget('order');
		$options_list = $this->ui->getWidget('options');

		foreach ($order_widget->values as $id) {
			if ($options_list->value == 'custom')
				$count++;

			$this->saveIndex($id, $count);
		}
	}

	// }}}
	// {{{ protected function relocate()

	/**
	 * Relocates after processing is complete
	 */
	protected function relocate()
	{
		$form = $this->ui->getWidget('order_form');
		$url = $form->getHiddenField(self::RELOCATE_URL_FIELD);
		$this->app->relocate($url);
	}

	// }}}
	// {{{ protected function getUpdatedMessage()

	/**
	 * Gets the message to show the user when the order is successfully updated
	 *
	 * @return SwatMessage a SwatMessage object containing the message to
	 *                      show the user when the order is successfully
	 *                      updated.
	 */
	protected function getUpdatedMessage()
	{
		return new SwatMessage(Admin::_('Order updated.'));
	}

	// }}}
	// {{{ abstract protected function saveIndex()

	/**
	 * Save index
	 *
	 * This method is called by {@link AdminOrder::saveIndexes()} to save a
	 * single ordering index. Sub-classes should implement this method and
	 * perform whatever actions are necessary to store the ordering index.
	 *
	 * @param mixed $id an integer identifier of the option to which
	 *                   ordering information is saved.
	 * @param integer $index the ordering index to store.
	 */
	abstract protected function saveIndex($id, $index);

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();
		$this->buildOptionList();
		$this->buildButton();
		$this->buildForm();
		$this->buildNavBar();
		$this->loadData();
	}

	// }}}
	// {{{ protected function buildOptionList()

	protected function buildOptionList()
	{
		$options_list = $this->ui->getWidget('options');
		$options_list->addOptionsByArray(array(
			'auto'=>Admin::_('Automatically'),
			'custom'=>Admin::_('Custom')));
	}

	// }}}
	// {{{ protected function buildButton()

	protected function buildButton()
	{
		$button = $this->ui->getWidget('submit_button');
		$button->title = Admin::_('Update Order');
	}
	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		$form = $this->ui->getWidget('order_form');
		$form->action = $this->source;

		if ($form->getHiddenField(self::RELOCATE_URL_FIELD) === null) {
			$url = $this->getRefererURL();
			$form->addHiddenField(self::RELOCATE_URL_FIELD, $url);
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		$this->navbar->createEntry(Admin::_('Change Order'));
	}

	// }}}
	// {{{ protected function display()

	protected function display()
	{
		parent::display();
		$this->displayJavaScript();
	}

	// }}}
	// {{{ abstract protected function loadData()

	/**
	 * Load the data
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 */
	abstract protected function loadData();

	// }}}
	// {{{ private function displayJavaScript()

	private function displayJavaScript()
	{
		echo '<script type="text/javascript">'."\n";

		printf("AdminOrder('options_custom', order_obj);\n");

		echo '</script>';
	}

	// }}}
}

?>
