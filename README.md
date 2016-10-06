ConfigurationBundle
=====================

[![Build Status](https://travis-ci.org/Lp-digital/configuration-bundle.svg?branch=master)](https://travis-ci.org/Lp-digital/configuration-bundle)
[![Code Climate](https://codeclimate.com/github/Lp-digital/configuration-bundle/badges/gpa.svg)](https://codeclimate.com/github/Lp-digital/ConfigurationBundle)
[![Test Coverage](https://codeclimate.com/github/Lp-digital/configuration-bundle/badges/coverage.svg)](https://codeclimate.com/github/Lp-digital/ConfigurationBundle/coverage)

**ConfigurationBundle** provides useful configuration per site feature.
This bundle support multi-sites instances.

Installation
------------

Edit the file `composer.json` of your BackBee project.

Add the new dependency to the bundle in the `require` section:
```json
# composer.json
...
    "require": {
        ...
        "lp-digital/configuration-bundle": "dev-master"
    },
...
```

Save and close the file.

Run a composer update on your project.

Activation
----------

Edit the file `repository/Config/bundles.yml`of your BackBee project.

Add the following line at the end of the file:
```yaml
# bundles configuration - repository/Config/bundles.yml
...
conf: LpDigital\Bundle\ConfigurationBundle\Configuration
```

Save and close the file.

Then launch the command to update database:
```
./backbee bundle:update conf --force
```

Depending on your configuration, cache may need to be cleared.

Configuration
-------------

Basically, one configuration section is a collection of elements having the same syntax than BackBee parameters, see [Parameters reference](http://docs.backbee.com/developer-documentation/components/classcontent/#parameters-reference):
```yaml
mysection:
    title: My section tite
    desc:  My section description
    elements:
        myelement:
            type: text
            label: My element label
            value: "Sample value"
```

Then, this configuration value will be available in templates throw the CoonfigHelper:
```twig
<div>
    {{ this.ConfigHelper('mysection:myelement') }} {# echo Sample value #}
</div>
```

These sections and elements will then available for editing from the Bundle panel of the administrative toolbar.
There is no limit for the number of sections or the number of elements in one section.

In order to add some configuration values in your BackBee project, create and edit the file:
`/repository/Config/bundle/conf/config.yml`

For example, the following shows you every element types available:
```yaml
#/repository/Config/bundle/conf/config.yml
sample:
    title: Fake Section
    desc: Fake section configuration
    elements:
        text:
            type: text
            label: Example for text value
            value: ""
        password:
            type: password
            label: Example for password value
            value: ""
        datetimepicker:
            type: datetimepicker
            label: Example for datetimepicker value
            value: 0
        textarea:
            type: textarea
            label: Example for textarea value
            value: ""
        checkbox:
            type: checkbox
            label: Example for checkbox value
            options: {'fake1': 'Fake value 1', 'fake2': 'Fake value 2'}
            value: []
            inline: true
        radio:
            type: radio
            label: Example for radio value
            options: {'fake1': 'Fake value 1', 'fake2': 'Fake value 2'}
            value: []
            inline: true
        select:
            type: select
            label: Example for select value
            options: {'fake1': 'Fake value 1', 'fake2': 'Fake value 2'}
            value: []
        nodeSelector:
            type: nodeSelector
            label: Example for nodeSelector value
            value: []
        mediaSelector:
            type: mediaSelector
            label: Example for mediaSelector value
            value: []
        linkSelector:
            type: linkSelector
            label: Example for linkSelector value
            value: []
```

Save and close the file, optionnaly empty the cache and enjoy...

---

*This project is supported by [Lp digital](http://www.lp-digital.fr/en/)*

**Lead Developer** : [@cedric-b](https://github.com/cedric-b)

Released under the GPL3 License