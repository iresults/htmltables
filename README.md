![Static Badge](https://img.shields.io/badge/11-%23878787?style=for-the-badge&logo=typo3&label=TYPO3&link=https%3A%2F%2Fget.typo3.org%2Fversion%2F11) ![Static Badge](https://img.shields.io/badge/12-%23FF8700?style=for-the-badge&logo=typo3&label=TYPO3&link=https%3A%2F%2Fget.typo3.org%2Fversion%2F12) ![Static Badge](https://img.shields.io/badge/13-%23FF8700?style=for-the-badge&logo=typo3&label=TYPO3&link=https%3A%2F%2Fget.typo3.org%2Fversion%2F13) ![Static Badge](https://img.shields.io/badge/14-%23FF8700?style=for-the-badge&logo=typo3&label=TYPO3&link=https%3A%2F%2Fget.typo3.org%2Fversion%2F14)

TYPO3 extension ``htmltables``
========================================

This extension adds a new content element type to create sophisticated HTML tables by Inline-Relational-Record-Editing (IRRE/Inline)

- Repository:  https://github.com/zarthwork/htmltables
- Issues:      https://github.com/zarthwork/htmltables/issues
- Read online: https://github.com/zarthwork/htmltables
- TER:         https://extensions.typo3.org/extension/htmltables/

## »HTML Tables« in a Nutshell

This extension is designed to easily create HTML tables with the necessary syntax and the most important attributes, using "Inline Relational Record Elements". You can use it as a base for your Web Accessible tables.

## Latest features

### Version 2.0.1
- [TASK] code cleanup and enhancements
- [BUGFIX] add missing return types (thanks to cundd)
- [TASK] dropped support for v11

### Version 2.0.0
- [TASK] TYPO3 v14 compatibility
- Drops support for v11

### Version 1.0.0
- Please check the database for new fields when updating from v0.9
- Updated images in Readme
- !!! [FEATURE] expand the table into a fullscreen bootstrap modal. New Template structure!
- [FEATURE] Set "class" for rows
- Several enhancements and fixes

[...]

## Installation

```bash
composer req zarthwork/htmltables
```

Don't forget to include the required typoscript into your template!

```
 + CType: HtmlTable (htmltables)
```

## Screenshots

### Frontend

![Frontend view](https://raw.githubusercontent.com/zarthwork/htmltables/master/Documentation/Images/frontend-example-w3c.png)

### Backend

**Backend - IRRE**

![Backend view](https://raw.githubusercontent.com/zarthwork/htmltables/master/Documentation/Images/backend-example_4.png)

**Backend - Page preview**

![Backend preview](https://raw.githubusercontent.com/zarthwork/htmltables/master/Documentation/Images/backend-preview.png)

**Backend - Element wizard**

![Backend wizard](https://raw.githubusercontent.com/zarthwork/htmltables/master/Documentation/Images/backend-wizard.png)

**Backend - Extension configuration**

![Backend extension conf](https://raw.githubusercontent.com/zarthwork/htmltables/master/Documentation/Images/system_extconf.png)

## Changelog

### Version 0.9.13
- [FEATURE] Add extension setting "useBootstrapTableClass". Uses "table" class initially – instead of "ce-table"
– Several visual enhancements in the backend

### Version 0.9.12
- [Bugfix] BE Preview: Order Cell Data by "sorting" (thanks to mamoun-al-smaiel)

### Version 0.9.11
- [Bugfix] Undefined array keys (thanks to MuellerNewMedia)

### Version 0.9.10
- Render backend preview (version 12 & 13)

### Version 0.9.9
- Modified readme
- Fixed some compatibility issues with version 11
- Added translation feature
- Some backend fixes

### Version 0.9.8
- Show record uids in the rows of the BE form
- Fix rendering of record elements
- Fix tbody rendering

### Version 0.9.7
- Raised compatibility to version 13
- You can now use record elements in addition to RTE bodytexts
- Extension configuration to switch to responsive table mode
- New extension icon
- You'll see a brief cell information in the BE rows

## License

In general the TYPO3 core is released under the GNU General Public License version
2 or any later version (`GPL-2.0-or-later`). In order to avoid licensing issues and
incompatibilities this package is licenced under the MIT License. In case  you
duplicate or modify source code, credits are not required but really appreciated.

## Security Contact

In case of finding additional security issues in the TYPO3 project or in this package in particular,
please get in touch with the [TYPO3 Security Team](mailto:security@typo3.org), or directly
report a vulnerability via GitHub.
