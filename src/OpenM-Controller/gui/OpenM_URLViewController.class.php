<?php

Import::php("OpenM-Controller.gui.OpenM_ViewDefaultServer");
Import::php("util.http.OpenM_URL");

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
class OpenM_URLViewController {

    const VIEW = "view";
    const FORM = "form";
    const LANG = "lang";
    const VALUE = "value";
    const URL_REWRITTING_MODE = 1;
    const URL_PARAMETER_MODE = 2;

    private $class;
    private $method;
    private $value;
    private static $root;
    private static $url_mode;
    private static $lang;

    public function __construct($class = null, $method = null, $value = null) {
        if (!String::isStringOrNull($class))
            throw new InvalidArgumentException("class must be a string");
        if (!String::isStringOrNull($method))
            throw new InvalidArgumentException("method must be a string");
        if (!String::isStringOrNull($value))
            throw new InvalidArgumentException("value must be a string");
        $this->class = $class;
        $this->method = $method;
        $this->value = $value;
    }

    public function getClass() {
        return (string) $this->class;
    }

    public function getMethod() {
        return (string) $this->method;
    }

    public function getValue() {
        return (string) $this->value;
    }

    public static function viewFromClass($class) {
        if (!String::isString($class))
            throw new InvalidArgumentException("class must be a string");

        if (!RegExp::preg("/^" . OpenM_ViewDefaultServer::VIEW_PREFIX . "([a-zA-Z0-9]|_)+" . OpenM_ViewDefaultServer::VIEW_SUFFIX . "$/", $class))
            throw new InvalidArgumentException("class must be in a valid format");

        return substr($class, strlen(OpenM_ViewDefaultServer::VIEW_PREFIX), -strlen(OpenM_ViewDefaultServer::VIEW_SUFFIX));
    }

    public static function classFromView($view) {
        return OpenM_ViewDefaultServer::VIEW_PREFIX . $view . OpenM_ViewDefaultServer::VIEW_SUFFIX;
    }

    public static function setLang($lang) {
        if (!String::isString($lang))
            throw new InvalidArgumentException("lang must be a string");

        if (!RegExp::preg("/^[a-z]{2}$/", $lang))
            throw new InvalidArgumentException("lang must be in a valid format ([a-z]{2})");

        self::$lang = $lang;
    }
    
    public static function getLang() {
        return "".self::$lang;
    }

    public static function setRoot($root) {
        if (!String::isString($root))
            throw new InvalidArgumentException("root must be a string");

        if (!(OpenM_URL::isValid($root)) && !(RegExp::preg("/^\//", $root)))
            throw new InvalidArgumentException("url must be in a valid format");

        self::$root = $root;
    }

    public function getURL() {
        if (self::$root == null)
            throw new OpenM_ViewDefaultServerException("url_base not defined");
        switch (self::$url_mode) {
            case self::URL_PARAMETER_MODE :
                return $this->getURL_Parameters();
                break;
            case self::URL_REWRITTING_MODE :
                return $this->getURL_Rewritting();
                break;
            default :
                return $this->getURL_Parameters();
                break;
        }
    }

    private function getURL_Parameters() {
        $return = self::$root;
        if ($this->class != null || self::$lang != null) {
            $return .= "?";
            if ($this->class != null) {
                $return .= self::VIEW . "=" . self::viewFromClass($this->class) . "&";
                if ($this->method != null)
                    $return .= self::FORM . "=" . $this->method . "&";
                if ($this->value != null)
                    $return .= self::VALUE . "=" . $this->value . "&";
            }
            if (self::$lang != null)
                $return .= self::LANG . "=" . self::$lang . "&";

            $return = substr($return, 0, -1);
        }
        return $return;
    }

    private function getURL_Rewritting() {
        throw new OpenM_ViewDefaultServerException("not implemented");
    }

    public static function from($view = null, $form = null, $value = null) {
        return new OpenM_URLViewController($view, $form, $value);
    }

    public static function getRoot() {
        return self::$root;
    }

    public static function getLang() {
        return self::$lang;
    }
}

?>