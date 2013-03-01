<?php

if (!defined("OpenM_SERVICE_CONFIG_FILE_NAME"))
    define("OpenM_SERVICE_CONFIG_FILE_NAME", "config.properties");

Import::php("util.Properties");
Import::php("OpenM-Services.gui.OpenM_ServiceViewException");

/**
 * @license http://www.apache.org/licenses/LICENSE-2.0 Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * @link http://www.open-miage.org
 * @author Gaël Saunier
 */
abstract class OpenM_ServiceView {

    const CONFIG_FILE_NAME = OpenM_SERVICE_CONFIG_FILE_NAME;
    const SMARTY_TEMPLATE_C_DIR = "Smarty.template_c.dir";
    const SMARTY_RESOURCES_DIR_VAR_NAME = "resources_dir";
    const SMARTY_CACHE_DIR = "Smarty.cache.dir";
    const RESOURCES_DIR = "gui.resources_dir";
    const LOG_MODE_PROPERTY = "OpenM_Log.mode";
    const LOG_LINE_MAX_SIZE = "OpenM_Log.line.max.size";
    const LOG_MODE_ACTIVATED = "ON";
    const LOG_LEVEL_PROPERTY = "OpenM_Log.level";
    const LOG_PATH_PROPERTY = "OpenM_Log.path";
    const LOG_FILE_NAME = "OpenM_Log.file.name";
    const DEFAULT_FORM = "_default";

    protected $configFile;
    protected $template_c;
    protected $ressources_dir;
    protected $cache_dir;
    protected $smarty;

    public function __construct() {
        $p = Properties::fromFile(self::CONFIG_FILE_NAME);
        if ($p->get(self::LOG_MODE_PROPERTY) == self::LOG_MODE_ACTIVATED)
            OpenM_Log::init($p->get(self::LOG_PATH_PROPERTY), $p->get(self::LOG_LEVEL_PROPERTY), $p->get(self::LOG_FILE_NAME), $p->get(self::LOG_LINE_MAX_SIZE));
        $this->template_c = $p->get(self::SMARTY_TEMPLATE_C_DIR);
        if ($this->template_c == null)
            throw new OpenM_ServiceViewException(self::SMARTY_TEMPLATE_C_DIR . " not defined in config file" . self::OPENM_CONFIG_FILE_PATH);
        $this->ressources_dir = $p->get(self::RESOURCES_DIR);
        if ($this->ressources_dir == null)
            throw new OpenM_ServiceViewException(self::RESOURCES_DIR . " not defined in config file" . self::OPENM_CONFIG_FILE_PATH);
        $this->cache_dir = $p->get(self::SMARTY_CACHE_DIR);
        $this->smarty = new Smarty();
    }

    public abstract function _default();

    public static function getClass() {
        return get_called_class();
    }

}

?>