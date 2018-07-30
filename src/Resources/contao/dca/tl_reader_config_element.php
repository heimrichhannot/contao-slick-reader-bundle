<?php

$dca = &$GLOBALS['TL_DCA']['tl_reader_config_element'];

/**
 * Palettes
 */
$dca['palettes'][\HeimrichHannot\SlickReaderBundle\Backend\ReaderConfigElement::TYPE_SLICK_GALLERY] = '{title_type_legend},title,type;{config_legend},slickConfig,imageSelectorField,imageField;';

/**
 * Fields
 */
$fields = [
    'slickConfig' => [
        'label'      => &$GLOBALS['TL_LANG']['tl_reader_config_element']['slickConfig'],
        'exclude'    => true,
        'filter'     => true,
        'inputType'  => 'select',
        'foreignKey' => 'tl_slick_config.title',
        'eval'       => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true, 'chosen' => true],
        'sql'        => "int(10) unsigned NOT NULL default '0'"
    ],
];

$dca['fields'] += $fields;