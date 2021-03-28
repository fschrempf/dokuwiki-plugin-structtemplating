<?php
/**
 * DokuWiki Plugin structtemplating (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Frieder Schrempf <dev@fris.de>
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use dokuwiki\plugin\struct\meta\AggregationTable;

require_once __DIR__ . '/vendor/autoload.php';

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class action_plugin_structtemplating extends DokuWiki_Action_Plugin
{

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     *
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('PLUGIN_STRUCT_RENDER_SCHEMA_DATA', 'BEFORE',
                                   $this, 'handle_struct_render_schema_data');
        $controller->register_hook('PLUGIN_STRUCT_RENDER_AGGREGATION_TABLE', 'BEFORE',
                                   $this, 'handle_struct_render_aggregation_table');
    }

    /**
     * [Custom event handler which performs action]
     *
     * Called for event: PLUGIN_STRUCT_RENDER_SCHEMA_DATA
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     *
     * @return void
     */
    public function handle_struct_render_schema_data(Doku_Event $event, $param)
    {
        $schemadata = $event->data['schemadata'];
        $meta = $event->data['meta'];
        $renderer = $event->data['renderer'];
        $format = $event->data['format'];

        $schemadata->optionSkipEmpty(true);
        $data = $schemadata->getData();
        if (!count($data))
            return;

        $event->data['hasdata'] = true; 

        $path = __DIR__ . '/assets/templates/schema';
        $loader = new FilesystemLoader($path);
        $twig = new Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        foreach ($data as $field) {
            $idx = strlen($renderer->doc);
            $field->render($renderer, $format);
            $field->rendered = substr($renderer->doc, $idx);
            $renderer->doc = substr($renderer->doc, 0, $idx);
        }

        try {
            $twigmarkup = $twig->render(
                $schemadata->getSchema()->getTable() . '.twig',
                [
                    'schema' => $schemadata->getSchema(),
                    'data' => $data,
                    'meta' => $meta
                ]
            );
            $renderer->doc .= $twigmarkup;
        } catch (Exception $e) {
            return;
        }

        $event->preventDefault();
    }

    /**
     * [Custom event handler which performs action]
     *
     * Called for event: PLUGIN_STRUCT_RENDER_AGGREGATION_TABLE
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     *
     * @return void
     */
    public function handle_struct_render_aggregation_table(Doku_Event $event, $param)
    {
        $renderer = $event->data['renderer'];
        $columns= $event->data['columns'];
        $table = $event->data['table'];
        $search = $event->data['search'];
        $data = $event->data['data'];

        $path = __DIR__ . '/assets/templates/aggregation';
        $loader = new FilesystemLoader($path);
        $twig = new Environment($loader, [
            'debug' => true,
        ]);
        $twig->addExtension(new \Twig\Extension\DebugExtension());

        /*
         * If the data covers a single schema, we look for a matching
         * aggregation template, if none is found or the data spans
         * over multiple schemas, we use the page id to lookup a template
         * file.
         */
        $templates = array();
        if (count($search->getSchemas()) === 1)
            $templates[] = $search->getSchemas()[0]->getTable() . '.twig';

        $templates[] = $table->getID() . '.twig';

        foreach ($data as $row) {
            foreach ($row as $field) {
                $idx = strlen($renderer->doc);
                $field->render($renderer, $format);
                $field->rendered = substr($renderer->doc, $idx);
                $renderer->doc = substr($renderer->doc, 0, $idx);
            }
        }

        $rendered = false;
        foreach ($templates as $template) {
            try {
                $twigmarkup = $twig->render(
                    $template,
                    [
                        'columns' => $columns,
                        'data' => $data
                    ]
                );
                $renderer->doc .= $twigmarkup;
                $event->preventDefault();
                return;
            } catch (Exception $e) {
            }
        }
    }
}

