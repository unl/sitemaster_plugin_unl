# sitemaster_plugin_unl


The main UNL plugin for SiteMaster. This is:

* a metric that checks for UNL specific rules (framework versions, etc)
* a plugin that syncs the registry with UNLcms sites

## Scripts:

### scripts/updateFrameworkVersions.php

This script connects with github to get the latest framework versions for the framework. When we make a major update to the framework (from 4.1 to 5.0 for example), there is often a short period where both frameworks are supported. 

There are two files that the script looks for:
* https://github.com/unl/wdntemplates/blob/develop/VERSION_DEP - the 'dependants version' or the version of the assets (changes much more frequently than the HTML version)
* https://github.com/unl/wdntemplates/blob/develop/VERSION_HTML - the 'HTML version' - changes only when the template html is changed and developers will need to manually make changes to their site to support the new version of the framework.

To support multiple versions of the framework at the same time, edit https://github.com/unl/sitemaster_plugin_unl/blob/master/src/FrameworkVersionHelper.php#L9

### scripts/syncUNLCMSSites.php

This will:
* get a list of sites in the CMS and create/delete registry entries where needed
* add/remove members from sites

### scripts/framework_audit.php

This will create a csv file that lists the framework versions for all sites in the UNL group.

### scripts/create_chancellors_report.php

This will create a csv file that details the implementation progress of all sites. This will need to be manually edited for new versions of the framework to 1) update references to the old version, and 2) clear the progress table so that sites can report their progress to the latest version.


