<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\SlickReaderBundle;

use HeimrichHannot\SlickReaderBundle\DependencyInjection\SlickReaderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoSlickReaderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new SlickReaderExtension();
    }
}
