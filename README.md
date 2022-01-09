# friendica-object-storage
Add-on for Friendica to use S3 object storage.

## Installation
- Copy the `objectstorage` folder to the `addon` folder of your Friendica installation.
- In the `objectstorage/addon` folder, run `composer install --no-dev`.
- In Friendica, as an administrator, activate the add-on. 
- On the storage selection page, select "Object Storage" and fill in the settings.
- Click `Save and Use storage backend`
- Optionally look at `Configuration > Site > Performance > Cache contact avatars` and
  decide whether you want these remote contact avatars included in your storage.

## Status
This is work-in-progress. Only tested very minimally against Amazon AWS S3.
