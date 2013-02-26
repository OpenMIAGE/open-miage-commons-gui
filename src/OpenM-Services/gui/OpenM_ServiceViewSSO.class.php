<?php

Import::php("OpenM-Services.gui.OpenM_ServiceView");
Import::php("OpenM-SSO.client.OpenM_SSOClientPoolSessionManager");
Import::php("util.http.OpenM_Header");

/**
 *
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
abstract class OpenM_ServiceViewSSO extends OpenM_ServiceView {

    const SSO_CONFIG_FILE_PATH = "OpenM_SSO.client.config.path";

    /**
     *
     * @var OpenM_SSOClientSession 
     */
    protected $manager;

    /**
     *
     * @var Properties
     */
    protected $properties;

    public function __construct() {
        parent::__construct();
        $this->properties = Properties::fromFile(self::CONFIG_FILE_NAME);
        $path = $this->properties->get(self::SSO_CONFIG_FILE_PATH);
        if ($path == null)
            throw new OpenM_ServiceViewException(self::SSO_CONFIG_FILE_PATH . " not defined in " . self::CONFIG_FILE_NAME);
        $this->manager = OpenM_SSOClientPoolSessionManager::fromFile($path);
    }

}

?>