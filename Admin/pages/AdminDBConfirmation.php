<?php

require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'Swat/SwatViewSelection.php';
require_once 'Admin/pages/AdminConfirmation.php';
require_once 'Admin/AdminDependency.php';
require_once 'Admin/exceptions/AdminException.php';

/**
 * Generic admin database confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * DB confirmation page, inherit directly from AdminConfirmation instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBConfirmation extends AdminConfirmation
{
	// {{{ protected properties

	protected $items = null;

	// }}}
	// {{{ public function setHiddenField()

	/**
	 * Set Hidden Field of Items
	 *
	 * @param array $items
	 */
	public function setHiddenField($items)
	{
		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('items', $items);
	}

	// }}}
	// {{{ public function setItems()

	/**
	 * Set items 
	 *
	 * @param array $items Array of items
	 */
	public function setItems($items)
	{
		if ($items instanceof SwatViewSelection &&
			count($items) > 0) {
			$this->items = array();
			foreach ($items as $item)
				$this->items[] = $item;
		} else {
			$this->items = $items;
		}

		$this->setHiddenField($this->items);
	}

	// }}}
	// {{{ protected function getItemList()

	/**
	 * Get quoted item list 
	 *
	 * @param string $type MDB2 datatype used to quote the items.
	 * @return string Comma-seperated and MDB2 quoted list of items.
	 * @throws AdminException
	 */
	protected function getItemList($type = 'integer')
	{
		$this->checkItems();
		return $this->app->db->implodeArray($this->items, $type);
	}

	// }}}
	// {{{ protected function getItemCount()

	/**
	* Get the number of items
	*
	* @return integer Number of items.
	* @throws AdminException
	*/
	protected function getItemCount()
	{
		$this->checkItems();
		return count($this->items);
	}

	// }}}
	// {{{ protected function getFirstItem()

	/**
	 * Get first item in the item list
	 *
	 * @return mixed the first item.
	 * @throws AdminException
	 */
	protected function getFirstItem()
	{
		$this->checkItems();
		reset($this->items);
		return current($this->items);
	}

	// }}}
	// {{{ private function checkItems()

	private function checkItems()
	{
		if (!is_array($this->items))
			throw new AdminException('There are no items. '.
				'AdminDBConfirmation::setItems() should be called to provide '.
				'an array of items for this confirmation page.');
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$this->items = $form->getHiddenField('items');

		$id = SiteApplication::initVar('id', null, SiteApplication::VAR_GET);

		if ($id !== null)
			$this->setItems(array($id));
	}

	// }}}

	// {{{ protected function processResponse()

	protected function processResponse()
	{
		$form = $this->ui->getWidget('confirmation_form');

		if ($form->button->id == 'yes_button') {
			try {
				$transaction = new SwatDBTransaction($this->app->db);
				$this->processDBData();
				$transaction->commit();

			} catch (SwatDBException $e) {
				$transaction->rollback();
				$this->generateMessage($e);
				$e->process();

			} catch (SwatException $e) {
				$this->generateMessage($e);
				$e->process();
			}
		}
	}

	// }}}
	// {{{ protected function generateMessage()

	protected function generateMessage(Exception $e)
	{
		if ($e instanceof SwatDBException)
			$message = new SwatMessage(
				Admin::_('A database error has occured.'),
				SwatMessage::SYSTEM_ERROR);
		else
			$message = new SwatMessage(Admin::_('An error has occured.'),
				SwatMessage::SYSTEM_ERROR);

		$this->app->messages->add($message);	
	}

	// }}}
	// {{{ protected function processDBData()

	/**
	 * Process data in the database
	 *
	 * This method is called to process data after an affirmative
	 * confirmation response.  Sub-classes should implement this method and
	 * perform whatever actions are necessary process the repsonse.
	 */
	protected function processDBData()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$this->items = $form->getHiddenField('items');
	}

	// }}}
}

?>
