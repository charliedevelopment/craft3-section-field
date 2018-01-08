# Section Field for Craft 3

This plugin provides a field type for choosing sections. This allows content administrators to select from available singles, channels, and structures. Entries using this field can then access these selections in their templates.

## Requirements

* Craft CMS 3.0.0-RC1 or above

## Installation

1. Open a terminal and navigate to your project folder:

```bash
cd /path/to/project
```

2. Require the package to download it via Composer:

```bash
composer require charliedev/section-field
```

3. Install the plugin on the `Settings -> Plugins` page in the Craft control panel.

**--- or ---**

3. Install the plugin via the command line:

```bash
./craft install/plugin section-field
```

## Usage

### Creating a Section Field

1. Create a new field in your Craft control panel via the Settings -> Fields panel.
2. Select *Section* as the field type.
3. Choose which sections will be available as options under *Allowed Sections*.
4. Check the *Allow Multiple* checkbox if applicable.
5. Attach the new field to a section.

### Editing a Section Field

The form controls for a section field are generated according to that individual field's configuration. A field is configured with a whitelist of allowed sections to use, and sections available on an entry are a combination of the whitelist and the sections the current user is allowed to edit.

* If only one selection is allowed, the field is a set of radio buttons. If the field is not required, an additional "None" option is provided, and will be selected by default.

* If multiple selections are allowed, the field is a set of checkboxes. If the field is required, at least one box must be checked.

### Templating with a Section Field

In a Twig template, you can retrieve the data from a section field as you would from any other field type. If the field is configured to allow a single selection, it will provide the section ID as an integer. If the field is configured to allow multiple selections, it will provide the section ID(s) as an array.

See the example below, where `mySectionField` is a section field that determines which section(s) to display entries from.

```twig
{% set sections = entry.mySectionField %}

{% set sectionEntries = craft.entries.sectionId(sections) %}

{% for sectionEntry in sectionEntries %}

	{# Display sectionEntry #}

{% endfor %}
```

---

*Built for [Craft CMS](https://craftcms.com/) by [Charlie Development](http://charliedev.com/)*
