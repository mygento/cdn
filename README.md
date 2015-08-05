CDN module
==========
[![Build Status](https://travis-ci.org/mygento/cdn.svg?branch=master)](https://travis-ci.org/mygento/cdn) [![Code Climate](https://codeclimate.com/github/mygento/cdn/badges/gpa.svg)](https://codeclimate.com/github/mygento/cdn) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mygento/cdn/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mygento/cdn/?branch=master)

Issues
-------
[GitHub](https://github.com/mygento/cdn/issues).

Working with Adapters
========
- Amazon S3 ✓
- Selectel  ✓
- FTP



Features
========
- Upload product image cache in CDN and replace url ✓
- Upload JS merge cache in CDN and replace url ✓
- Upload CSS merge cache in CDN and replace url ✓
- Upload on product image save and download source image if absent ✓
- Upload wysiwyg images and CMS media directives ✓
- Async upload ✓
- Upload skin folder ✓
- Upload js folder ✓
- Gzip && Minification ✓
- Expires (S3-only) ✓
- Mage::getBaseUrl('media')
- Favicon
- Mage::getSkinUrl
- Category Images



### Install a module in your project
If you want to use [the our Magento module repository](http://mygento.github.io/packages),
set up your root ```composer.json``` in your project like this:

```json
{
    "require": {
        "mygento/cdn": "1.*",
    },
    "repositories": [
        {
            "type": "composer",
            "url": "http://mygento.github.io/packages"
        }
    ]
}
```

www.mygento.ru
