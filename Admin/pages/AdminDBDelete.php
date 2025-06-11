<?php

/**
 * Generic admin database delete page.
 *
 * This class is intended to be a convenience base class. For a fully custom
 * delete page, inherit directly from {@link AdminConfirmation} or
 * {@link AdminDBConfirmation} instead.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBDelete extends AdminDBConfirmation
{
    protected $single_delete = false;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->single_delete = (bool) SiteApplication::initVar(
            'single_delete',
            false,
            SiteApplication::VAR_POST
        );

        $id = SiteApplication::initVar('id', null, SiteApplication::VAR_GET);

        if ($id !== null) {
            $this->setItems([$id]);
            $this->single_delete = true;
            $form = $this->ui->getWidget('confirmation_form');
            $form->addHiddenField('single_delete', $this->single_delete);
        }

        $yes_button = $this->ui->getWidget('yes_button');
        $yes_button->setFromStock('delete');

        $no_button = $this->ui->getWidget('no_button');
        $no_button->setFromStock('cancel');

        $this->navbar->popEntry();
        $this->navbar->createEntry(Admin::_('Delete'));
    }

    // process phase

    protected function generateMessage(Throwable $e)
    {
        if ($e instanceof SwatDBException) {
            $message = new SwatMessage(
                Admin::_('A database error has occured.
				The item(s) were not deleted.'),
                'system-error'
            );
        } else {
            $message = new SwatMessage(
                Admin::_('An error has occured.
				The item(s) were not deleted.'),
                'system-error'
            );
        }

        $this->app->messages->add($message);
    }

    protected function relocate()
    {
        $form = $this->ui->getWidget('confirmation_form');
        $url = $form->getHiddenField(self::RELOCATE_URL_FIELD);
        // always search for only the component name with slashes. This helps
        // for cases where the page name has a match to the component name we're
        // checking against, and if the trailing slash is missing, we're
        // redirecting to the index anyway.
        $component = '/' . $this->getComponentName() . '/';

        // if the component name is in the relocate url, relocate to the
        // the component index page to prevent relocating to a details page that
        // will no longer exist.
        if (mb_strpos($url, $component) === false
            || $form->button->id == 'no_button') {
            parent::relocate();
        } else {
            $this->app->relocate($this->getComponentName());
        }
    }
}
