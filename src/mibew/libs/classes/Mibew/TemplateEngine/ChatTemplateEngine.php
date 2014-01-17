<?php
/*
 * Copyright 2005-2014 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
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
 */

namespace Mibew\TemplateEngine;

/**
 * Simple template engine for chat templates
 */
class ChatTemplateEngine {

	/**
	 * Regular expression for conditional blocks in templates
	 */
	const IF_REGEXP = "/\\\${(if|ifnot):([\w\.]+)}(.*?)(\\\${else:\\2}.*?)?\\\${endif:\\2}/s";

	/**
	 * Path to teplates relative to MIBEW_FS_ROOT.
	 * @var string
	 */
	protected $stylePath;

	/**
	 * Machine name of the templates style.
	 * @var string
	 */
	protected $styleName;

	/**
	 * Data for the currently rendering template. Unfortunately there is no
	 * another place to store these data for used chat templates logic.
	 * @var array
	 */
	protected $templateData;

	/**
	 * Flatten data for the currently rendering template.
	 * @var array
	 */
	protected $flattenTemplateData;

	/**
	 * Constructs an instance of the template engine.
	 *
	 * @param string $style_path Path to the style relative to MIBEW_FS_ROOT.
	 * @param string $style_name Machine name of the templates style.
	 */
	public function __construct($style_path, $style_name) {
		$this->stylePath = $style_path;
		$this->styleName = $style_name;
	}

	/**
	 * Renders template and returns HTML for it.
	 *
	 * @param string $template_name Name of a template with neither path nor
	 * extension.
	 * @param array $data Data for substitutions.
	 * @return string Rendered HTML markup.
	 */
	public function render($template_name, $data) {
		$this->flattenTemplateData = array_flatten_recursive($data);
		$this->templateData = $data;
		$contents = $this->getTemplateFileContent($template_name);
		return $this->expandText($contents);
	}

	/**
	 * Check if condition form conditional construction is true.
	 *
	 * @param string $condition Condition name. Can be any element name of the
	 * template data array.
	 */
	public function checkCondition($condition) {
		if ($condition == 'errors') {
			return !empty($this->templateData['errors'])
				&& is_array($this->templateData['errors']);
		}
		return !empty($this->flattenTemplateData[$condition]);
	}

	/**
	 * Process conditional construction. This function is a callback for
	 * "preg_replace_callback" function.
	 *
	 * @param array $matches matches passed from "preg_replace_callback"
	 * function.
	 * @return string One of conditional blocks depending of conditional value.
	 */
	public function expandCondition($matches) {
		$value = $this->checkCondition($matches[2]) ^ ($matches[1] != 'if');

		if ($value) {
			return preg_replace_callback(
				self::IF_REGEXP,
				array($this, "expandCondition"),
				$matches[3]
			);
		} else if (isset($matches[4])) {
			return preg_replace_callback(
				self::IF_REGEXP,
				array($this, "expandCondition"),
				substr($matches[4], strpos($matches[4], "}") + 1)
			);
		}

		return "";
	}

	/**
	 * Replace variables in template with its values. This function is a
	 * callback for "preg_replace_callback" function.
	 *
	 * @param array $matches matches passed from "preg_replace_callback"
	 * function.
	 * @return string Value of the variable or empty string if the value was not
	 * passed in with template data.
	 */
	public function expandVar($matches) {
		$prefix = $matches[1];
		$var = $matches[2];

		if (!$prefix) {
			if ($var == 'mibewroot') {
				return MIBEW_WEB_ROOT;
			} elseif ($var == 'tplroot') {
				return MIBEW_WEB_ROOT . "/" . $this->stylePath;
			} elseif ($var == 'styleid') {
				return $this->styleName;
			} elseif ($var == 'pagination') {
				return generate_pagination($this->templateData['pagination']);
			} elseif ($var == 'errors' || $var == 'harderrors') {
				if (
					!empty($this->templateData['errors'])
					&& is_array($this->templateData['errors'])
				) {
					$result = getlocal("$var.header");
					foreach ($this->templateData['errors'] as $e) {
						$result .= getlocal("errors.prefix")
							. $e
							. getlocal("errors.suffix");
					}
					$result .= getlocal("errors.footer");
					return $result;
				}
			}

		} elseif ($prefix == 'msg:' || $prefix == 'msgjs:' || $prefix == 'url:') {
			$message = '';
			if (strpos($var, ",") !== false) {
				$pos = strpos($var, ",");
				$param = substr($var, $pos + 1);
				$var = substr($var, 0, $pos);
				$message = getlocal2($var, array($this->flattenTemplateData[$param]));
			} else {
				$message = getlocal($var);
			}
			if ($prefix == 'msgjs:') {
				return json_encode($message);
			}
			return $message;
		} else if ($prefix == 'form:') {
			return form_value($this->templateData, $var);
		} else if ($prefix == 'page:' || $prefix == 'pagejs:') {
			$message = isset($this->flattenTemplateData[$var])
				? $this->flattenTemplateData[$var]
				: "";
			return ($prefix == 'pagejs:') ? json_encode($message) : $message;
		} else if ($prefix == 'if:' || $prefix == 'else:' || $prefix == 'endif:' || $prefix == 'ifnot:') {
			return "<!-- wrong $prefix:$var -->";
		}

		return "";
	}

	/**
	 * Process "include" control structure. This function is a callback for
	 * "preg_replace_callback" function.
	 *
	 * @param array $matches matches passed from "preg_replace_callback"
	 * function.
	 * @return string Contents of including file
	 */
	public function expandInclude($matches) {
		$template_name = $matches[1];
		return $this->getTemplateFileContent($template_name);
	}

	/**
	 * Converts all control structures to markup.
	 *
	 * @param string $text Source text
	 * @return string Markup with no control structures
	 */
	public function expandText($text) {
		$text = preg_replace_callback(
			"/\\\${include:([\w\.]+)}/",
			array($this, "expandInclude"),
			$text
		);

		$text = preg_replace_callback(
			self::IF_REGEXP,
			array($this, "expandCondition"),
			$text
		);

		return preg_replace_callback(
			"/\\\${(\w+:)?([\w\.,]+)}/",
			array($this, "expandVar"),
			$text
		);
	}

	/**
	 * Loads content of a template file.
	 *
	 * @param string $template_name Name of a template file to load.
	 * @return string Template file's content.
	 *
	 * @throws \RuntimeException If there is no such template file or the file
	 * is not readable.
	 */
	protected function getTemplateFileContent($template_name) {
		$full_file_path = MIBEW_FS_ROOT . '/' . $this->stylePath .
			'/templates/' . $template_name . '.tpl';

		if (!is_readable($full_file_path)) {
			throw new \RuntimeException(
				'Cannot load template file: "' . $full_file_path . '"'
			);
		}

		return file_get_contents($full_file_path);
	}
}

?>