<?php

if (!defined("OpenM_SERVICE_CONFIG_FILE_NAME"))
    define("OpenM_SERVICE_CONFIG_FILE_NAME", "config.properties");

if (!Import::php("Smarty"))
    throw new ImportException("Smarty");

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
    protected $resources_dir;
    protected $cache_dir;
    protected $smarty;
    protected $properties;

    public function __construct() {
        $this->properties = Properties::fromFile(self::CONFIG_FILE_NAME);
        if ($this->properties->get(self::LOG_MODE_PROPERTY) == self::LOG_MODE_ACTIVATED)
            OpenM_Log::init($this->properties->get(self::LOG_PATH_PROPERTY), $this->properties->get(self::LOG_LEVEL_PROPERTY), $this->properties->get(self::LOG_FILE_NAME), $this->properties->get(self::LOG_LINE_MAX_SIZE));
        $this->template_c = $this->properties->get(self::SMARTY_TEMPLATE_C_DIR);
        if ($this->template_c == null)
            throw new OpenM_ServiceViewException(self::SMARTY_TEMPLATE_C_DIR . " not defined in config file" . self::CONFIG_FILE_NAME);
        $this->resources_dir = $this->properties->get(self::RESOURCES_DIR);
        if ($this->resources_dir == null)
            throw new OpenM_ServiceViewException(self::RESOURCES_DIR . " not defined in config file" . self::CONFIG_FILE_NAME);
        $this->cache_dir = $this->properties->get(self::SMARTY_CACHE_DIR);
        $this->smarty = new Smarty();
    }

    public abstract function _default();

    protected function _redirect($method, $class = null, $parameters = null) {
        if ($class === null)
            $class = $this->getClass();
        if ($parameters === null)
            OpenM_Header::redirect(OpenM_URLViewController::from($class, $method)->getURL());
        else
            OpenM_Header::redirect(OpenM_URLViewController::from($class, $method, $parameters)->getURL());
    }

    public static function getClass() {
        return get_called_class();
    }

}

?>