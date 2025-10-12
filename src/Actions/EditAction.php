<?php

namespace Navia\Actions;

class EditAction extends AbstractAction
{
    public function getTitle()
    {
        return __('navia::generic.edit');
    }

    public function getIcon()
    {
        return 'voyager-edit';
    }

    public function getPolicy()
    {
        return 'edit';
    }

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm btn-primary pull-right edit',
        ];
    }

    public function getDefaultRoute()
    {
        return route('navia.'.$this->dataType->slug.'.edit', $this->data->{$this->data->getKeyName()});
    }
}
