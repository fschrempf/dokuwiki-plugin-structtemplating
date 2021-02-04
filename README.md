# Struct Templating Plugin for DokuWiki

Use twig templates for custom struct data rendering

## Installing

If you install this plugin manually, make sure it is installed in
lib/plugins/structtemplating/ - if the folder is called different it
will not work!

Please refer to http://www.dokuwiki.org/plugins for additional info
on how to install plugins in DokuWiki.

## Usage

After installing the plugin, an admin can add twig template files in the
`lib/plugin/structtemplating/templates/schema` directory on the server.

Currently only templates for schema page data is supported. The filename of
the template file must be `<schema>.twig`.

You can access properties of the schema through the `schema` variable and
by calling functions available in the [`Schema` class](https://github.com/cosmocode/dokuwiki-plugin-struct/blob/master/meta/Schema.php).

The field data is available through the `data` array. Each element also has
a `rendered` property that contains the result of the DokuWiki's renderer.

The raw values can be accessed via `getValue` or other methods in the
[`Value` class](https://github.com/cosmocode/dokuwiki-plugin-struct/blob/master/meta/Value.php).

See `templates/schema/default.twig.example` for some examples.

## Copyright

Copyright (C) Frieder Schrempf <dev@fris.de>

## License

GPL-2.0-only, see LICENSE file