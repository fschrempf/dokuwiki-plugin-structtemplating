# Struct Templating Plugin for DokuWiki

Use twig templates for custom struct data rendering.

## Installing

If you install this plugin manually, make sure it is installed in
lib/plugins/structtemplating/ - if the folder is called different it
will not work!

Please refer to http://www.dokuwiki.org/plugins for additional info
on how to install plugins in DokuWiki.

## Usage

### Templates

After installing the plugin, an admin can add twig template files in the
`lib/plugin/structtemplating/assets/templates/schema` directory on the server.

Currently only templates for schema page data are supported. The filename of
the template file must be `<schema>.twig`.

You can access properties of the schema through the `schema` variable and
by calling functions available in the [`Schema` class](https://github.com/cosmocode/dokuwiki-plugin-struct/blob/master/meta/Schema.php).

The field data is available through the `data` array. Each element also has
a `rendered` property that contains the result of the DokuWiki's renderer.

The raw values can be accessed via `getValue` or other methods in the
[`Value` class](https://github.com/cosmocode/dokuwiki-plugin-struct/blob/master/meta/Value.php).

See `default.twig.example` for some examples.

### Stylesheets

By default the plugin installs a `style.less` stylesheet that imports
`assets/style/style.less`. This allows you to add your custom stylesheets in the
`assets/style/` directory and track them together with your templates.

### Tracking Changes in Templates and Stylesheets

The `assets` directory is empty when the plugin is installed. You can use it as
the root for a git repository to put your templates and stylesheets under
version control.

## Copyright

Copyright (C) Frieder Schrempf <dev@fris.de>

## License

GPL-2.0-only, see LICENSE file