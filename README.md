# friendica-object-storage
Add-on for Friendica to use S3 object storage.

## Installation
- Copy the `objectstorage` folder to the `addon` folder of your Friendica installation.
- In the `objectstorage/addon` folder, run `composer install --no-dev`.
- In Friendica, as an administrator, activate the add-on. 
- On the storage selection page `Configuration > storage`, select `ObjectStorage` and fill in the settings.
  - In your Amazon AWS S3 or compatible service you define a new empty bucket with a globally unique name
    and enter that name here.
  - The bucket permissions should be set to not allow public access, but by your account only.
  - When Friendica runs on AWS EC2, create an IAM policy with access for GetObject, PutObject
    and DeleteObject to this bucket only and add this policy to an EC2 instance role, then set
    that EC2 instance role on your instance.
  - When Friendica doesn't run on AWS EC2, define credentials with bucket access and set these
    in the `~/.aws/credentials`
    [file](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials_profiles.html)
    or [in environment variables](https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_credentials_environment.html).
- Click `Save and Use storage backend`.
- Optionally look at `Configuration > Site > Performance > Cache contact avatars` and
  decide whether you want these remote contact avatars included in your storage.
  
## Status
This is work-in-progress.
- Only tested very minimally against Amazon AWS S3.
- No CDN can be configured.
- No by-pass or redirect on the reverse proxy is possible,
i.e. the media are always loaded by Friendica from the bucket instead of redirecting
the user's browser to the bucket.

Note that the Friendica project itself now provides and S3 storage add-on. It is preferred over
this one. The differences are:
- It uses a third party library instead of Amazon's library
- It requires you to enter your login credentials, while this plugin doesn't even allow you to enter them
