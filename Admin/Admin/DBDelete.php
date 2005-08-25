<?php

require_once 'Admin/Admin/DBConfirmation.php';
require_once 'SwatDB/SwatDBException.php';

/**
 * Generic admin database delete page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * delete page, inherit directly from {@link AdminConfirmation} or
 * {@link @AdminDBConfirmation} instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBDelete extends AdminDBConfirmation
{
	protected $items = null;

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

	/**
	 * Get the number of items
	 *
	 * @return integer Number of items.
	 */
	protected function getItemCount()
	{
		return count($this->items);
	}

	protected function processDBData()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$this->items = $form->getHiddenField('items');
	}

	protected function processGenerateMessage(Exception $e)
	{
		if ($e instanceof SwatDBException) {
			$msg = new SwatMessage(Admin::_('A database error has occured.
				The item(s) were not deleted.'),
				 SwatMessage::SYSTEM_ERROR);
		} else {
			$msg = new SwatMessage(Admin::_('An error has occured.
				The item(s) were not deleted.'),
				SwatMessage::SYSTEM_ERROR);
		}

		$this->app->messages->add($msg);	
	}
}

?>
