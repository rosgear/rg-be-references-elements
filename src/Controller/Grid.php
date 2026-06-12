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
use Ge\Panel\Widget\TabGrid;
use Ge\Panel\Helper\ExtGrid;
use Ge\Panel\Helper\HtmlGrid;
use Ge\Panel\Controller\GridController;
use Ge\Panel\Helper\HtmlNavigator as HtmlNav;

/**
 *  Контроллер списка элементов интерфейса.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\References\Elements\Controller
 * @since 1.0
 */
class Grid extends GridController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Rg\Backend\References\Elements\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        /** @var TabGrid $tab Сетка данных (Ge.view.grid.Grid GeJS) */
        $tab = parent::createWidget();

        // столбцы (Ge.view.grid.Grid.columns GeJS)
        $tab->grid->columns = [
            ExtGrid::columnNumberer(),
            ExtGrid::columnAction(),
            [
                'xtype'     => 'templatecolumn',
                'text'      => ExtGrid::columnInfoIcon($this->t('Name')),
                'dataIndex' => 'name',
                'cellTip'   => HtmlGrid::tags([
                    HtmlGrid::header('{nameLo}'),
                    HtmlGrid::fieldLabel($this->t('Name'), '{name}'),
                    HtmlGrid::fieldLabel(
                        $this->module->t('Name') . ' (' . Ge::$app->language->name . ')', 
                        '{nameLo}'
                    ),
                    HtmlGrid::fieldLabel($this->t('Type'), '{type}'),
                    HtmlGrid::fieldLabel($this->t('Form field'), HtmlGrid::tplChecked('isField')),
                    HtmlGrid::fieldLabel($this->t('Enabled'), HtmlGrid::tplChecked('enabled'))
                ]),
                'tpl'       => HtmlGrid::img('{icon}', ['align' => 'absmiddle'], false) . ' {name}',
                'filter'    => ['type' => 'string'],
                'sortable'  => true,
                'width'     => 200
            ],
            [
                'text'      => $this->module->t('Name') . ' (' . Ge::$app->language->name . ')',
                'dataIndex' => 'nameLo',
                'cellTip'   => '{nameLo}',
                'sortable'  => false,
                'width'     => 270
            ],
            [
                'text'      => '#Type',
                'dataIndex' => 'type',
                'cellTip'   => '{type}',
                'filter'    => ['type' => 'string'],
                'sortable'  => true,
                'width'     => 180
            ],
            [
                'xtype'       => 'g-gridcolumn-checker',
                'text'        => ExtGrid::columnIcon('g-icon-m_backend', 'svg'),
                'tooltip'     => '#Interface elements this field',
                'collectData' => ['name'],
                'dataIndex'   => 'isField',
                'filter'    => ['type' => 'boolean']
            ],
            [
                'xtype'       => 'g-gridcolumn-switch',
                'text'        => ExtGrid::columnIcon('g-icon-m_visible', 'svg'),
                'tooltip'     => '#Enabled',
                'collectData' => ['name'],
                'dataIndex'   => 'enabled',
                'filter'    => ['type' => 'boolean']
            ]
        ];

        // панель инструментов (Ge.view.grid.Grid.tbar GeJS)
        $tab->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Добавить"
                        'add' => [
                            'caching' => false
                        ],
                        'delete',
                        'cleanup',
                        '-',
                        'edit',
                        'select',
                        '-',
                        'refresh'
                    ]
                ],
                'columns',
                'search'
            ], [
                'route' =>  Ge::alias('@route')
            ])
        ];

        // контекстное меню записи (Ge.view.grid.Grid.popupMenu GeJS)
        $tab->grid->popupMenu = [
            'items' => [
                [
                    'text'    => '#Edit record',
                    'iconCls' => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handler' => 'loadWidget',
                    'handlerArgs' => [
                        'route'   => $this->module->route('/form/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ]
                ]
            ]
        ];

        // 2-й клик по строке сетки
        $tab->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => $this->module->route('/form/view/{id}')
        ];
        // количество строк в сетке
        $tab->grid->store->pageSize = 50;
        // поле аудита записи
        $tab->grid->logField = 'name';
        // плагины сетки
        $tab->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->grid->bodyCls = 'g-grid_background';

        // панель навигации (Ge.view.navigator.Info GeJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{nameLo}'),
            HtmlNav::fieldLabel($this->t('Name'), '{name}'),
            HtmlNav::fieldLabel(
                $this->module->t('Name') . ' (' . Ge::$app->language->name . ')', 
                '{nameLo}'
            ),
            HtmlNav::fieldLabel($this->t('Type'), '{type}'),
            HtmlNav::fieldLabel(
                ExtGrid::columnIcon('g-icon-m_backend', 'svg') . ' ' . $this->t('Form field'), 
                HtmlNav::tplChecked('isField')
            ),
            HtmlNav::fieldLabel(
                ExtGrid::columnIcon('g-icon-m_visible', 'svg') . ' '  . $this->t('Enabled'), 
                HtmlNav::tplChecked('enabled')
            ),
            HtmlNav::widgetButton(
                $this->t('Edit record'),
                ['route' => $this->module->route('/form/view/{id}'), 'long' => true],
                ['title' => $this->t('Edit record')]
            )
        ]);
        $tab
            ->addCss(GE_DEBUG ? '/grid.css' : '/grid.min.css')
            ->addRequire('Ge.view.grid.column.Switch');
        return $tab;
    }
}
