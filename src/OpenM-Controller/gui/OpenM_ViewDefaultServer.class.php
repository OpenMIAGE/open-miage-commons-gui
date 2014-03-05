<?php

Import::php("OpenM-Services.gui.OpenM_ServiceView");
Import::php("OpenM-Controller.gui.OpenM_ViewDefaultServerException");
Import::php("OpenM-Controller.gui.OpenM_URLViewController");
Import::php("util.Properties");
Import::php("util.wrapper.RegExp");
Import::php("util.OpenM_Log");

/**
 * @copyright (c) 2013, www.open-miage.org
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
class OpenM_ViewDefaultServer extends OpenM_ServiceView {

    const DEFAULT_PAGE = "OpenM_ViewDefaultServer.default.page";
    const DEFAULT_LANG = "OpenM_ViewDefaultServer.default.lang";
    const DEFAULT_ERROR_404 = "OpenM_ViewDefaultServer.default.error.404";
    const ROOT = "OpenM_ViewDefaultServer.root";
    const VIEW_PREFIX = "OpenM_";
    const VIEW_SUFFIX = "View";
    const VIEW_EXTENTION = ".class.php";

    private static $url;
    private static $error404;

    public function handle() {
        OpenM_Log::debug("launch server", __CLASS__, __METHOD__, __LINE__);
        try {
            $url = $this->init();
        } catch (Exception $exc) {
            die($exc->getMessage());
        }
        OpenM_Log::debug("url found", __CLASS__, __METHOD__, __LINE__);
        $class = $url->getClass();
        OpenM_Log::debug("create view", __CLASS__, __METHOD__, __LINE__);
        $object = new $class();
        OpenM_Log::debug("call form", __CLASS__, __METHOD__, __LINE__);
        $form = $url->getMethod();
        $value = $url->getValue();
        if ($value != "" && $value != null)
            $object->$form($value);
        else
            $object->$form();
    }

    /**
     * 
     * @return OpenM_URLViewController
     * @throws OpenM_ViewDefaultServerException
     */
    private function init() {
        if (self::$url != null)
            return self::$url;

        OpenM_Log::debug("load property file", __CLASS__, __METHOD__, __LINE__);
        $p = Properties::fromFile(self::CONFIG_FILE_NAME);
        OpenM_Log::debug("load root", __CLASS__, __METHOD__, __LINE__);
        $root = $p->get(self::ROOT);
        if ($root == null)
            throw new OpenM_ViewDefaultServerException(self::ROOT . " not defined in " . self::CONFIG_FILE_NAME);

        OpenM_Log::debug("set root path to URL controller", __CLASS__, __METHOD__, __LINE__);
        OpenM_URLViewController::setRoot($root);

        OpenM_Log::debug("recover view from URL", __CLASS__, __METHOD__, __LINE__);
        if (!isset($_GET[OpenM_URLViewController::VIEW])) {
            $page = $p->get(self::DEFAULT_PAGE);
            if ($page == null)
                throw new OpenM_ViewDefaultServerException(self::DEFAULT_PAGE . " not defined in " . self::CONFIG_FILE_NAME);
            $a = explode(".", $page);
            if (isset($a[0]))
                $gui = $a[0];
            if (isset($a[1]))
                $form = $a[1];
            if (isset($a[2]))
                $lang = $a[2];
        }
        else
            $gui = $_GET[OpenM_URLViewController::VIEW];

        OpenM_Log::debug("recover lang from URL", __CLASS__, __METHOD__, __LINE__);
        if (isset($_GET[OpenM_URLViewController::LANG]))
            $lang = $_GET[OpenM_URLViewController::LANG];
        if ($lang != null && $lang != "")
            OpenM_URLViewController::setLang($lang);
        else if ($p->get(self::DEFAULT_LANG) !== null)
            OpenM_URLViewController::setLang($p->get(self::DEFAULT_LANG));

        OpenM_Log::debug("recover error404 from config file", __CLASS__, __METHOD__, __LINE__);
        $error404 = $p->get(self::DEFAULT_ERROR_404);
        if ($error404 == null)
            throw new OpenM_ViewDefaultServerException(self::DEFAULT_ERROR_404 . " not defined in " . self::CONFIG_FILE_NAME);
        $a = explode(".", $error404);
        $error404_class = OpenM_URLViewController::classFromView($a[0]);
        $error404_classFile = $error404_class . self::VIEW_EXTENTION;
        if (!Import::php($error404_classFile))
            throw new OpenM_ViewDefaultServerException("default error 404 manager class not found");

        if (isset($a[1]))
            $error404_form = $a[1];

        OpenM_Log::debug("save 404 error URL", __CLASS__, __METHOD__, __LINE__);
        self::$error404 = new OpenM_URLViewController($error404_class, $error404_form);

        if (!RegExp::preg("/^([a-zA-Z0-9]|_)+$/", $gui))
            return $this->set($error404_class, $error404_form);

        OpenM_Log::debug("recover class from view", __CLASS__, __METHOD__, __LINE__);
        $class = OpenM_URLViewController::classFromView($gui);
        $classFile = $class . self::VIEW_EXTENTION;

        if (!is_file($classFile))
            return $this->set($error404_class, $error404_form);
        if (!Import::php($classFile))
            return $this->set($error404_class, $error404_form);
        if (!class_exists($class))
            return $this->set($error404_class, $error404_form);

        OpenM_Log::debug("recover form from URL", __CLASS__, __METHOD__, __LINE__);
        if (isset($_GET[OpenM_URLViewController::FORM]))
            $form = $_GET[OpenM_URLViewController::FORM];
        else if ($form == null || $form == "")
            $form = self::DEFAULT_FORM;

        if (!RegExp::preg("/^([a-zA-Z0-9]|_)+$/", $form))
            return $this->set($error404_class, $error404_form);
        if (!method_exists($class, $form))
            return $this->set($error404_class, $error404_form);

        OpenM_Log::debug("recover method from form", __CLASS__, __METHOD__, __LINE__);
        return $this->set($class, $form);
    }

    private function set($class, $form, $value = null) {
        OpenM_Log::debug("create URL controller from class, method and parameters", __CLASS__, __METHOD__, __LINE__);
        self::$url = new OpenM_URLViewController($class, $form, $value);
        return self::$url;
    }

    public function _default() {
        die("forbidden method called");
    }

    public function get404() {
        return self::$error404;
    }

}

?>