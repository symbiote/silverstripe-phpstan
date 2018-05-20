<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

use SilverStripe\Core\ClassInfo;

/**
 * A helper class that has references to various core SilverStripe class names.
 * This exists to *hopefully* make maintaining SS3 + SS4 simultaneously simple.
 */
class ClassHelper
{
    // const SSObject = 'Object'; // Removed in SS 4.X
    const ViewableData = \SilverStripe\View\ViewableData::class;
    const DataObject = \SilverStripe\ORM\DataObject::class;
    const Extensible = \SilverStripe\Core\Extensible::class;

    // DataList
    const DataList = \SilverStripe\ORM\DataList::class;
    const HasManyList = \SilverStripe\ORM\HasManyList::class;
    const ManyManyList = \SilverStripe\ORM\ManyManyList::class;

    // Injector
    const Injector = \SilverStripe\Core\Injector\Injector::class;
    const Extension = \SilverStripe\Core\Extension::class;

    // Versioned
    const Versioned = \SilverStripe\Versioned\Versioned::class;

    // Controller
    const ContentController = \SilverStripe\CMS\Controllers\ContentController::class;
    const RequestFilter = \SilverStripe\Control\RequestFilter::class;

    // FormField
    const DBField = \SilverStripe\ORM\FieldType\DBField::class;
    const FormField = \SilverStripe\Forms\FormField::class;
    const StringField = \SilverStripe\ORM\FieldType\DBString::class;
    const DBInt = \SilverStripe\ORM\FieldType\DBInt::class;
    const DBFloat = \SilverStripe\ORM\FieldType\DBFloat::class;
    const HTMLText = \SilverStripe\ORM\FieldType\DBHTMLText::class;

    // ORM
    const MySQLDatabase = \SilverStripe\ORM\Connect\MySQLDatabase::class;

    // File
    const File = \SilverStripe\Assets\File::class;

    // Session
    const CookieJar = \SilverStripe\Control\CookieJar::class;
    const Cookie_Backend = \SilverStripe\Control\Cookie_Backend::class;

    // CMS
    const SiteTree = \SilverStripe\CMS\Model\SiteTree::class;

    // SilverStripe 4.2+ (master branch at time of writing, 4.2+ is an assumption)
    // NOTE(Jake): 2018-05-17: Using a string instead of ::class to not break SilverStripe 4.0 PHPStan scanning of this project
    const SiteTreeLink = 'SilverStripe\CMS\Model\SiteTreeLink';

    public static function getSubclassesFor($className)
    {
        return ClassInfo::subclassesFor($className);
    }
}
