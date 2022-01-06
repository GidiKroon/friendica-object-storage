# objectstorage
Add-on for Friendica to use S3 object storage.

After activating this add-on, go to the storage selection page and fill in the settings for "Object Storage". Once you make this the
current storage backend, any new files will be stored in object storage. Friendica provides a script to move all existing files to the current storage backend.

The chosen storage backend stores the following files:
- photos uploaded by a local user, including resized versions
- any other attached files by a local user, e.g. videos
- avatars of local users, including resized versions
- avatars of any known remote users, including resized versions
