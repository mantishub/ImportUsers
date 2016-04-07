ImportUsers is a MantisBT plugin that enables administrators to import users
from a CSV file.

The support csv format is:

The CSV format should include the following:
- username (mandatory)
- realname (optional)
- email address (optional)
- access level (viewer, reporter, ...etc) - use access levels enum config option for possible values.
- password (empty generates random password)
- protected (mandatory, accepts 1/0 or true/false)
- enabled (mandatory, accepts 1/0 or true/false)

Compatibility

This plugin is compatible with MantisBT Modern UI.
See http://github.com/mantishub/mantisbt

