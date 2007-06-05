<?php

require_once 'Swat/SwatViewSelection.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'Admin/pages/AdminConfirmation.php';
require_once 'Admin/AdminDependency.php';
require_once 'Admin/exceptions/AdminException.php';

/**
 * Generic admin database confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * DB confirmation page, inherit directly from AdminConfirmation instead.
 *
 * @package   Admin
 * @copyright 2004-2007 silverorange
 */
abstract class AdminDBConfirmation extends AdminConfirmation
{
	// {{{ protected properties

	/**
	 * The current items of this confirmation page
	 *
	 * @var SwatViewSelection
	 *
	 * @see AdminDBConfirmation::setItems()
	 */
	protected $items;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new database-driven confirmation page
	 *
	 * @param SiteApplication $app
	 * @param SiteLayout $layout optional.
	 */
	public function __construct(SiteApplication $app, SiteLayout $layout = null)
	{
		parent::__construct($app, $layout);

		// don't use setItems() here because the UI has not been constructed
		// yet and the hidden value cannot be added to the form
		$this->items = new SwatViewSelection(array());
	}

	// }}}
	// {{{ public function setItems()

	/**
	 * Sets the items of this confirmation page
	 *
	 * @param SwatViewSelection|array $items the items of this confirmation
	 *                                        page. Developers are encouraged
	 *                                        to use a SwatViewSelection; array
	 *                                        is provided for backwards
	 *                                        compatibility.
	 */
	public function setItems($items)
	{
		if (is_array($items))
			$items = new SwatViewSelection($items);

		if (!($items instanceof SwatViewSelection))
			throw new SwatInvalidClassException(
				'The $items parameter must be either a SwatViewSelection or '.
				'an array.', 0, $items);

		$this->items = $items;

	 	// Add a hidden field to this confirmation page's form to store the
	 	// current items of this confirmation page across requests
		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('items', $this->items);
	}

	// }}}
	// {{{ protected function getItemList()

	/**
	 * Get the items of this confirmation page as a database-quoted list 
	 *
	 * @param string $type optional. The MDB2 datatype used to quote the items.
	 *                      By default, 'integer' is used.
	 *
	 * @return string a comma-seperated, database-quoted list of items.
	 */
	protected function getItemList($type = 'integer')
	{
		$list = array();

		foreach ($this->items as $item)
			$list[] = $this->app->db->quote($item, $type);

		return implode(',', $list);
	}

	// }}}
	// {{{ protected function getItemCount()

	/**
	 * Gets the number of items on this confirmation page
	 *
	 * @return integer the number of items on this confirmation page.
	 */
	protected function getItemCount()
	{
		return count($this->items);
	}

	// }}}
	// {{{ protected function getFirstItem()

	/**
	 * Gets the first item on this confirmation page
	 *
	 * @return mixed the first item.
	 *
	 * @see AdminDBConfirmation::setItems()
	 */
	protected function getFirstItem()
	{
		$this->items->rewind();
		return $this->items->current();
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$items = $form->getHiddenField('items');
		if ($items !== null)
			$this->setItems($items);

		$id = SiteApplication::initVar('id', null, SiteApplication::VAR_GET);

		if ($id !== null)
			$this->setItems(new SwatViewSelection(array($id)));
	}

	// }}}

	// process phase
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
		$this->setItems($form->getHiddenField('items'));
	}

	// }}}
}

?>
