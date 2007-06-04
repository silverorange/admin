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

	protected $selection = null;

	// }}}
	// {{{ public function setSelection()

	/**
	 * Set selection
	 *
	 * @param SwatViewSelection $selection
	 */
	public function setSelection(SwatViewSelection $selection)
	{
		$this->selection = $selection;
		$this->setHiddenField();
	}

	// }}}
	// {{{ public function setItems()

	/**
	 * Set items
	 *
	 * This allows the setting of the confirmation selection using an array
	 * of values. The preferred method is to pass a {@link
	 * SwatViewSelection} to {@link AdminDBConfirmation::setSelection()}.
	 *
	 * @param array $items an array of items
	 */
	public function setItems($items)
	{
		$selection = new SwatViewSelection($items);
		$this->setSelection($selection);
	}

	// }}}
	// {{{ protected function setHiddenField()

	/**
	 * Set hidden field to store the selection
	 */
	protected function setHiddenField()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('selection', $this->selection);
	}

	// }}}
	// {{{ protected function getItemList()

	/**
	 * Get quoted item list 
	 *
	 * @param string $type MDB2 datatype used to quote the items.
	 * @return string Comma-seperated and MDB2 quoted list of items.
	 */
	protected function getItemList($type = 'integer')
	{
		$list = array();

		foreach ($this->selection as $item)
			$list[] = $this->app->db->quote($item, $type);

		return implode(',', $list);
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
		return count($this->selection);
	}

	// }}}
	// {{{ protected function getFirstItem()

	/**
	 * Get first item in the item list
	 *
	 * @return mixed the first item.
	 */
	protected function getFirstItem()
	{
		reset($this->selection);
		return current($this->selection);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$this->selection = $form->getHiddenField('selection');

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
		$this->selection = $form->getHiddenField('selection');
	}

	// }}}
}

?>
