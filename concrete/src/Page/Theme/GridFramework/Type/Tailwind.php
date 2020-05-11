<?php
namespace Concrete\Core\Page\Theme\GridFramework\Type;

use Concrete\Core\Page\Theme\GridFramework\GridFramework;

class Tailwind extends GridFramework
{
    public function supportsNesting()
    {
        return true;
    }

    public function getPageThemeGridFrameworkName()
    {
        return t('Tailwind CSS');
    }

    public function getPageThemeGridFrameworkRowStartHTML()
    {
        return '<div class="flex flex-row">';
    }

    public function getPageThemeGridFrameworkRowEndHTML()
    {
        return '</div>';
    }

    public function getPageThemeGridFrameworkContainerStartHTML()
    {
        return '<div class="container">';
    }

    public function getPageThemeGridFrameworkContainerEndHTML()
    {
        return '</div>';
    }


    public function getPageThemeGridFrameworkEditRowClass()
    {
        return 'flex flex-row w-full';
    }

    public function getPageThemeGridFrameworkColumnClasses()
    {
        $columns = [
            'w-1/12',
            'w-1/6',
            'w-1/4',
            'w-1/3',
            'w-5/12',
            'w-1/2',
            'w-7/12',
            'w-2/3',
            'w-3/4',
            'w-5/6',
            'w-11/12',
            'w-full',
        ];

        return $columns;
    }

    public function getPageThemeGridFrameworkColumnOffsetClasses()
    {
        $offsets = [];

        return $offsets;
    }

    public function getPageThemeGridFrameworkColumnAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkColumnOffsetAdditionalClasses()
    {
        return '';
    }

    public function getPageThemeGridFrameworkHideOnExtraSmallDeviceClass()
    {
        return 'xs:hidden';
    }

    public function getPageThemeGridFrameworkHideOnSmallDeviceClass()
    {
        return 'sm:hidden';
    }

    public function getPageThemeGridFrameworkHideOnMediumDeviceClass()
    {
        return 'md:hidden';
    }

    public function getPageThemeGridFrameworkHideOnLargeDeviceClass()
    {
        return 'lg:hidden';
    }
}
