# Changelog

## Unreleased

## 1.1.0 - 2018-08-02

### Added
- Better data normalization when storing/retrieving from db (IDs provided are integers instead of strings, arrays <=> integers if field configuration changes).

### Fixed
- Multiple selections being returned as unparsed JSON strings.

## 1.0.1 - 2018-01-31

### Fixed
- Fixed an issue where fields configured to allow only single-section selections would throw exceptions on validation.

## 1.0.0 - 2018-01-08

The initial release of the Section Field plugin.

### Added
- Section field, allowing content administrators to choose one or more sections from an administrator configurable list.