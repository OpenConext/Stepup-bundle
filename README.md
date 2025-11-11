# Step-up Bundle
[![Build Status](https://travis-ci.org/OpenConext/Stepup-bundle.svg)](https://travis-ci.org/OpenConext/Stepup-bundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/OpenConext/Stepup-bundle/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/OpenConext/Stepup-bundle/?branch=develop) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/5b8b8d8b-e917-4954-818b-782d9e181c75/mini.png)](https://insight.sensiolabs.com/projects/5b8b8d8b-e917-4954-818b-782d9e181c75)

A Symfony bundle that holds shared code and framework integration for all Step-up applications. See [Stepup-Deploy](https://github.com/OpenConext/Stepup-Deploy) for an overview of Stepup.

## Requirements

- PHP 8.2 or higher
- Symfony 6.3+ or 7.0+

## Installation

 * Add the package to your Composer file
    ```sh
    composer require surfnet/stepup-bundle
    ```

 * The bundle should be automatically registered in `config/bundles.php` (for Symfony Flex projects). If not, add it manually:
    ```php
    // config/bundles.php
    return [
        // ...
        Surfnet\StepupBundle\SurfnetStepupBundle::class => ['all' => true],
    ];
    ```

 * Copy and adjust the error templates to your application folder
    * `src/Resources/views/Exception/error.html.twig` → `templates/bundles/SurfnetStepupBundle/Exception/error.html.twig`
    * `src/Resources/views/Exception/error404.html.twig` → `templates/bundles/SurfnetStepupBundle/Exception/error404.html.twig`

### Install resources

For modern Symfony applications, you can use AssetMapper (recommended for Symfony 7.0+) or Webpack Encore to manage assets.

#### Using AssetMapper (Symfony 7.0+)

```bash
# Install AssetMapper if not already installed
composer require symfony/asset-mapper symfony/asset symfony/twig-pack
```

Copy the bundle's public assets to your project:
```bash
php bin/console assets:install --symlink
```

Then reference the assets in your templates:
```twig
<link href="{{ asset('bundles/surfnetstepup/less/stepup.css') }}" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="{{ asset('bundles/surfnetstepup/js/stepup.js') }}"></script>
```

#### Using Webpack Encore

Import the bundle's assets in your JavaScript/CSS entry point:
```javascript
// assets/app.js
import '../vendor/surfnet/stepup-bundle/src/Resources/public/js/stepup.js';

// assets/app.css
@import '../vendor/surfnet/stepup-bundle/src/Resources/public/less/stepup.less';
```

## Using the locale switcher

The locale switcher is a form that can be rendered with the help of a Twig function.

```twig
{% if app.user %}
    {% set locale_switcher = stepup_locale_switcher('handler_route', {'return-url': app.request.uri}) %}
    {{ form_start(locale_switcher, { attr: { class: 'form-inline' }}) }}
    {{ form_widget(locale_switcher.locale) }}
    {{ form_widget(locale_switcher.switch) }}
    {{ form_end(locale_switcher) }}
{% endif %}
```

Include the required assets:
```twig
<link href="{{ asset('bundles/surfnetstepup/less/style.css') }}" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="{{ asset('bundles/surfnetstepup/js/index.js') }}"></script>
```

## Release strategy

### CHANGELOG
The changelog for the bundle is kept in the `CHANGELOG.md` file. A history of the releases can be found in this file.
Previous release notes are kept in this file for history purposes. Please use markdown to style the changelog.  

Please update the changelog with any notable changes that are introduced in an upcoming release. If you are not yet 
certain what the next release number will be, give the release title a generic value like `Upcoming release`. Make sure
before merging the changes to the release branch to update the release title in the changelog.

**Example CHANGELOG entry**
```markdown
# 2.5.23
Brief explanation of the major changes of this release

## New features
 * Title of PR of the new feature #30
 * Support of POST binding for AuthnRequest #31
 
## Bugfixes
 * Title of PR of the bugfix #33

## Improvements
 * Title of PR of the improvement #29
```

When releasing a hotfix on a release branch, please update the changelog on the release branch and after releasing the
hotfix, also merge the hotfix to develop.