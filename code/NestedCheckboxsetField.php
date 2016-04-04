<?php

class NestedCheckboxSetField extends CheckboxSetField {

	public function Field($properties = array()) {
		Requirements::css(MODULE_NESTEDCHECKBOXSETFIELD_DIR . '/css/NestedCheckboxSetField.css');

		$properties = array_merge($properties, array(
			'Options' => $this->getOptions()
		));

		return $this->customise($properties)->renderWith($this->getTemplates());
	}

	public function getOptions() {
		$source = $this->source;
		$values = $this->value;
		$items = array();

		// Get values from the join, if available
		if(is_object($this->form)) {
			$record = $this->form->getRecord();
			if(!$values && $record && $record->hasMethod($this->name)) {
				$funcName = $this->name;
				$join = $record->$funcName();
				if($join) {
					foreach($join as $joinItem) {
						$values[] = $joinItem->ID;
					}
				}
			}
		}

		if(is_array($values)) {
			$items = $values;
		}

		if (is_array($source)) {
			unset($source['']);
		}

		if ($source == null) {
			$source = array();
		}

		$options = new ArrayList();

		foreach($source as $sourceGroup => $childSource) {
			$childOptions = new ArrayList();

			foreach ($childSource as $value => $title) {
				$optionItem = new ArrayData(array(
					'ID' => $this->ID() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $value),
					'Class' => $this->extraClass(),
					'Name' => "{$this->name}[{$value}]",
					'Value' => $value,
					'Title' => $title,
					'isChecked' => in_array($value, $items) || in_array($value, $this->defaultItems),
					'isDisabled' => $this->disabled || in_array($value, $this->disabledItems)
				));

				$childOptions->push($optionItem);
			}

			$options->push(new ArrayData(array(
				'Title' => $sourceGroup,
				'ChildOptions' => $childOptions
			)));
		}

		$this->extend('updateGetOptions', $options);

		return $options;
	}

	public function Type() {
		return 'optionset checkboxset nestedcheckboxset';
	}

	public function validate($validator) {
		$values = $this->value;

		if (!$values) {
			return true;
		}

		$sourceArray = array_keys($this->getSourceAsFlatArray());

		if (count(array_diff($values, $sourceArray)) > 0) {
			$validator->validationError(
				$this->name,
				_t(
					'CheckboxSetField.SOURCE_VALIDATION',
					"Please select a value within the list provided. '{value}' is not a valid option",
					array('value' => implode(' and ', array_diff($values, $sourceArray)))
				),
				"validation"
			);

			return false;
		}

		return true;
	}

	private function getSourceAsFlatArray() {
		$flatSource = array();

		foreach ($this->source as $key => $source) {
			$flatSource = $flatSource + $source;
		}

		return $flatSource;
	}

}