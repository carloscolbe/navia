<?php

namespace Navia\FormFields;

class DateHandler extends AbstractHandler
{
    protected $codename = 'date';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('navia::formfields.date', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
