<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SlickReaderBundle\ConfigElementType;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\ReaderBundle\ConfigElementType\ConfigElementType;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;

class SlickGalleryConfigElementType implements ConfigElementType
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework = $framework;
    }

    public function addToItemData(ItemInterface $item, ReaderConfigElementModel $readerConfigElement)
    {
        $projectDir = System::getContainer()->get('huh.utils.container')->getProjectDir() . '/';

        if (!($imageSelectorField = $readerConfigElement->imageSelectorField) || !$item->getRawValue($imageSelectorField) ||
            (!$imageField = $readerConfigElement->imageField) || !$item->getRawValue($imageField)) {
            return;
        }

        if (null === ($config = System::getContainer()->get('huh.slick.model.config')->findByPk($readerConfigElement->slickConfig))) {
            return;
        }

        /** @var FilesModel $filesModel */
        $filesModel = $this->framework->getAdapter(FilesModel::class);
        
        $imageSrc = $readerConfigElement->orderField ? $item->getRawValue($readerConfigElement->orderField) : $item->getRawValue($readerConfigElement->orderField);

        $images = StringUtil::deserialize($imageSrc, true);

        if (empty($images)) {
            return;
        }

        // prepare images
        $preparedImages = [];

        foreach ($images as $image) {
            if (null === ($imageFile = $filesModel->findByUuid($image))) {
                continue;
            }

            if (null === $imageFile
                || !file_exists($projectDir . $imageFile->path)
            ) {
                continue;
            }

            if ($imageFile->type == 'folder') {
                $dirname = $imageFile->path;

                foreach (array_diff(scandir($projectDir . $imageFile->path), ['..', '.']) as $fileName) {
                    if (null === ($imageFile = $filesModel->findByPath($dirname . '/' . $fileName))) {
                        continue;
                    }

                    $preparedImages[] = $this->prepareImage($imageFile, $readerConfigElement->imgSize, $image, $imageSelectorField);
                }
            } else {
                $preparedImages[] = $this->prepareImage($imageFile, $readerConfigElement->imgSize, $image, $imageSelectorField);
            }
        }

        // create slick
        $galleries              = $item->slickGalleries ?? [];
        $galleries[$imageField] = [
            'images'         => $preparedImages,
            'dataAttributes' => System::getContainer()->get('huh.slick.config')->getAttributesFromModel($config)
        ];

//        $gallery = '<div class="slick-container slick-slider" '.$galleries[$imageField]['dataAttributes'].'>';
//        foreach($galleries[$imageField]['images'] as $image)
//        {
//            $gallery .= '<div class="slick-slide"><img src="'.$image['src'].'" ></div>';
//        }
//
//        $gallery .= '</div>';
        
        
        $item->setFormattedValue('slickGalleries', $galleries);
    }

    protected function prepareImage($imageFile, $imageSize, $imageField, $imageSelectorField)
    {
        $imageArray = [];

        // Override the default image size
        if ('' != $imageSize) {
            $size = StringUtil::deserialize($imageSize);

            if ($size[0] > 0 || $size[1] > 0 || is_numeric($size[2])) {
                $imageArray['size'] = $imageSize;
            }
        }

        $imageArray[$imageField] = $imageFile->path;

        $templateData[$imageField] = [];

        System::getContainer()->get('huh.utils.image')->addToTemplateData(
            $imageField,
            $imageSelectorField,
            $templateData[$imageField],
            $imageArray,
            null,
            null,
            null,
            $imageFile
        );

        return $templateData[$imageField];
    }
}

