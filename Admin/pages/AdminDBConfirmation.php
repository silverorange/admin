<?php

require_once('Admin/pages/AdminConfirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

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
		$this->items = $items;
		$this->setHiddenField($items);
	}

	// }}}
	// {{{ protected function getItemList()

	/**
	 * Get quoted item list 
	 *
	 * @param string $type MDB2 datatype used to quote the items.
	 * @return string Comma-seperated and MDB2 quoted list of items.
	 */
	protected function getItemList($type)
	{
		$items = $this->items;
		
		foreach ($items as &$id)
			$id = $this->app->db->quote($id, $type);

		return implode(',',$items);
	}

	// }}}
	// {{{ protected function getItemCount()

	/**
	* Get the number of items
	*
	* @return integer Number of items.
	*/
	protected function getItemCount()
	{
		return count($this->items);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$id = SwatApplication::initVar('id', null, SwatApplication::VAR_GET);

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
				$this->app->db->beginTransaction();
				$this->processDBData();
				$this->app->db->commit();

			} catch (SwatDBException $e) {
				$this->app->db->rollback();
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
			$msg = new SwatMessage(Admin::_('A database error has occured.'), SwatMessage::SYSTEM_ERROR);
		else
			$msg = new SwatMessage(Admin::_('An error has occured.'), SwatMessage::SYSTEM_ERROR);

		$this->app->messages->add($msg);	
	}

	// }}}
	// {{{ protected function processDBData()

	/**
	 * Process data in the database
	 *
	 * This method is called to process data after an affirmative confirmation response.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary process the repsonse.
	 */
	protected function processDBData()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$this->items = $form->getHiddenField('items');
	}

	// }}}
}

?>
