<?php

/**
 * Generic admin search page.
 *
 * This class is intended to be a convenience base class. For a fully custom
 * search page, inherit directly from AdminPage instead.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminSearch extends AdminIndex
{
    // process phase

    protected function processInternal()
    {
        parent::processInternal();

        $form_found = true;

        try {
            $form = $this->ui->getWidget('search_form');
        } catch (SwatWidgetNotFoundException $e) {
            $form_found = false;
        }

        if ($form_found) {
            $form->process();

            if ($form->isProcessed()) {
                $this->saveState();
            }

            if ($this->hasState()) {
                $this->loadState();
                $frame = $this->ui->getWidget('results_frame');
                $frame->visible = true;
            }
        }
    }

    /**
     * Clears a saved search state.
     */
    protected function clearState()
    {
        if ($this->hasState()) {
            unset($this->app->session->{$this->getKey()});
        }
    }

    protected function saveState()
    {
        $form_found = true;

        try {
            $search_form = $this->ui->getWidget('search_form');
        } catch (SwatWidgetNotFoundException $e) {
            $form_found = false;
        }

        if ($form_found) {
            $search_state = $search_form->getDescendantStates();
            $this->app->session->{$this->getKey()} = $search_state;
        }
    }

    /**
     * Loads a saved search state for this page.
     *
     * @return bool true if a saved state exists for this page and false if
     *              it does not
     *
     * @see AdminSearchPage::hasState()
     */
    protected function loadState()
    {
        $return = false;

        $form_found = true;

        try {
            $search_form = $this->ui->getWidget('search_form');
        } catch (SwatWidgetNotFoundException $e) {
            $form_found = false;
        }

        if ($form_found) {
            if ($this->hasState()) {
                $search_form->setDescendantStates(
                    $this->app->session->{$this->getKey()}
                );

                $return = true;
            }
        }

        return $return;
    }

    /**
     * Checks if this search page has stored search information.
     *
     * @return bool true if this page has stored search information and
     *              false if it does not
     */
    protected function hasState()
    {
        return isset($this->app->session->{$this->getKey()});
    }

    protected function getKey()
    {
        return $this->source . '_search_state';
    }

    // build phase

    protected function buildInternal()
    {
        parent::buildInternal();

        try {
            $form = $this->ui->getWidget('search_form');
            $form->action = $this->source;
            $form->autofocus = true;
        } catch (SwatWidgetNotFoundException $e) {
            // ignore
        }
    }

    /**
     * Builds views for this search page.
     *
     * View models are initialized to an empty table store unless a saved
     * search state is available.
     */
    protected function buildViews()
    {
        $form_found = true;

        try {
            $results_frame = $this->ui->getWidget('results_frame');
        } catch (SwatWidgetNotFoundException $e) {
            $form_found = false;
        }

        if ($form_found) {
            // set non-visible results frame views to have empty models
            if (!$results_frame->visible) {
                $views = $results_frame->getDescendants(SwatTableView::class);
                foreach ($views as $view) {
                    $view->model = new SwatTableStore();
                }
            }
        }

        // build other views normally
        parent::buildViews();
    }
}
