<?php
/**
 * Plugin Name: Regime
 * Plugin URI: https://github.com/chirontex/regime
 * Description: Плагин для создания форм регистрации и авторизации.
 * Version: 0.4.2
 * Author: Dmitry Shumilin
 * Author URI: mailto://chirontex@yandex.ru
*/
/**
 * @package Regime
 * @version 0.4.2
 * 
 * Copyright 2021, Dmitry Shumilin  (email: chirontex@yandex.ru)

 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see: http://www.gnu.org/licenses/gpl-3.0.html
 */
use Regime\Main;

require_once __DIR__.'/regime-autoload.php';

new Main(
    plugin_dir_path(__FILE__),
    plugin_dir_url(__FILE__)
);
