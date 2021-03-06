<?php
App::uses('FormHelper', 'View/Helper');
/**
 * Twitter Bootstrap Form Helper
 */
class BootstrapFormHelper extends FormHelper {

	/**
	* Build custom input field for Twitter Bootstrap support
	*
	* @param string $fieldName
	* @param array $options
	*
	* @return string
	*/
	public function input($fieldName, $options = array()) {
		$defaults = array(
			'before'	=> '',
			'between'	=> '<div class="controls">',
			'after'		=> '</div>',
			'format'	=> array('before', 'label', 'between', 'input', 'error', 'after'),
			'class'		=> 'span9', // @todo make this dynamic
			'div'		=> array(
				'class' => 'control-group'
			),
			'error'		=> array(
				'attributes' => array(
					'class' => 'help-block error',
					'wrap'	=> 'span'
				)
			),
			'help'		=> '',
			'required'	=> false
		);

		$options = array_merge($defaults, $options);

		if (isset($options['actions'])) {
			$options['after'] .= '<div class="actions">' . join("\n", $options['actions']) . '</div>';
			unset($options['actions']);
		}

		return parent::input($fieldName, $options);
	}

	public function label($fieldName = null, $text = null, $options = array()) {
		$options = $this->addClass($options, 'control-label');

		$s = parent::label($fieldName, $text, $options);
		if (!empty($this->_labelHelpText)) {
			return sprintf('<div class="descr">%s<p>%s</p></div>', $s, $this->_labelHelpText);
		}

		if (!empty($options['help'])) {
			return sprintf('<div class="descr">%s<p>%s</p></div>', $s, $options['help']);
		}

		return sprintf('<div class="descr">%s</div>', $s);
	}


	protected function _inputLabel($fieldName, $label, $options) {
		if (isset($options['help'])) {
			$this->_labelHelpText = $options['help'];
		}

		$label = parent::_inputLabel($fieldName, $label, $options);
		$this->_labelHelpText = '';
		return $label;
	}


	/**
	 * Creates an HTML link, but access the url using method DELETE.
	 * Requires javascript to be enabled in browser.
	 *
	 * This method creates a `<form>` element. So do not use this method inside an existing form.
	 * Instead you should add a submit button using FormHelper::submit()
	 *
	 * ### Options:
	 *
	 * - `data` - Array with key/value to pass in input hidden
	 * - `confirm` - Can be used instead of $confirmMessage.
	 * - Other options is the same of HtmlHelper::link() method.
	 * - The option `onclick` will be replaced.
	 *
	 * @param string $title The content to be wrapped by <a> tags.
	 * @param mixed $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
	 * @param array $options Array of HTML attributes.
	 * @param string $confirmMessage JavaScript confirmation message.
	 * @return string An `<a />` element.
	 * @link http://book.cakephp.org/2.0/en/core-libraries/helpers/form.html#FormHelper::postLink
	 */
	public function deleteLink($title, $url = null, $options = array(), $confirmMessage = false) {
		if (!empty($options['confirm'])) {
			$confirmMessage = $options['confirm'];
			unset($options['confirm']);
		}

		$formName = uniqid('post_');
		$formUrl = $this->url($url);
		$out = $this->Html->useTag('form', $formUrl, array('name' => $formName, 'id' => $formName, 'style' => 'display:none;', 'method' => 'post'));
		$out .= $this->Html->useTag('hidden', '_method', ' value="DELETE"');
		$out .= $this->_csrfField();

		$fields = array();
		if (isset($options['data']) && is_array($options['data'])) {
			foreach ($options['data'] as $key => $value) {
				$fields[$key] = $value;
				$out .= $this->hidden($key, array('value' => $value, 'id' => false));
			}
			unset($options['data']);
		}
		$out .= $this->secure($fields);
		$out .= $this->Html->useTag('formend');

		$url = '#';
		$onClick = 'document.' . $formName . '.submit();';
		if ($confirmMessage) {
			$confirmMessage = str_replace(array("'", '"'), array("\'", '\"'), $confirmMessage);
			$options['onclick'] = "if (confirm('{$confirmMessage}')) { {$onClick} }";
		} else {
			$options['onclick'] = $onClick;
		}
		$options['onclick'] .= ' event.returnValue = false; return false;';

		$out .= $this->Html->link($title, $url, $options);
		return $out;
	}

	/**
	 * Render error messages
	 *
	 * @param string $field
	 * @param mixed $text
	 * @param array $options
	 *
	 * @return string
	 */
	public function error($field, $text = null, $options = array()) {
		// The only way currently to catch Model Relation validation errors :(
		if ($field[0] == ucfirst($field[0])) {
			$field = sprintf('%s.%s', $this->_modelScope, $field);
		}

		return parent::error($field, $text, $options);
	}

	/**
	 * Submit button
	 *
	 * @param string $label
	 *
	 * @return string
	 */
	public function submit($label = null, $options = array()) {
		$defaults = array(
			'div'	=> 'actions',
			'class' => 'btn primary'
		);
		$options = array_merge($defaults, $options);

		return parent::submit($label, $options);
	}
}