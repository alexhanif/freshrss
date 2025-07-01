# Extensions

Not every feature is relevant for all users, but some special features are relevant to the right person. That is why FreshRSS is extendable.

There are some "official" extensions (supported and published by the FreshRSS development team and community) and "community" extensions (developed and published individually by third-party developers).

## Extension repositories

Most known extensions are listed in the front end: see configuration menu `Configuration/Extensions`.

## How to install and update

### Recommended: From the Web Interface

The easiest way to install and manage extensions is through the FreshRSS user interface. This method handles downloading, unpacking, and placing the files in the correct location automatically.

1.  Navigate to `Configuration > Extensions`.
2.  Browse the list of "Community Extensions" to find new extensions.

* **To install:** Simply click the `Install` button next to the desired extension in the `Action` column. FreshRSS will handle the download and installation automatically.
* **To update a single extension:** If an update is available for an extension you have already installed, an `Update` button will appear next to it in the `Action` column.
* **To update all extensions:** If any installed extension(s) have an available update, an `Update all` button will be displayed at the top of the "Community Extensions" section. Clicking this will update all outdated extensions in a single step.

### Manual Installation
For extensions that are not in the community list, or if you prefer to install them manually, you can place the extension files directly on your server.

Upload the folder (f.e. `CustomCSS`) of your chosen extension into your `./extensions` directory.

Result: Content of `./extensions/CustomCSS/` has f.e. `extension.php`, `metadata.json`, `configure.php`, `README.md` files and the folders `i18n` and `static`

Important: Do not delete or overwrite the existing files `./extensions/.gitignore` and `./extensions/README.md`.

## How to enable/disable and manage

See in the front end: configuration menu `Configuration/Extensions`

### User extensions

Every user has to manage the extensions by themselves. Configuration via the gear icon is valid only for that user, not for other users.

metadata.json:

```json
{
  "type": "user"
}
```

### System extensions

Only administrators can enable/disable system extensions. The configuration via the gear icon is valid for every user.

metadata.json:

```json
{
  "type": "system"
}
```

### pre installed extensions (core extensions)

See folder: `.lib/core-extensions`

Important: do not install your chosen extensions here!
