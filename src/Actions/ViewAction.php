<?php

namespace Navia\Actions;

class ViewAction extends AbstractAction
{
    public function getTitle()
    {
        return __('navia::generic.view');
    }

    public function getIcon()
    {
        return 'navia-eye';
    }

    public function getPolicy()
    {
        return 'read';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-warning pull-right view',
        ];
    }

    public function getDefaultRoute()
    {
        return route('navia.'.$this->dataType->slug.'.show', $this->data->{$this->data->getKeyName()});
    }
}
