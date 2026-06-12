<?php
/**
 * Расширение модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\References\Elements;

/**
 * Расширение "Элементы интерфейса".
 * 
 * Элементы интерфейса Панели управления.
 * 
 * Расширение принадлежит модулю "Справочники".
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\References\Elements
 * @since 1.0
 */
class Extension extends \Ge\Panel\Extension\Extension
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'rg.be.references.elements';

    /**
     * {@inheritdoc}
     */
    public string $defaultController = 'grid';

    /**
     * Возвращает значок по умолчанию для элемента.
     * 
     * @return string
     */
    public function getNoneIcon(): string
    {
        return $this->getAssetsUrl() . '/images/elements/none.svg';
    }
}