<?php
/**
 * Section Field plugin for Craft 3.0
 * @copyright Copyright Charlie Development
 */

namespace charliedev\sectionfield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;

use craft\helpers\Json;

use yii\db\Schema;

/**
 * This field allows a selection from a configured set of sections.
 */
class SectionField extends Field implements PreviewableFieldInterface
{

	/**
	 * @var bool Whether or not the field allows multiple selections.
	 */
	public $allowMultiple = false;

	/**
	 * @var array What sections have been whitelisted as selectable for this field.
	 */
	public $whitelistedSections = [];

	/**
	 * @inheritdoc
	 * @see craft\base\ComponentInterface
	 */
	public static function displayName(): string
	{
		return \Craft::t('section-field', 'Section');
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public static function hasContentColumn(): bool
	{
		return true;
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getContentColumnType(): string
	{
		return Schema::TYPE_STRING;
	}

	/**
	 * @inheritdoc
	 * @see craft\base\SavableComponentInterface
	 */
	public function getSettingsHtml(): string
	{
		return Craft::$app->getView()->renderTemplate(
			'section-field/_settings',
			[
				'field' => $this,
				'sections' => $this->getSections()
			]
		);
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function rules(): array
	{
		$rules = parent::rules();

		$rules[] = [['whitelistedSections'], 'validateSectionWhitelist'];

		return $rules;
	}

	/**
	 * Ensures the section IDs selected for the whitelist are for valid sections.
	 * @param string $attribute The name of the attribute being validated.
	 * @return void
	 */
	public function validateSectionWhitelist(string $attribute) {

		$sections = $this->getSections();

		foreach ($this->whitelistedSections as $section) {
			if (!isset($sections[$section])) {
				$this->addError($attribute, Craft::t('section-field', 'Invalid section selected.'));
			}
		}
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getInputHtml($value, ElementInterface $element = null): string
	{
		$sections = $this->getSections(); // Get all sections available to the current user.
		$whitelist = array_flip($this->whitelistedSections); // Get all whitelisted sections.
		$whitelist[''] = true; // Add a blank entry in, in case the field's options allow a 'None' selection.
		if (!$this->allowMultiple && !$this->required) { // Add a 'None' option specifically for optional, single value fields.
			$sections = array('' => Craft::t('app', 'None')) + $sections;
		}
		$whitelist = array_intersect_key($sections, $whitelist); // Discard any sections not available within the whitelist.

		return Craft::$app->getView()->renderTemplate(
			'section-field/_input', [
				'field' => $this,
				'value' => $value,
				'sections' => $whitelist,
			]
		);
	}

	/**
	 * @inheritdoc
	 * @see craft\base\Field
	 */
	public function getElementValidationRules(): array
	{
		return [
			['validateSections'],
		];
	}

	/**
	 * Ensures the section IDs selected are available to the current user.
	 * @param ElementInterface $element The element with the value being validated.
	 * @return void
	 */
	public function validateSections(ElementInterface $element)
	{
		$value = $element->getFieldValue($this->handle);

		if (!is_array($value)) {
			$value = [$value];
		}

		$sections = $this->getSections();

		foreach ($value as $section) {
			if (!isset($sections[$section])) {
				$element->addError($this->handle, Craft::t('section-field', 'Invalid section selected.'));
			}
		}
	}

	public function normalizeValue($value, ElementInterface $element = null)
	{
		// Convert string representation from db into plain array/int.
		if (is_string($value)) {
			$value = Json::decodeIfJson($value);
		}

		if (is_int($value)
			&& $this->allowMultiple) {
			// Int, but field allows multiple, convert to array.
			$value = [$value];
		} else if (is_array($value)
			&& !$this->allowMultiple
			&& count($value) == 1) {
			// Array, but field allows only one, if single value, convert.
			$value = intval($value[0]);
		}

		// Convert string IDs to integers (for pre 1.1.0 data).
		if (is_array($value)) {
			foreach ($value as $key => $id) {
				$value[$key] = intval($id);
			}
		}

		return $value;
	}

	public function serializeValue($value, ElementInterface $element = null)
	{
		// Convert string IDs to integers for storage.
		if (is_array($value)) {
			foreach ($value as $key => $id) {
				$value[$key] = intval($id);
			}
		}

		return Json::encode($value);
	}

	/**
	 * Retrieves all sections in an id-name pair, suitable for the underlying options display.
	 */
	private function getSections() {
		$sections = array();
		foreach (Craft::$app->getSections()->getEditableSections() as $section) {
			$sections[$section->id] = Craft::t('site', $section->name);
		}
		return $sections;
	}
}
