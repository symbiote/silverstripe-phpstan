<?php declare(strict_types = 1);

namespace SilbinaryWolf\SilverstripePHPStan;

/**
 * A helper class that has references to various core SilverStripe class names.
 * This exists to *hopefully* make maintaining SS3 + SS4 simultaneously simple.
 */
class ClassHelper
{
    const SSObject = 'Object';
    const ViewableData = \ViewableData::class;
    const DataObject = \DataObject::class;

    // DataList
    const DataList = \DataList::class;
    const HasManyList = \HasManyList::class;
    const ManyManyList = \ManyManyList::class;

    // Injector
    const Injector = \Injector::class;
    const Extension = \Extension::class;

    // Versioned
    const Versioned = \Versioned::class;

    // Controller
    const ContentController = \ContentController::class;
    const RequestFilter = \RequestFilter::class;

    // FormField
    const DBField = \DBField::class;
    const FormField = \FormField::class;
    const StringField = \StringField::class;
    const DBInt = \DBInt::class;
    const DBFloat = \DBFloat::class;
    const HTMLText = \HTMLText::class;

    // ORM
    const MySQLDatabase = \MySQLDatabase::class;

    // File
    const File = \File::class;

    // Session
    const CookieJar = \CookieJar::class;
    const Cookie_Backend = \Cookie_Backend::class;

    // CMS
    const SiteTree = \SiteTree::class;
}
