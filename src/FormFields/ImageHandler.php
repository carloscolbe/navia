<?php

namespace Navia\FormFields;

class ImageHandler extends AbstractHandler
{
    protected $codename = 'image';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('navia::formfields.image', [
            'row'             => $row,
            'options'         => $options,
            'dataType'        => $dataType,
            'dataTypeContent' => $dataTypeContent,
        ]);
    }
}
