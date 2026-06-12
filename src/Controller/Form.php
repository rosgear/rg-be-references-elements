<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\References\Elements\Controller;

use Ge;
use Ge\Mvc\Module\BaseModule;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер формы элемента интерфейса.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\References\Elements\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\Elements\Desk\Extension
     */
    public BaseModule $module;

    /**
     * Возвращает все свойства элементов интерфейса.
     * 
     * @return array
     */
    protected function getSourceConfig(): array
    {
        $config = $tags = [];

        /** @var null|\Rg\Backend\References\Properties\Extension $extension */
        $extension = Ge::$app->extensions->create('rg.be.references.properties');
        if ($extension) {
            /** @var null|\Rg\Backend\References\Properties\Model\Property $properties */
            $properties = $extension->getModel('Property');
            if ($properties) {
                $rows = $properties->fetchAll();
                foreach ($rows as $row) {
                    $property = $row['property'];
                    $config[$property] = [
                        'displayName' => $property,
                        'tooltip'     => $extension->t($row['name'] ?? $property),
                        'type'        => $row['type'] ?? 'string',
                        'editor'      => empty($row['editor']) ? null : json_decode($row['editor'])
                    ];

                    $tags[] = [$property, $property];
                }
            }
        }
        return [$config, $tags];
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        /** @var array $sourceConfig */
        $sourceConfig = $this->getSourceConfig();

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->router->route = $this->module->route('/form');
        $window->form->loadJSONFile('/form', 'items', [
            '@sourceConfig' => $sourceConfig[0],
            '@tags'         => $sourceConfig[1]
            
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 500;
        $window->height = 600;
        $window->responsiveConfig = [
            'height < 600' => ['height' => '99%'],
        ];
        $window->resizable = false;
        $window->layout = 'fit';
        $window
            ->setNamespaceJS('Rg.be.references.elements')
            ->addRequire('Rg.be.references.elements.PropertiesController' . (GE_DEBUG ? '-debug' : ''))
            ->addRequire('Ge.view.form.field.Field')
            ->addRequire('Ge.view.grid.property.Grid');
        return $window;
    }
}
