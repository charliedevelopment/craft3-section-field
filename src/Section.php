<?php
/**
 * Section Field plugin for Craft 3.0
 * @copyright Copyright Charlie Development
 */

namespace charliedev\sectionfield;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;

use charliedev\sectionfield\fields\SectionField;

use yii\base\Event;

/**
 * The main Craft plugin class.
 */
class Section extends Plugin
{

	/**
	 * @inheritdoc
	 * @see craft\base\Plugin
	 */
	public function init()
	{
		parent::init();

		Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, [$this, 'registerFieldTypes']);
	}

	/**
	 * Registers the field type provided by this plugin.
	 * @param RegisterComponentTypesEvent $event The event.
	 * @return void
	 */
	public function registerFieldTypes(RegisterComponentTypesEvent $event)
	{
		$event->types[] = SectionField::class;
	}
}
